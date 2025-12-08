<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Card;
use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Summary cards
        $totalBalance = Bank::where('user_id', $user->id)->sum('current_balance');
        $totalBanks = Bank::where('user_id', $user->id)->count();
        $totalCards = Card::whereHas('bank', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        
        // This month transactions
        $thisMonthIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');
            
        $thisMonthExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->sum('amount');
        
        // Recent transactions
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with(['bank', 'category', 'card'])
            ->orderBy('transaction_date', 'desc')
            ->limit(10)
            ->get();
        
        // Expense by category (this month)
        $expenseByCategory = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->whereYear('transaction_date', Carbon::now()->year)
            ->with('category')
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->get();
        
        // Monthly trend (last 6 months)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            
            $income = Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');
                
            $expense = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $date->month)
                ->whereYear('transaction_date', $date->year)
                ->sum('amount');
            
            $monthlyTrend[] = [
                'month' => $date->format('M Y'),
                'income' => $income,
                'expense' => $expense,
            ];
        }
        
        return view('dashboard', compact(
            'totalBalance',
            'totalBanks',
            'totalCards',
            'thisMonthIncome',
            'thisMonthExpense',
            'recentTransactions',
            'expenseByCategory',
            'monthlyTrend'
        ));
    }
}
