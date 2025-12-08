<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Bank;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Validation\ValidationException;
use Exception;

class CardController extends Controller
{
    public function index(Request $request)
    {
        $query = Card::whereHas('bank', function($q) {
            $q->where('user_id', auth()->id());
        })->with('bank');
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('card_name', 'like', '%' . $request->search . '%')
                  ->orWhere('card_number', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by bank
        if ($request->filled('bank_id')) {
            $query->where('bank_id', $request->bank_id);
        }
        
        // Filter by card type
        if ($request->filled('card_type')) {
            $query->where('card_type', $request->card_type);
        }
        
        // Filter by card form
        if ($request->filled('card_form')) {
            $query->where('card_form', $request->card_form);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $cards = $query->paginate($perPage)->appends($request->all());
        
        $banks = \App\Models\Bank::where('user_id', auth()->id())->get();
        
        return view('cards.index', compact('cards', 'banks'));
    }

    public function create()
    {
        $banks = Bank::where('user_id', auth()->id())->get();
        return view('cards.create', compact('banks'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_id' => 'required|exists:banks,id',
                'card_name' => 'required|string|max:255',
                'card_number' => 'nullable|string|max:20',
                'transaction_limit' => 'nullable|numeric|min:0',
                'card_type' => 'required|in:debit,credit',
                'card_form' => 'required|in:physical,virtual',
                'expiry_date' => 'nullable|date',
                'description' => 'nullable|string',
            ]);

            Card::create($validated);

            return redirect()->route('cards.index')
                ->with('success', 'Kartu berhasil ditambahkan!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $card = Card::whereHas('bank', function($query) {
                $query->where('user_id', auth()->id());
            })->findOrFail($id);
            
            $banks = Bank::where('user_id', auth()->id())->get();
            return view('cards.edit', compact('card', 'banks'));
        } catch (Exception $e) {
            return redirect()->route('cards.index')->with('error', 'Kartu tidak ditemukan!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $card = Card::whereHas('bank', function($query) {
                $query->where('user_id', auth()->id());
            })->findOrFail($id);
            
            $validated = $request->validate([
                'bank_id' => 'required|exists:banks,id',
                'card_name' => 'required|string|max:255',
                'card_number' => 'nullable|string|max:20',
                'transaction_limit' => 'nullable|numeric|min:0',
                'card_type' => 'required|in:debit,credit',
                'card_form' => 'required|in:physical,virtual',
                'expiry_date' => 'nullable|date',
                'description' => 'nullable|string',
            ]);

            $card->update($validated);

            return redirect()->route('cards.index')
                ->with('success', 'Kartu berhasil diupdate!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $card = Card::whereHas('bank', function($query) {
                $query->where('user_id', auth()->id());
            })->findOrFail($id);
            
            $card->delete();

            return redirect()->route('cards.index')
                ->with('success', 'Kartu berhasil dihapus!');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            $cards = Card::whereHas('bank', function($query) {
                $query->where('user_id', auth()->id());
            })->with('bank')->get();
            
            return (new FastExcel($cards))->download('cards_' . date('Y-m-d') . '.xlsx', function ($card) {
                return [
                    'Bank' => $card->bank->bank_name,
                    'Nama Kartu' => $card->card_name,
                    'Nomor Kartu' => $card->card_number,
                    'Tipe' => $card->card_type,
                    'Bentuk' => $card->card_form,
                    'Limit' => $card->transaction_limit,
                    'Tanggal Kadaluarsa' => $card->expiry_date ? $card->expiry_date->format('d/m/Y') : '',
                    'Status' => $card->is_active ? 'Aktif' : 'Tidak Aktif',
                ];
            });
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
}
