<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Bank;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportApiController extends Controller
{
    /**
     * Get summary report.
     */
    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $userId = $request->user_id;

        $income = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $expense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalBalance = Bank::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('current_balance');

        $transactionCount = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Summary report retrieved successfully',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'total_income' => $income,
                'total_expense' => $expense,
                'net_balance' => $income - $expense,
                'total_balance' => $totalBalance,
                'transaction_count' => $transactionCount,
            ]
        ]);
    }

    /**
     * Get report by category.
     */
    public function byCategory(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());
        $type = $request->get('type', 'expense'); // income or expense

        $userId = $request->user_id;

        $report = Transaction::where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->select(
                'categories.id',
                'categories.name',
                'categories.color',
                'categories.icon',
                DB::raw('SUM(transactions.amount) as total_amount'),
                DB::raw('COUNT(transactions.id) as transaction_count')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color', 'categories.icon')
            ->orderBy('total_amount', 'desc')
            ->get();

        $totalAmount = $report->sum('total_amount');

        $reportWithPercentage = $report->map(function ($item) use ($totalAmount) {
            $item->percentage = $totalAmount > 0 ? round(($item->total_amount / $totalAmount) * 100, 2) : 0;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'Category report retrieved successfully',
            'data' => [
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ],
                'type' => $type,
                'total_amount' => $totalAmount,
                'categories' => $reportWithPercentage,
            ]
        ]);
    }

    /**
     * Get monthly trend report.
     */
    public function monthlyTrend(Request $request)
    {
        $months = $request->get('months', 6); // Last 6 months by default
        $userId = $request->user_id;

        $startDate = now()->subMonths($months)->startOfMonth();
        $endDate = now()->endOfMonth();

        $transactions = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as month'),
                'type',
                DB::raw('SUM(amount) as total_amount')
            )
            ->groupBy('month', 'type')
            ->orderBy('month')
            ->get();

        $grouped = $transactions->groupBy('month')->map(function ($items, $month) {
            return [
                'month' => $month,
                'income' => $items->where('type', 'income')->sum('total_amount'),
                'expense' => $items->where('type', 'expense')->sum('total_amount'),
                'net' => $items->where('type', 'income')->sum('total_amount') - 
                        $items->where('type', 'expense')->sum('total_amount'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'message' => 'Monthly trend report retrieved successfully',
            'data' => [
                'period' => [
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'months' => $grouped,
            ]
        ]);
    }

    /**
     * Get report by bank.
     */
    public function byBank(Request $request)
    {
        $userId = $request->user_id;

        $banks = Bank::where('user_id', $userId)
            ->where('is_active', true)
            ->withCount(['transactions as transaction_count'])
            ->get()
            ->map(function ($bank) {
                return [
                    'id' => $bank->id,
                    'bank_name' => $bank->bank_name,
                    'account_nickname' => $bank->account_nickname,
                    'current_balance' => $bank->current_balance,
                    'transaction_count' => $bank->transaction_count,
                ];
            });

        $totalBalance = $banks->sum('current_balance');

        return response()->json([
            'success' => true,
            'message' => 'Bank report retrieved successfully',
            'data' => [
                'total_balance' => $totalBalance,
                'banks' => $banks,
            ]
        ]);
    }
}

