@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Kategori Transaksi</h1>
        <div class="flex gap-2">
            @if($defaultCategoriesCount > 0)
            <a href="{{ route('categories.import-default') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-download"></i>
                Import Default
            </a>
            @endif
            @can('create-categories')
            <a href="{{ route('categories.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
                <i class="fas fa-plus"></i>
                Tambah Kategori
            </a>
            @endcan
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama kategori, deskripsi..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                <select name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Semua Tipe</option>
                    <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="both" {{ request('type') == 'both' ? 'selected' : '' }}>Both</option>
                </select>
            </div>
            <div class="md:col-span-3 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('categories.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Info & Per Page -->
    <div class="bg-white rounded-lg shadow mb-6 px-6 py-4 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            Menampilkan <span class="font-semibold">{{ $categories->firstItem() ?? 0 }}</span> sampai <span class="font-semibold">{{ $categories->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $categories->total() }}</span> data
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-700">Per halaman:</label>
            <select onchange="window.location.href='{{ route('categories.index') }}?per_page='+this.value+'&{{ http_build_query(request()->except('per_page')) }}'" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
        <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white text-xl" 
                        style="background-color: {{ $category->color ?? '#6B7280' }}">
                        {{ $category->icon ?? '📁' }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $category->name }}</h3>
                        <span class="text-sm px-2 py-1 rounded-full inline-block mt-1
                            {{ $category->type == 'income' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $category->type == 'expense' ? 'bg-red-100 text-red-700' : '' }}
                            {{ $category->type == 'both' ? 'bg-blue-100 text-blue-700' : '' }}">
                            {{ ucfirst($category->type) }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-2">
                    @can('edit-categories')
                    <a href="{{ route('categories.edit', $category->id) }}" 
                        class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    @endcan
                    @can('delete-categories')
                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="inline" 
                        onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
            @if($category->description)
            <p class="text-gray-600 text-sm">{{ $category->description }}</p>
            @endif
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <i class="fas fa-folder text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada kategori</h3>
            <p class="text-gray-500 mb-4">Kategori membantu mengorganisir transaksi Anda</p>
            @can('create-categories')
            <a href="{{ route('categories.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kategori Pertama
            </a>
            @endcan
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="mt-6">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
