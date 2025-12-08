<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;
use Exception;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
            
            // Summary
            $totalIncome = Transaction::where('user_id', auth()->id())
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
                
            $totalExpense = Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->sum('amount');
            
            // By Category
            $expenseByCategory = Transaction::where('user_id', auth()->id())
                ->where('type', 'expense')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->with('category')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->orderBy('total', 'desc')
                ->get();
            
            $incomeByCategory = Transaction::where('user_id', auth()->id())
                ->where('type', 'income')
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->with('category')
                ->select('category_id', DB::raw('SUM(amount) as total'))
                ->groupBy('category_id')
                ->orderBy('total', 'desc')
                ->get();
            
            return view('reports.index', compact(
                'totalIncome',
                'totalExpense',
                'expenseByCategory',
                'incomeByCategory',
                'startDate',
                'endDate'
            ));
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth());
            
            $transactions = Transaction::where('user_id', auth()->id())
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->with(['bank', 'category', 'card'])
                ->orderBy('transaction_date', 'desc')
                ->get();
            
            return (new FastExcel($transactions))->download('report_' . date('Y-m-d') . '.xlsx', function ($transaction) {
                return [
                    'Tanggal' => $transaction->transaction_date->format('d/m/Y H:i'),
                    'Tipe' => ucfirst($transaction->type),
                    'Kategori' => $transaction->category ? $transaction->category->name : '-',
                    'Bank' => $transaction->bank->account_nickname,
                    'Kartu' => $transaction->card ? $transaction->card->card_name : '-',
                    'Nominal' => $transaction->amount,
                    'Metode Pembayaran' => $transaction->payment_method,
                    'Deskripsi' => $transaction->description,
                ];
            });
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
}
