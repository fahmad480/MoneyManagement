@extends('layouts.app')

@section('title', 'API Management')

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div x-data="{ showModal: false, showNewKey: {{ session('new_key') ? 'true' : 'false' }} }">
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">API Management</h1>
                <p class="text-gray-600 mt-1">Kelola API Keys dan monitor penggunaan API Anda</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('api.documentation') }}" class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-lg shadow flex items-center gap-2 border border-gray-300 transition">
                    <i class="fas fa-book"></i>
                    <span class="font-medium">Dokumentasi</span>
                </a>
                <button @click="showModal = true" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 transition-all transform hover:scale-105">
                    <i class="fas fa-plus"></i>
                    <span class="font-medium">Buat API Key Baru</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Alert with New Key -->
    @if(session('new_key'))
    <div x-show="showNewKey" class="bg-green-50 border-l-4 border-green-400 p-6 rounded-lg mb-6" role="alert">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center mb-2">
                    <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                    <h3 class="text-lg font-semibold text-green-900">API Key Berhasil Dibuat!</h3>
                </div>
                <p class="text-green-800 mb-4 ml-9">Simpan API Key ini dengan aman. Key ini hanya ditampilkan sekali dan tidak dapat dilihat lagi.</p>
                <div class="ml-9">
                    <div class="flex items-center bg-white rounded-lg p-3 border border-green-300">
                        <input type="text" id="newApiKey" value="{{ session('new_key') }}" readonly class="flex-1 bg-transparent border-none focus:outline-none text-sm font-mono text-gray-800">
                        <button onclick="copyToClipboard('{{ session('new_key') }}', 'newApiKey')" class="ml-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                </div>
            </div>
            <button @click="showNewKey = false" class="text-green-600 hover:text-green-800 ml-4">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
    </div>
    @endif

    @if(session('success') && !session('new_key'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-50 border-l-4 border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button @click="show = false" class="text-green-600 hover:text-green-800">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Requests -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Total Requests</p>
                    <h3 class="text-3xl font-bold">{{ number_format($totalRequests) }}</h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-server text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Requests -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Hari Ini</p>
                    <h3 class="text-3xl font-bold">{{ number_format($todayRequests) }}</h3>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-calendar-day text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Keys -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">API Keys Aktif</p>
                    <h3 class="text-3xl font-bold">{{ $apiKeys->where('is_active', true)->count() }}</h3>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-key text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Avg Response Time -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm mb-1">Avg Response Time</p>
                    <h3 class="text-3xl font-bold">{{ number_format($avgResponseTime, 0) }}<span class="text-lg">ms</span></h3>
                </div>
                <div class="bg-orange-400 bg-opacity-30 rounded-full p-4">
                    <i class="fas fa-tachometer-alt text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Request Trend (7 Hari Terakhir)</h3>
            <div style="position: relative; height: 250px;">
                <canvas id="requestTrendChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Code Distribution</h3>
            <div style="position: relative; height: 250px;">
                <canvas id="statusCodeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- API Keys List -->
    <div class="bg-white rounded-xl shadow-md mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">API Keys Anda</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Requests</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Last Used</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($apiKeys as $key)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $key->name }}</div>
                            @if($key->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($key->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ substr($key->key, 0, 20) }}...</code>
                                <button onclick="copyToClipboard('{{ $key->key }}', 'key-{{ $key->id }}')" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($key->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-circle text-xs mr-1"></i> Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-circle text-xs mr-1"></i> Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-900 font-semibold">{{ number_format($key->logs_count) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="text-sm text-gray-500">
                                {{ $key->last_used_at ? $key->last_used_at->diffForHumans() : 'Never' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <form action="{{ route('api.keys.toggle', $key->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-600 hover:text-gray-900" title="{{ $key->is_active ? 'Deactivate' : 'Activate' }}">
                                        <i class="fas fa-{{ $key->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('api.keys.destroy', $key->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus API key ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-key text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">Belum ada API key. Buat API key pertama Anda!</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Logs & Endpoint Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Logs -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent API Logs (Last 50)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API Key</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Time (ms)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentLogs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $log->created_at->format('H:i:s') }}</td>
                            <td class="px-4 py-3 text-xs text-gray-700">{{ $log->apiKey->name }}</td>
                            <td class="px-4 py-3">
                                <code class="text-xs text-gray-700">{{ Str::limit($log->endpoint, 35) }}</code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $methodColors = [
                                        'GET' => 'bg-blue-100 text-blue-800',
                                        'POST' => 'bg-green-100 text-green-800',
                                        'PUT' => 'bg-yellow-100 text-yellow-800',
                                        'PATCH' => 'bg-purple-100 text-purple-800',
                                        'DELETE' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $methodColors[$log->method] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $log->method }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusColor = $log->status_code >= 500 ? 'bg-red-100 text-red-800' :
                                                   ($log->status_code >= 400 ? 'bg-yellow-100 text-yellow-800' :
                                                   ($log->status_code >= 300 ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'));
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColor }}">
                                    {{ $log->status_code }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-gray-900 font-mono">{{ number_format($log->response_time, 0) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">No logs found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Endpoint Statistics -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top 10 Endpoints</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Request Count</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Time (ms)</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($endpointStats as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <code class="text-xs text-gray-700">{{ Str::limit($stat->endpoint, 40) }}</code>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                    {{ number_format($stat->count) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-gray-900 font-mono">
                                {{ number_format($stat->avg_response_time, 0) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">No endpoint statistics available.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create API Key Modal (Alpine.js) -->
<div x-show="showModal" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="showModal = false"
             aria-hidden="true"></div>

        <!-- Center modal -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="{{ route('api.keys.store') }}" method="POST">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                Create New API Key
                            </h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="name" 
                                           id="name" 
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                           placeholder="My API Key">
                                    <p class="mt-1 text-xs text-gray-500">A descriptive name for this API key</p>
                                </div>
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description
                                    </label>
                                    <textarea name="description" 
                                              id="description" 
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                              placeholder="What will this key be used for?"></textarea>
                                    <p class="mt-1 text-xs text-gray-500">Optional: What will this key be used for?</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse gap-2">
                    <button type="submit" 
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-2"></i>
                        Create API Key
                    </button>
                    <button type="button" 
                            @click="showModal = false"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Request Trend Chart
    const trendCtx = document.getElementById('requestTrendChart').getContext('2d');
    const trendData = @json($last7Days);
    const trendChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: trendData.map(d => d.date),
            datasets: [{
                label: 'Requests',
                data: trendData.map(d => d.count),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.2)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Status Code Chart
    const statusCtx = document.getElementById('statusCodeChart').getContext('2d');
    const statusData = @json($statusCodeStats);
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: statusData.map(d => d.status_code),
            datasets: [{
                data: statusData.map(d => d.count),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    function copyApiKey() {
        const input = document.getElementById('newApiKey');
        input.select();
        document.execCommand('copy');
        alert('API Key copied to clipboard!');
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            alert('API Key copied to clipboard!');
        });
    }
</script>
@endpush
</div>
@endsection
