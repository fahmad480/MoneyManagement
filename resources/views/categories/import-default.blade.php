@extends('layouts.app')

@section('title', 'Import Kategori Default')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Import Kategori Default</h1>
            <p class="text-gray-600 mt-2">Pilih kategori default yang ingin Anda import ke akun Anda</p>
        </div>
        <a href="{{ route('categories.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if($defaultCategories->isEmpty())
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada kategori default</h3>
        <p class="text-gray-500">Saat ini tidak ada kategori default yang tersedia untuk diimport.</p>
    </div>
    @else
    <form action="{{ route('categories.import-default.store') }}" method="POST">
        @csrf
        
        <!-- Select All -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" id="select-all" class="w-4 h-4 text-blue-600 focus:ring-blue-500 rounded">
                <span class="font-medium text-gray-700">Pilih Semua Kategori</span>
            </label>
        </div>

        <!-- Categories by Type -->
        @php
            $groupedCategories = $defaultCategories->groupBy('type');
            $typeColors = [
                'income' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700'],
                'expense' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700'],
                'both' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700'],
            ];
        @endphp

        @foreach($groupedCategories as $type => $categories)
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="px-3 py-1 rounded-full text-sm
                    {{ $type == 'income' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $type == 'expense' ? 'bg-red-100 text-red-700' : '' }}
                    {{ $type == 'both' ? 'bg-blue-100 text-blue-700' : '' }}">
                    {{ ucfirst($type) }}
                </span>
                <span class="text-gray-500 text-base">({{ $categories->count() }} kategori)</span>
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow p-4 hover:shadow-md transition border-2 {{ $typeColors[$type]['border'] ?? 'border-gray-200' }}">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" 
                            name="category_ids[]" 
                            value="{{ $category->id }}" 
                            class="w-5 h-5 text-blue-600 focus:ring-blue-500 rounded mt-1 category-checkbox">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-lg" 
                                    style="background-color: {{ $category->color ?? '#6B7280' }}">
                                    {{ $category->icon ?? '📁' }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-800">{{ $category->name }}</h3>
                                </div>
                            </div>
                            @if($category->description)
                            <p class="text-gray-600 text-sm">{{ $category->description }}</p>
                            @endif
                        </div>
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <!-- Submit Button -->
        <div class="bg-white rounded-lg shadow p-6 sticky bottom-0">
            <div class="flex justify-between items-center">
                <div class="text-gray-600">
                    <span id="selected-count">0</span> kategori dipilih
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('categories.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Batal
                    </a>
                    <button type="submit" 
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                        id="import-button"
                        disabled>
                        <i class="fas fa-download mr-2"></i>
                        Import Kategori
                    </button>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        const selectedCountSpan = document.getElementById('selected-count');
        const importButton = document.getElementById('import-button');

        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
            selectedCountSpan.textContent = checkedCount;
            importButton.disabled = checkedCount === 0;
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                categoryCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateSelectedCount();
            });
        }

        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectedCount();
                
                // Update select all checkbox
                if (selectAllCheckbox) {
                    const allChecked = Array.from(categoryCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(categoryCheckboxes).some(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            });
        });

        updateSelectedCount();
    });
</script>
@endpush
@endsection
