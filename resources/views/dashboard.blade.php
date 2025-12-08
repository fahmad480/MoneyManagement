@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="text-gray-600 mt-1">Selamat datang kembali, {{ auth()->user()->name }}!</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('transfer.form') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2 transition-all transform hover:scale-105">
                    <i class="fas fa-random"></i>
                    <span class="font-medium">Transfer Saldo</span>
                </a>
                <a href="{{ route('transactions.create') }}" class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-lg shadow flex items-center gap-2 border border-gray-300 transition">
                    <i class="fas fa-plus"></i>
                    <span class="font-medium">Tambah Transaksi</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Total Saldo</p>
                    <h3 class="text-2xl font-bold">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Banks -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Bank</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalBanks }}</h3>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-university text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>

        <!-- Total Cards -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Kartu</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalCards }}</h3>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-credit-card text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- This Month Income -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pemasukan</p>
                    <h3 class="text-xl font-bold text-green-600">Rp {{ number_format($thisMonthIncome, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-arrow-down text-2xl text-green-600"></i>
                </div>
            </div>
        </div>

        <!-- This Month Expense -->
        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pengeluaran</p>
                    <h3 class="text-xl font-bold text-red-600">Rp {{ number_format($thisMonthExpense, 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Bulan ini</p>
                </div>
                <div class="bg-red-100 rounded-full p-3">
                    <i class="fas fa-arrow-up text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Trend Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Tren Bulanan</h2>
                <span class="text-sm text-gray-500">6 Bulan Terakhir</span>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="monthlyTrendChart"></canvas>
            </div>
        </div>

        <!-- Expense by Category Chart -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Pengeluaran per Kategori</h2>
                <span class="text-sm text-gray-500">Bulan Ini</span>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="expenseCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h2>
                <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-700">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentTransactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->transaction_date->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($transaction->category)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                      style="background-color: {{ $transaction->category->color }}20; color: {{ $transaction->category->color }};">
                                    {{ $transaction->category->icon }} {{ $transaction->category->name }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ Str::limit($transaction->description ?? 'Tidak ada deskripsi', 30) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $transaction->bank->account_nickname }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($transaction->type === 'income')
                                <span class="text-green-600">+ Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            @elseif($transaction->type === 'expense')
                                <span class="text-red-600">- Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            @else
                                <span class="text-blue-600"><i class="fas fa-exchange-alt"></i> Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada transaksi</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Monthly Trend Chart
const monthlyTrendCtx = document.getElementById('monthlyTrendChart').getContext('2d');
new Chart(monthlyTrendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyTrend, 'month')) !!},
        datasets: [{
            label: 'Pemasukan',
            data: {!! json_encode(array_column($monthlyTrend, 'income')) !!},
            borderColor: '#10B981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Pengeluaran',
            data: {!! json_encode(array_column($monthlyTrend, 'expense')) !!},
            borderColor: '#EF4444',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Expense by Category Chart
const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
const categoryLabels = [];
const categoryData = [];
const categoryColors = [];

@foreach($expenseByCategory as $item)
    @if($item->category)
        categoryLabels.push('{{ $item->category->name }}');
        categoryData.push({{ $item->total }});
        categoryColors.push('{{ $item->category->color }}');
    @endif
@endforeach

new Chart(expenseCategoryCtx, {
    type: 'doughnut',
    data: {
        labels: categoryLabels,
        datasets: [{
            data: categoryData,
            backgroundColor: categoryColors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += 'Rp ' + context.parsed.toLocaleString('id-ID');
                        
                        // Calculate percentage
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        label += ' (' + percentage + '%)';
                        
                        return label;
                    }
                }
            }
        }
    }
});
</script>
@endpush
