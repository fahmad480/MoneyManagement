@extends('layouts.app')

@section('title', 'Manajemen Role')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen Role</h1>
        <a href="{{ route('admin.roles.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Tambah Role
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama role..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="md:col-span-2 flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Info & Per Page -->
    <div class="bg-white rounded-lg shadow mb-6 px-6 py-4 flex justify-between items-center">
        <div class="text-sm text-gray-700">
            Menampilkan <span class="font-semibold">{{ $roles->firstItem() ?? 0 }}</span> sampai <span class="font-semibold">{{ $roles->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $roles->total() }}</span> data
        </div>
        <div class="flex items-center gap-2">
            <label class="text-sm text-gray-700">Per halaman:</label>
            <select onchange="window.location.href='{{ route('admin.roles.index') }}?per_page='+this.value+'&{{ http_build_query(request()->except('per_page')) }}'" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
    </div>

    @php
        $sortBy = request('sort_by', 'name');
        $sortOrder = request('sort_order', 'asc');
        $nextOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
    @endphp

    <!-- Sort Options -->
    <div class="bg-white rounded-lg shadow p-4 mb-6 flex justify-between items-center">
        <div class="flex gap-4">
            <a href="?sort_by=name&sort_order={{ $sortBy === 'name' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" 
               class="flex items-center text-sm {{ $sortBy === 'name' ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                Urutkan Nama
                @if($sortBy === 'name')
                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                @else
                    <i class="fas fa-sort ml-1 opacity-30"></i>
                @endif
            </a>
            <a href="?sort_by=users_count&sort_order={{ $sortBy === 'users_count' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" 
               class="flex items-center text-sm {{ $sortBy === 'users_count' ? 'text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-800' }}">
                Urutkan Jumlah User
                @if($sortBy === 'users_count')
                    <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                @else
                    <i class="fas fa-sort ml-1 opacity-30"></i>
                @endif
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">{{ ucfirst($role->name) }}</h3>
                    <p class="text-sm text-gray-500">{{ $role->users_count }} users</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </a>
                    @if($role->users_count == 0)
                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline" 
                        onsubmit="return confirm('Yakin ingin menghapus role ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            <div class="border-t pt-4">
                <p class="text-sm text-gray-600 font-medium mb-2">Permissions:</p>
                <div class="flex flex-wrap gap-1">
                    @forelse($role->permissions as $permission)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 text-xs rounded">{{ $permission->name }}</span>
                    @empty
                    <span class="text-xs text-gray-500">No permissions</span>
                    @endforelse
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
    <div class="mt-6">
        {{ $roles->links() }}
    </div>
    @endif
</div>
@endsection
