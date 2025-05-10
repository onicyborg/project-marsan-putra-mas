<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            $totalMember = \App\Models\User::where('role', 'user')->count();

            $pemasukanHariIni = \App\Models\Transaction::whereDate('transaction_time', now())
                ->where('status', 'Success')
                ->sum('gross_amount');

            $totalSuccess = \App\Models\Transaction::where('status', 'Success')->count();

            $totalPending = \App\Models\Transaction::where('status', 'Pending')->count();

            $newMembers = \App\Models\User::where('role', 'user')->orderBy('created_at', 'desc')->take(5)->get();

            return view('admin.dashboard', compact(
                'totalMember',
                'pemasukanHariIni',
                'totalSuccess',
                'totalPending',
                'newMembers'
            ));
        } else {
            $joinDate = auth()->user()->created_at->format('d-m-Y');

            $transaksiHariIni = \App\Models\Transaction::where('user_id', Auth::id())->whereDate('transaction_time', now())
                ->where('status', 'Success')
                ->sum('gross_amount');

            $totalSuccess = \App\Models\Transaction::where('user_id', Auth::id())->where('status', 'Success')->count();

            $totalPending = \App\Models\Transaction::where('status', 'Pending')->count();

            $newMembers = \App\Models\User::where('role', 'user')->orderBy('created_at', 'desc')->take(5)->get();

            return view('user.dashboard', compact(
                'joinDate',
                'transaksiHariIni',
                'totalSuccess',
                'totalPending',
                'newMembers'
            ));
        }
    }


    public function getChartData()
    {
        $user = Auth::user(); // Ambil user yang sedang login

        // Ambil 6 bulan terakhir
        $months = collect(range(0, 5))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        })->reverse();

        // Base query
        $query = DB::table('transactions')
            ->selectRaw("DATE_FORMAT(transaction_time, '%Y-%m') as month, SUM(gross_amount) as total")
            ->where('status', 'Success')
            ->where('transaction_time', '>=', Carbon::now()->subMonths(6)->startOfMonth());

        // Jika user bukan admin, filter berdasarkan user_id
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Eksekusi query dan susun hasil
        $transactions = $query->groupBy('month')
            ->pluck('total', 'month');

        // Susun label dan data untuk chart
        $labels = [];
        $data = [];

        foreach ($months as $month) {
            $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
            $data[] = $transactions->get($month, 0); // default 0 jika tidak ada transaksi
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Sales',
                    'backgroundColor' => '#177dff',
                    'borderColor' => '#177dff',
                    'legendColor' => '#177dff',
                    'borderWidth' => 1,
                    'data' => $data
                ]
            ]
        ]);
    }
}
