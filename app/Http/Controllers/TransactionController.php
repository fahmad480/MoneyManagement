<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionCharge;
use App\Models\Bank;
use App\Models\Card;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Validation\ValidationException;
use Exception;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('user_id', auth()->id())
            ->with(['bank', 'card', 'category', 'charges']);
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by bank
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        
        // Filter by amount range
        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', $request->min_amount);
        }
        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', $request->max_amount);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'transaction_date');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $transactions = $query->paginate($perPage)->appends($request->all());
        
        // Get filter options
        $banks = \App\Models\Bank::where('user_id', auth()->id())->get();
        $categories = \App\Models\Category::all();
        
        return view('transactions.index', compact('transactions', 'banks', 'categories'));
    }

    public function create()
    {
        $banks = Bank::where('user_id', auth()->id())->get();
        $cards = Card::whereHas('bank', function($query) {
            $query->where('user_id', auth()->id());
        })->with('bank')->get();
        $categories = Category::all();
        
        return view('transactions.create', compact('banks', 'cards', 'categories'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'type' => 'required|in:income,expense,transfer',
                'bank_id' => 'required|exists:banks,id',
                'card_id' => 'nullable|exists:cards,id',
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'source' => 'nullable|string',
                'to_bank_id' => 'nullable|required_if:type,transfer|exists:banks,id',
                'reference_number' => 'nullable|string',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
                'charges' => 'nullable|array',
                'charges.*.charge_type' => 'required_with:charges|string',
                'charges.*.amount' => 'required_with:charges|numeric|min:0',
            ]);
            
            // Create transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'type' => $validated['type'],
                'bank_id' => $validated['bank_id'],
                'card_id' => $validated['card_id'] ?? null,
                'category_id' => $validated['category_id'],
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'source' => $validated['source'] ?? null,
                'to_bank_id' => $validated['to_bank_id'] ?? null,
                'reference_number' => $validated['reference_number'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'] ?? null,
            ]);
            
            // Add charges if any
            if (isset($validated['charges'])) {
                foreach ($validated['charges'] as $charge) {
                    TransactionCharge::create([
                        'transaction_id' => $transaction->id,
                        'charge_type' => $charge['charge_type'],
                        'amount' => $charge['amount'],
                    ]);
                    
                    // Create separate expense transaction for each charge
                    $chargeCategory = Category::firstOrCreate(
                        ['name' => 'Biaya Transaksi'],
                        [
                            'icon' => '💳',
                            'color' => '#EF4444',
                            'type' => 'expense',
                            'description' => 'Biaya transaksi tambahan'
                        ]
                    );
                    
                    Transaction::create([
                        'user_id' => auth()->id(),
                        'type' => 'expense',
                        'bank_id' => $validated['bank_id'],
                        'category_id' => $chargeCategory->id,
                        'amount' => $charge['amount'],
                        'payment_method' => $validated['payment_method'],
                        'reference_number' => 'CHARGE-' . strtoupper(uniqid()),
                        'transaction_date' => $validated['transaction_date'],
                        'description' => 'Biaya ' . $charge['charge_type'] . ' untuk transaksi ' . ($transaction->reference_number ?? 'TXN-' . $transaction->id),
                    ]);
                }
            }
            
            // Update bank balance
            $bank = Bank::find($validated['bank_id']);
            if ($validated['type'] === 'income') {
                $bank->current_balance += $validated['amount'];
            } else if ($validated['type'] === 'expense') {
                $bank->current_balance -= $validated['amount'];
                // Deduct charges
                if (isset($validated['charges'])) {
                    foreach ($validated['charges'] as $charge) {
                        $bank->current_balance -= $charge['amount'];
                    }
                }
            } else if ($validated['type'] === 'transfer') {
                $bank->current_balance -= $validated['amount'];
                // Update destination bank
                $toBank = Bank::find($validated['to_bank_id']);
                $toBank->current_balance += $validated['amount'];
                $toBank->save();
            }
            $bank->save();
            
            DB::commit();
            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil ditambahkan!');
                
        } catch (ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $transaction = Transaction::where('user_id', auth()->id())
                ->with('charges')
                ->findOrFail($id);
            
            $banks = Bank::where('user_id', auth()->id())->get();
            $cards = Card::whereHas('bank', function($query) {
                $query->where('user_id', auth()->id());
            })->with('bank')->get();
            $categories = Category::all();
            
            return view('transactions.edit', compact('transaction', 'banks', 'cards', 'categories'));
        } catch (Exception $e) {
            return redirect()->route('transactions.index')->with('error', 'Transaksi tidak ditemukan!');
        }
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('user_id', auth()->id())
                ->findOrFail($id);
            
            $validated = $request->validate([
                'type' => 'required|in:income,expense,transfer',
                'bank_id' => 'required|exists:banks,id',
                'card_id' => 'nullable|exists:cards,id',
                'category_id' => 'required|exists:categories,id',
                'amount' => 'required|numeric|min:0',
                'payment_method' => 'required|string',
                'source' => 'nullable|string',
                'to_bank_id' => 'nullable|required_if:type,transfer|exists:banks,id',
                'reference_number' => 'nullable|string',
                'transaction_date' => 'required|date',
                'description' => 'nullable|string',
            ]);
            
            // Revert old balance changes
            $oldBank = Bank::find($transaction->bank_id);
            if ($transaction->type === 'income') {
                $oldBank->current_balance -= $transaction->amount;
            } else if ($transaction->type === 'expense') {
                $oldBank->current_balance += $transaction->amount;
                // Revert old charges
                $oldCharges = $transaction->charges()->sum('amount');
                if ($oldCharges > 0) {
                    $oldBank->current_balance += $oldCharges;
                }
            } else if ($transaction->type === 'transfer') {
                $oldBank->current_balance += $transaction->amount;
                $oldToBank = Bank::find($transaction->to_bank_id);
                $oldToBank->current_balance -= $transaction->amount;
                $oldToBank->save();
            }
            $oldBank->save();
            
            // Update transaction
            $transaction->update($validated);
            
            // Apply new balance changes
            $newBank = Bank::find($validated['bank_id']);
            if ($validated['type'] === 'income') {
                $newBank->current_balance += $validated['amount'];
            } else if ($validated['type'] === 'expense') {
                $newBank->current_balance -= $validated['amount'];
                // Apply new charges if any
                if ($request->has('charges')) {
                    foreach ($request->charges as $charge) {
                        $newBank->current_balance -= $charge['amount'];
                    }
                }
            } else if ($validated['type'] === 'transfer') {
                $newBank->current_balance -= $validated['amount'];
                $newToBank = Bank::find($validated['to_bank_id']);
                $newToBank->current_balance += $validated['amount'];
                $newToBank->save();
            }
            $newBank->save();
            
            DB::commit();
            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil diupdate!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $transaction = Transaction::where('user_id', auth()->id())
                ->findOrFail($id);
            
            // Revert balance changes
            $bank = Bank::find($transaction->bank_id);
            if ($transaction->type === 'income') {
                $bank->current_balance -= $transaction->amount;
            } else if ($transaction->type === 'expense') {
                $bank->current_balance += $transaction->amount;
                // Revert charges
                $charges = $transaction->charges()->sum('amount');
                if ($charges > 0) {
                    $bank->current_balance += $charges;
                }
            } else if ($transaction->type === 'transfer') {
                $bank->current_balance += $transaction->amount;
                $toBank = Bank::find($transaction->to_bank_id);
                $toBank->current_balance -= $transaction->amount;
                $toBank->save();
            }
            $bank->save();
            
            // Delete charges first
            $transaction->charges()->delete();
            
            $transaction->delete();
            
            DB::commit();
            return redirect()->route('transactions.index')
                ->with('success', 'Transaksi berhasil dihapus!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function export(Request $request)
    {
        try {
            $query = Transaction::where('user_id', auth()->id())
                ->with(['bank', 'card', 'category', 'charges']);
            
            if ($request->filled('start_date')) {
                $query->whereDate('transaction_date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $query->whereDate('transaction_date', '<=', $request->end_date);
            }
            
            $transactions = $query->orderBy('transaction_date', 'desc')->get();
            
            return (new FastExcel($transactions))->download('transactions_' . date('Y-m-d') . '.xlsx', function ($transaction) {
                return [
                    'Tanggal' => $transaction->transaction_date->format('d/m/Y H:i'),
                    'Tipe' => ucfirst($transaction->type),
                    'Bank' => $transaction->bank->account_nickname,
                    'Kartu' => $transaction->card ? $transaction->card->card_name : '-',
                    'Kategori' => $transaction->category->name,
                    'Nominal' => $transaction->amount,
                    'Metode' => $transaction->payment_method,
                    'Sumber' => $transaction->source ?? '-',
                    'Deskripsi' => $transaction->description ?? '-',
                ];
            });
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
    
    public function showTransferForm()
    {
        try {
            $banks = Bank::where('user_id', auth()->id())->get();
            
            if ($banks->count() < 2) {
                return redirect()->route('banks.index')
                    ->with('error', 'Anda membutuhkan minimal 2 rekening untuk melakukan transfer!');
            }
            
            return view('transfer.form', compact('banks'));
        } catch (Exception $e) {
            return redirect()->route('dashboard')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function processTransfer(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'from_bank_id' => 'required|exists:banks,id|different:to_bank_id',
                'to_bank_id' => 'required|exists:banks,id',
                'amount' => 'required|numeric|min:0.01',
                'has_charge' => 'required|boolean',
                'charge_type' => 'required_if:has_charge,1|nullable|string',
                'charge_amount' => 'required_if:has_charge,1|nullable|numeric|min:0',
                'description' => 'nullable|string',
            ]);
            
            // Verify both banks belong to user
            $fromBank = Bank::where('user_id', auth()->id())
                ->where('id', $validated['from_bank_id'])
                ->firstOrFail();
                
            $toBank = Bank::where('user_id', auth()->id())
                ->where('id', $validated['to_bank_id'])
                ->firstOrFail();
            
            // Check sufficient balance
            $totalDeduction = $validated['amount'];
            if ($validated['has_charge'] && isset($validated['charge_amount'])) {
                $totalDeduction += $validated['charge_amount'];
            }
            
            if ($fromBank->current_balance < $totalDeduction) {
                DB::rollBack();
                return back()->withInput()
                    ->with('error', 'Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($fromBank->current_balance, 0, ',', '.'));
            }
            
            // Find or create transfer category
            $transferCategory = Category::firstOrCreate(
                ['name' => 'Transfer Antar Rekening'],
                [
                    'icon' => '💸',
                    'color' => '#3B82F6',
                    'type' => 'both',
                    'description' => 'Transfer antar rekening sendiri'
                ]
            );
            
            // Create transaction record
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'type' => 'transfer',
                'bank_id' => $validated['from_bank_id'],
                'category_id' => $transferCategory->id,
                'amount' => $validated['amount'],
                'payment_method' => 'Bank Transfer',
                'to_bank_id' => $validated['to_bank_id'],
                'reference_number' => 'TRF-' . strtoupper(uniqid()),
                'transaction_date' => now(),
                'description' => $validated['description'] ?? 'Transfer dari ' . $fromBank->account_nickname . ' ke ' . $toBank->account_nickname,
            ]);
            
            // Add charge if any
            if ($validated['has_charge'] && $validated['charge_amount'] > 0) {
                TransactionCharge::create([
                    'transaction_id' => $transaction->id,
                    'charge_type' => $validated['charge_type'],
                    'amount' => $validated['charge_amount'],
                ]);
                
                // Create separate expense transaction for the charge
                $chargeCategory = Category::firstOrCreate(
                    ['name' => 'Biaya Transfer'],
                    [
                        'icon' => '💳',
                        'color' => '#EF4444',
                        'type' => 'expense',
                        'description' => 'Biaya transaksi transfer'
                    ]
                );
                
                Transaction::create([
                    'user_id' => auth()->id(),
                    'type' => 'expense',
                    'bank_id' => $validated['from_bank_id'],
                    'category_id' => $chargeCategory->id,
                    'amount' => $validated['charge_amount'],
                    'payment_method' => 'Bank Transfer',
                    'reference_number' => 'CHARGE-' . strtoupper(uniqid()),
                    'transaction_date' => now(),
                    'description' => 'Biaya ' . $validated['charge_type'] . ' untuk transfer ' . $transaction->reference_number,
                ]);
            }
            
            // Update balances
            $fromBank->current_balance -= $validated['amount'];
            if ($validated['has_charge'] && $validated['charge_amount'] > 0) {
                $fromBank->current_balance -= $validated['charge_amount'];
            }
            $fromBank->save();
            
            $toBank->current_balance += $validated['amount'];
            $toBank->save();
            
            DB::commit();
            
            return redirect()->route('transfer.form')
                ->with('success', 'Transfer berhasil! Rp ' . number_format($validated['amount'], 0, ',', '.') . ' telah dipindahkan.');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
