<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Validation\ValidationException;
use Exception;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $query = Bank::where('user_id', auth()->id());
        
        // Filter by bank name
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('bank_name', 'like', '%' . $request->search . '%')
                  ->orWhere('account_nickname', 'like', '%' . $request->search . '%')
                  ->orWhere('account_number', 'like', '%' . $request->search . '%');
            });
        }
        
        // Filter by bank type
        if ($request->filled('bank_type')) {
            $query->where('bank_type', $request->bank_type);
        }
        
        // Filter by balance range
        if ($request->filled('min_balance')) {
            $query->where('current_balance', '>=', $request->min_balance);
        }
        if ($request->filled('max_balance')) {
            $query->where('current_balance', '<=', $request->max_balance);
        }
        
        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 10);
        $banks = $query->paginate($perPage)->appends($request->all());
        
        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_name' => 'required|string|max:255',
                'account_nickname' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',
                'current_balance' => 'required|numeric|min:0',
                'photo' => 'nullable|image|max:2048',
                'branch' => 'nullable|string|max:255',
                'bank_type' => 'required|in:digital,conventional',
                'description' => 'nullable|string',
            ], [
                'bank_name.required' => 'Nama bank wajib diisi',
                'account_nickname.required' => 'Nama panggilan rekening wajib diisi',
                'account_number.required' => 'Nomor rekening wajib diisi',
                'current_balance.required' => 'Saldo awal wajib diisi',
                'current_balance.numeric' => 'Saldo harus berupa angka',
                'current_balance.min' => 'Saldo tidak boleh negatif',
                'bank_type.required' => 'Tipe bank wajib dipilih',
                'photo.image' => 'File harus berupa gambar',
                'photo.max' => 'Ukuran foto maksimal 2MB',
            ]);

            $validated['user_id'] = auth()->id();

            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('bank_photos', 'public');
                $validated['photo'] = $path;
            }

            Bank::create($validated);

            return redirect()->route('banks.index')
                ->with('success', 'Bank berhasil ditambahkan!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $bank = Bank::where('user_id', auth()->id())
                ->findOrFail($id);
            
            return view('banks.edit', compact('bank'));
        } catch (Exception $e) {
            return redirect()->route('banks.index')->with('error', 'Bank tidak ditemukan!');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $bank = Bank::where('user_id', auth()->id())
                ->findOrFail($id);
            
            $validated = $request->validate([
                'bank_name' => 'required|string|max:255',
                'account_nickname' => 'required|string|max:255',
                'account_number' => 'required|string|max:50',
                'current_balance' => 'required|numeric|min:0',
                'photo' => 'nullable|image|max:2048',
                'branch' => 'nullable|string|max:255',
                'bank_type' => 'required|in:digital,conventional',
                'description' => 'nullable|string',
            ]);

            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($bank->photo) {
                    Storage::disk('public')->delete($bank->photo);
                }
                
                $path = $request->file('photo')->store('bank_photos', 'public');
                $validated['photo'] = $path;
            }

            $bank->update($validated);

            return redirect()->route('banks.index')
                ->with('success', 'Bank berhasil diupdate!');
                
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $bank = Bank::where('user_id', auth()->id())
                ->findOrFail($id);
            
            // Delete photo if exists
            if ($bank->photo) {
                Storage::disk('public')->delete($bank->photo);
            }
            
            $bank->delete();

            return redirect()->route('banks.index')
                ->with('success', 'Bank berhasil dihapus!');
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function export()
    {
        try {
            $banks = Bank::where('user_id', auth()->id())->get();
            
            return (new FastExcel($banks))->download('banks_' . date('Y-m-d') . '.xlsx', function ($bank) {
                return [
                    'Nama Bank' => $bank->bank_name,
                    'Nama Panggilan' => $bank->account_nickname,
                    'Nomor Rekening' => $bank->account_number,
                    'Saldo' => $bank->current_balance,
                    'Cabang' => $bank->branch,
                    'Tipe Bank' => $bank->bank_type,
                    'Status' => $bank->is_active ? 'Aktif' : 'Tidak Aktif',
                    'Dibuat' => $bank->created_at->format('d/m/Y H:i'),
                ];
            });
        } catch (Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }
}
