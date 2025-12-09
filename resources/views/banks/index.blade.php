@extends('layouts.app')

@section('title', 'Bank')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Rekening Bank</h1>
            <p class="text-gray-600 mt-1">Kelola semua rekening bank Anda</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('banks.export') }}" 
               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-file-excel mr-2"></i> Export Excel
            </a>
            @can('create-banks')
            <a href="{{ route('banks.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i> Tambah Bank
            </a>
            @endcan
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Total Saldo</p>
                    <h3 class="text-2xl font-bold">Rp {{ number_format($banks->sum('current_balance'), 0, ',', '.') }}</h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Bank</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $banks->total() }}</h3>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-university text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Bank Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $banks->where('is_active', true)->count() }}</h3>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama bank, nomor rekening..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Bank</label>
                <select name="bank_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="conventional" {{ request('bank_type') == 'conventional' ? 'selected' : '' }}>Konvensional</option>
                    <option value="digital" {{ request('bank_type') == 'digital' ? 'selected' : '' }}>Digital</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Saldo Minimal</label>
                <input type="number" name="min_balance" value="{{ request('min_balance') }}" placeholder="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Saldo Maksimal</label>
                <input type="number" name="max_balance" value="{{ request('max_balance') }}" placeholder="Tidak terbatas" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('banks.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Data Info & Per Page -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Menampilkan <span class="font-semibold">{{ $banks->firstItem() ?? 0 }}</span> sampai <span class="font-semibold">{{ $banks->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $banks->total() }}</span> data
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Per halaman:</label>
                <select onchange="window.location.href='{{ route('banks.index') }}?per_page='+this.value+'&{{ http_build_query(request()->except('per_page')) }}'" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @php
                            $sortBy = request('sort_by', 'created_at');
                            $sortOrder = request('sort_order', 'desc');
                            $nextOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
                        @endphp
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="?sort_by=bank_name&sort_order={{ $sortBy === 'bank_name' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Bank
                                @if($sortBy === 'bank_name')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="?sort_by=account_number&sort_order={{ $sortBy === 'account_number' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Nomor Rekening
                                @if($sortBy === 'account_number')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="?sort_by=bank_type&sort_order={{ $sortBy === 'bank_type' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                                Tipe
                                @if($sortBy === 'bank_type')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cabang
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="?sort_by=current_balance&sort_order={{ $sortBy === 'current_balance' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center justify-end hover:text-gray-700">
                                Saldo
                                @if($sortBy === 'current_balance')
                                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-30"></i>
                                @endif
                            </a>
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($banks as $bank)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($bank->photo)
                                    <img src="{{ asset('storage/' . $bank->photo) }}" 
                                         alt="{{ $bank->bank_name }}"
                                         class="w-12 h-12 rounded-lg object-cover mr-3 border border-gray-200">
                                @else
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-university text-white text-lg"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ $bank->bank_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $bank->account_nickname }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-mono">{{ $bank->account_number }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($bank->bank_type == 'digital')
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-mobile-alt mr-1"></i> Digital
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-building mr-1"></i> Konvensional
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            {{ $bank->branch ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="text-sm font-bold text-gray-900">
                                Rp {{ number_format($bank->current_balance, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2">
                                @can('edit-banks')
                                <a href="{{ route('banks.edit', $bank->id) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('delete-banks')
                                <form action="{{ route('banks.destroy', $bank->id) }}" 
                                      method="POST" 
                                      class="inline" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus bank {{ $bank->account_nickname }}? Semua kartu dan transaksi terkait akan terhapus!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center px-3 py-1.5 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <i class="fas fa-inbox text-6xl mb-4"></i>
                                <p class="text-xl font-semibold text-gray-600 mb-2">Belum Ada Bank Terdaftar</p>
                                <p class="text-sm text-gray-500 mb-4">Tambahkan bank pertama Anda untuk mulai mencatat transaksi keuangan</p>
                                @can('create-banks')
                                <a href="{{ route('banks.create') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i> Tambah Bank Sekarang
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        
        <!-- Pagination -->
        @if($banks->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $banks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
