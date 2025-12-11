<!-- Sidebar -->
<aside class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full md:translate-x-0" :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
    <div class="h-full px-3 py-4 overflow-y-auto bg-white border-r border-gray-200">
        <!-- Logo -->
        <div class="flex items-center mb-6 px-3">
            <i class="fas fa-wallet text-3xl text-blue-600"></i>
            <span class="ml-3 text-xl font-bold text-gray-800">{{ config('app.name') }}</span>
        </div>
        
        <!-- User Info -->
        <div class="mb-6 p-3 bg-blue-50 rounded-lg">
            <div class="flex items-center">
                @if(auth()->user()->profile_photo)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" alt="Profile" class="w-10 h-10 rounded-full">
                @else
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                @endif
                <div class="ml-3 flex-1">
                    <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-600">{{ ucfirst(auth()->user()->roles->first()->name ?? 'Member') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Navigation Links -->
        <ul class="space-y-2">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-home w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('banks.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('banks.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-university w-5"></i>
                    <span class="ml-3">Bank</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('cards.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('cards.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-credit-card w-5"></i>
                    <span class="ml-3">Kartu</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('transactions.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('transactions.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-exchange-alt w-5"></i>
                    <span class="ml-3">Transaksi</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('transfer.form') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('transfer.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-random w-5"></i>
                    <span class="ml-3">Transfer Saldo</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('categories.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-tags w-5"></i>
                    <span class="ml-3">Kategori</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('reports.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-chart-pie w-5"></i>
                    <span class="ml-3">Laporan</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('api.management') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('api.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-code w-5"></i>
                    <span class="ml-3">API Management</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('profile.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('profile.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-user-cog w-5"></i>
                    <span class="ml-3">Pengaturan Akun</span>
                </a>
            </li>
            
            @can('manage-users')
            <li class="mt-6">
                <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</p>
            </li>
            
            <li>
                <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Manajemen User</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.roles.index') }}" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-blue-50 {{ request()->routeIs('admin.roles.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fas fa-user-shield w-5"></i>
                    <span class="ml-3">Manajemen Role</span>
                </a>
            </li>
            @endcan
            
            <li class="mt-6">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center p-3 text-red-600 rounded-lg hover:bg-red-50">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Logout</span>
                    </a>
                </form>
            </li>
        </ul>
    </div>
</aside>

<!-- Mobile sidebar backdrop -->
<div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-gray-900 bg-opacity-50 md:hidden" x-cloak></div>

<!-- Top Bar for Mobile -->
<div class="md:ml-64 sticky top-0 z-20 bg-white border-b border-gray-200 md:hidden">
    <div class="flex items-center justify-between p-4">
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <span class="text-lg font-bold text-gray-800">{{ config('app.name') }}</span>
        <div class="w-6"></div>
    </div>
</div>
