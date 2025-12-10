@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Manajemen User</h1>
        <a href="{{ route('admin.users.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Tambah User
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <form method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, email, telepon..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Role</option>
                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Email</label>
                    <select name="verified" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Semua Status</option>
                        <option value="yes" {{ request('verified') == 'yes' ? 'selected' : '' }}>Terverifikasi</option>
                        <option value="no" {{ request('verified') == 'no' ? 'selected' : '' }}>Belum Verifikasi</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Data Info & Per Page -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="text-sm text-gray-700">
                Menampilkan <span class="font-semibold">{{ $users->firstItem() ?? 0 }}</span> sampai <span class="font-semibold">{{ $users->lastItem() ?? 0 }}</span> dari <span class="font-semibold">{{ $users->total() }}</span> data
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Per halaman:</label>
                <select onchange="window.location.href='{{ route('admin.users.index') }}?per_page='+this.value+'&{{ http_build_query(request()->except('per_page')) }}'" class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    @php
                        $sortBy = request('sort_by', 'created_at');
                        $sortOrder = request('sort_order', 'desc');
                        $nextOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="?sort_by=name&sort_order={{ $sortBy === 'name' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                            User
                            @if($sortBy === 'name')
                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 opacity-30"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                        <a href="?sort_by=email&sort_order={{ $sortBy === 'email' ? $nextOrder : 'asc' }}&{{ http_build_query(request()->except(['sort_by', 'sort_order'])) }}" class="flex items-center hover:text-gray-700">
                            Email
                            @if($sortBy === 'email')
                                <i class="fas fa-sort-{{ $sortOrder === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @else
                                <i class="fas fa-sort ml-1 opacity-30"></i>
                            @endif
                        </a>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                @if($user->profile_photo)
                                <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($user->profile_photo) }}" alt="">
                                @else
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->phone ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $user->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->email_verified_at)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Terverifikasi
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Belum Verifikasi
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @foreach($user->roles as $role)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            {{ $role->name == 'superadmin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $role->name }}
                        </span>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center gap-2">
                            <!-- Toggle Email Verification -->
                            @if($user->id !== auth()->user()->id)
                            <form action="{{ route('admin.users.toggle-verification', $user->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Yakin ingin mengubah status verifikasi email user ini?');">
                                @csrf
                                @method('POST')
                                <button type="submit" class="text-amber-600 hover:text-amber-900" title="{{ $user->email_verified_at ? 'Batalkan Verifikasi' : 'Verifikasi Email' }}">
                                    <i class="fas fa-{{ $user->email_verified_at ? 'user-times' : 'user-check' }}"></i>
                                </button>
                            </form>
                            @endif
                            
                            <!-- Edit -->
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit User">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Delete -->
                            @if($user->id !== auth()->user()->id)
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline" 
                                onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus User">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection
