<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        // Ambil semua transaksi dari database
        $transactions_pending = Transaction::whereIn('status', ['Pending', 'Failed'])->get();
        $transactions_success = Transaction::where('status', 'Success')->get();
        $unsuccessfulTransactions = Transaction::where('status', '!=', 'Success')
            ->where('status', '!=', 'Pending')
            ->where('status', '!=', 'Failed')
            ->get();

        // Tampilkan halaman transaksi dengan data transaksi
        return view('admin.transaction', compact('transactions_pending', 'transactions_success', 'unsuccessfulTransactions'));
    }

    public function store(Request $request)
    {
        // Validasi basic
        $request->validate([
            'client_type' => 'required|in:member,non-member',
            'member_id' => 'required_if:client_type,member|nullable|exists:users,id',
            'customer_name' => 'required_if:client_type,non-member|nullable|string|max:255',
            'services' => 'required|array|min:1',
            'services.*.service_name' => 'required|string|max:255',
            'services.*.price' => 'required|numeric|min:0',
        ]);

        // Gunakan transaksi database untuk memastikan atomisitas
        DB::beginTransaction();
        try {
            // Simpan data utama transaksi
            $transaction = new Transaction();

            if ($request->client_type == 'member') {
                $transaction->user_id = $request->member_id;
                $name = DB::table('users')->where('id', $request->member_id)->value('name');
                $email = DB::table('users')->where('id', $request->member_id)->value('email');
            } else {
                $transaction->customer_name = $request->customer_name;
                $name = $request->customer_name;
                $email = null; // Atau bisa diisi dengan email default
            }

            // Hitung total harga semua layanan
            $grossAmount = 0;
            foreach ($request->services as $service) {
                $grossAmount += $service['price'];
            }

            $transaction->gross_amount = $grossAmount;

            // Set your Merchant Server Key
            \Midtrans\Config::$serverKey = config('midtrans.serverkey');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
            \Midtrans\Config::$isProduction = config('midtrans.isProduction');
            // Set sanitization on (default)
            \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
            // Set 3DS transaction for credit card to true
            \Midtrans\Config::$is3ds = config('midtrans.is3ds');

            $params = array(
                'transaction_details' => array(
                    'order_id' => rand(),
                    'gross_amount' => $grossAmount,
                ),
                'customer_details' => array(
                    'first_name' => $name,
                    'email' => $email,
                )
            );

            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->snap_token = $snapToken;

            $transaction->save();

            // Simpan detail layanan
            foreach ($request->services as $service) {
                $transactionService = new DetailTransaction();
                $transactionService->transaction_id = $transaction->id;
                $transactionService->service_name = $service['service_name'];
                $transactionService->price = $service['price'];
                $transactionService->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Transaction successfully saved.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to save transaction. Error: ' . $e->getMessage());
        }
    }

    public function getDetailTransaction($id)
    {
        try {
            // Cari transaksi
            $transaction = Transaction::findOrFail($id);

            // Ambil detail transaksi yang terkait
            $details = DetailTransaction::where('transaction_id', $transaction->id)->get();

            // Kalau mau langsung return HTML untuk disisipkan ke table expand:
            $html = '<table class="table table-bordered">';
            $html .= '<thead><tr><th>Service Name</th><th>Price</th></tr></thead><tbody>';

            foreach ($details as $detail) {
                $html .= '<tr>';
                $html .= '<td>' . e($detail->service_name) . '</td>';
                $html .= '<td>Rp. ' . number_format($detail->price) . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';

            return response()->json($html);
        } catch (\Exception $e) {
            return response()->json('<div class="text-danger">Failed to load details.</div>', 500);
        }
    }

    public function pay_transaction(Request $request)
    {
        // Validasi input
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'payment_method' => 'required|in:cash,gateway',
            'snap_result' => 'required_if:payment_method,gateway|nullable|string',
        ]);

        // Cari transaksi
        $transaction = Transaction::findOrFail($request->transaction_id);

        if ($request->payment_method == 'cash') {
            $transaction->status = 'Success';
            $transaction->payment_method = 'Cash';
            $transaction->save();
            return redirect()->back()->with('success', 'Transaction successfully updated.');
        } else {
            $transaction->payment_method = 'Transfer';
            $transaction->snap_result = $request->snap_result;

            // Ambil status dari Midtrans response
            $snapResult = json_decode($request->snap_result);
            $midtransStatus = $snapResult->transaction_status ?? 'pending';

            if (in_array($midtransStatus, ['settlement', 'capture'])) {
                $transaction->status = 'Success';
            } elseif ($midtransStatus === 'pending') {
                $transaction->status = 'Pending';
            } else {
                $transaction->status = 'Failed';
            }

            $transaction->save();
            return response()->json([
                'success' => true,
                'snap_token' => $transaction->snap_token,
                'message' => 'Transaction successfully updated.',
            ]);
        }
    }

    public function cancel_transaction($id)
    {
        // Cari transaksi
        $transaction = Transaction::findOrFail($id);

        // Ubah status transaksi menjadi Cancel
        $transaction->status = 'Cancel';
        $transaction->save();

        return redirect()->back()->with('success', 'Transaction successfully canceled.');
    }
}
