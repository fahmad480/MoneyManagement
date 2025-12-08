@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Laporan Keuangan</h1>
        <a href="{{ route('reports.export', request()->all()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="fas fa-file-excel"></i>
            Export Laporan
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fas fa-search mr-2"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-arrow-down text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Pemasukan</p>
                    <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-arrow-up text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Total Pengeluaran</p>
                    <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full {{ ($totalIncome - $totalExpense) >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                    <i class="fas fa-wallet text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-gray-500 text-sm">Selisih</p>
                    <p class="text-2xl font-bold {{ ($totalIncome - $totalExpense) >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                        Rp {{ number_format(abs($totalIncome - $totalExpense), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Expense by Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pengeluaran per Kategori</h2>
            @if($expenseByCategory->count() > 0)
            <canvas id="expenseCategoryChart" height="250"></canvas>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-chart-pie text-4xl mb-2"></i>
                <p>Belum ada data pengeluaran</p>
            </div>
            @endif
        </div>

        <!-- Income by Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pemasukan per Kategori</h2>
            @if($incomeByCategory->count() > 0)
            <canvas id="incomeCategoryChart" height="250"></canvas>
            @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-chart-pie text-4xl mb-2"></i>
                <p>Belum ada data pemasukan</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Expense by Category Chart
    @if($expenseByCategory->count() > 0)
    const expenseCategoryCtx = document.getElementById('expenseCategoryChart').getContext('2d');
    const expenseCategoryChart = new Chart(expenseCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($expenseByCategory->pluck('category.name')) !!},
            datasets: [{
                data: {!! json_encode($expenseByCategory->pluck('total')) !!},
                backgroundColor: [
                    '#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6',
                    '#EC4899', '#14B8A6', '#F97316', '#06B6D4', '#6366F1'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif

    // Income by Category Chart
    @if($incomeByCategory->count() > 0)
    const incomeCategoryCtx = document.getElementById('incomeCategoryChart').getContext('2d');
    const incomeCategoryChart = new Chart(incomeCategoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($incomeByCategory->pluck('category.name')) !!},
            datasets: [{
                data: {!! json_encode($incomeByCategory->pluck('total')) !!},
                backgroundColor: [
                    '#10B981', '#3B82F6', '#8B5CF6', '#F59E0B', '#EF4444',
                    '#14B8A6', '#EC4899', '#06B6D4', '#F97316', '#6366F1'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
</script>
@endpush
@endsection
