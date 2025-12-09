@extends('layouts.app')

@section('title', 'Transaksi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Transaksi</h1>
        <div class="flex gap-2">
            <a href="{{ route('transactions.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-file-excel"></i>
                Export
            </a>
            <a href="{{ route('transactions.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-plus"></i>
                Tambah Transaksi
            </a>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Deskripsi, nomor referensi..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bank</label>
                <select name="bank_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Bank</option>
                    @foreach($banks as $bank)
                    <option value="{{ $bank->id }}" {{ request('bank_id') == $bank->id ? 'selected' : '' }}>{{ $bank->account_nickname }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Card</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal Min</label>
                <input type="number" name="min_amount" value="{{ request('min_amount') }}" placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nominal Max</label>
                <input type="number" name="max_amount" value="{{ request('max_amount') }}" placeholder="Tidak terbatas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Data Info & Per Page -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Menampilkan <span class="font-semibold">{{ $transactions->firstItem() ?? 0 }}</span> sampai <span class="font-semibold">{{ $transactions->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $transactions->total() }}</span> data
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Per halaman:</label>
                <select onchange="window.location.href='{{ route('transactions.index') }}?per_page='+this.value+'&{{ http_build_query(request()->except('per_page')) }}'" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        
        @if($transactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @php
                            $sortBy = request('sort_by', 'created_at');
                            $sortOrder = request('sort_order', 'desc');
                            $nextOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="?sort_by=transaction_date&sort_order={{ $sortBy === 'transaction_date' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Tanggal
                                @if($sortBy === 'transaction_date')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="?sort_by=type&sort_order={{ $sortBy === 'type' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Tipe
                                @if($sortBy === 'type')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="?sort_by=category_id&sort_order={{ $sortBy === 'category_id' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Kategori
                                @if($sortBy === 'category_id')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bank/Kartu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="?sort_by=amount&sort_order={{ $sortBy === 'amount' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Nominal
                                @if($sortBy === 'amount')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                            <a href="?sort_by=payment_method&sort_order={{ $sortBy === 'payment_method' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Metode
                                @if($sortBy === 'payment_method')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ $transaction->type == 'income' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $transaction->type == 'expense' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $transaction->type == 'transfer' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($transaction->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($transaction->category)
                                <span class="mr-2">{{ $transaction->category->icon }}</span>
                                <span class="text-sm text-gray-900">{{ $transaction->category->name }}</span>
                                @else
                                <span class="text-sm text-gray-500">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="text-gray-900">{{ $transaction->bank->account_nickname }}</div>
                            @if($transaction->card)
                            <div class="text-gray-500 text-xs">{{ $transaction->card->card_name }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                            {{ $transaction->type == 'income' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type == 'income' ? '+' : '-' }} 
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ ucfirst($transaction->payment_method) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ Str::limit($transaction->description ?? '-', 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('transactions.edit', $transaction->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline" 
                                onsubmit="return confirm('Yakin ingin menghapus transaksi ini? Saldo bank akan dikembalikan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $transactions->links() }}
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-exchange-alt text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada transaksi</h3>
            <p class="text-gray-500 mb-4">Mulai catat transaksi keuangan Anda</p>
            <a href="{{ route('transactions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Tambah Transaksi Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
