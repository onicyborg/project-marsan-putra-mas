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
        $transactions_pending = Transaction::where('status', 'Pending')->get();
        $transactions_success = Transaction::where('status', 'Success')->get();
        $unsuccessfulTransactions = Transaction::where('status', '!=', 'Success')->where('status', '!=', 'Pending')->get();

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
            } else {
                $transaction->customer_name = $request->customer_name;
            }

            // Hitung total harga semua layanan
            $grossAmount = 0;
            foreach ($request->services as $service) {
                $grossAmount += $service['price'];
            }

            $transaction->gross_amount = $grossAmount;

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
}
