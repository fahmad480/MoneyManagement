@extends('layouts.app')

@section('title', 'Tambah Bank')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Tambah Bank Baru</h1>
        <p class="text-gray-600 mt-1">Tambahkan rekening bank untuk mulai mencatat transaksi</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <form action="{{ route('banks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Bank -->
                <div class="md:col-span-2">
                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Bank <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bank_name" id="bank_name" 
                           value="{{ old('bank_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bank_name') border-red-500 @enderror"
                           placeholder="Contoh: Bank BCA"
                           required>
                    @error('bank_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Panggilan -->
                <div class="md:col-span-2">
                    <label for="account_nickname" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Panggilan Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_nickname" id="account_nickname" 
                           value="{{ old('account_nickname') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('account_nickname') border-red-500 @enderror"
                           placeholder="Contoh: BCA Utama, Mandiri Tabungan"
                           required>
                    @error('account_nickname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nomor Rekening -->
                <div>
                    <label for="account_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Rekening <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="account_number" id="account_number" 
                           value="{{ old('account_number') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('account_number') border-red-500 @enderror"
                           placeholder="1234567890"
                           required>
                    @error('account_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Saldo Awal -->
                <div>
                    <label for="current_balance" class="block text-sm font-medium text-gray-700 mb-2">
                        Saldo Awal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" name="current_balance" id="current_balance" 
                               value="{{ old('current_balance', 0) }}"
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('current_balance') border-red-500 @enderror"
                               placeholder="0"
                               min="0"
                               step="0.01"
                               required>
                    </div>
                    @error('current_balance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Bank -->
                <div>
                    <label for="bank_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Bank <span class="text-red-500">*</span>
                    </label>
                    <select name="bank_type" id="bank_type"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('bank_type') border-red-500 @enderror"
                            required>
                        <option value="">Pilih Tipe Bank</option>
                        <option value="digital" {{ old('bank_type') == 'digital' ? 'selected' : '' }}>Digital</option>
                        <option value="conventional" {{ old('bank_type') == 'conventional' ? 'selected' : '' }}>Konvensional</option>
                    </select>
                    @error('bank_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cabang -->
                <div>
                    <label for="branch" class="block text-sm font-medium text-gray-700 mb-2">
                        Cabang Bank
                    </label>
                    <input type="text" name="branch" id="branch" 
                           value="{{ old('branch') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('branch') border-red-500 @enderror"
                           placeholder="Contoh: KCP Jakarta Pusat">
                    @error('branch')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Foto Bank -->
                <div class="md:col-span-2">
                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Bank (Optional)
                    </label>
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="photo" id="photo" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('photo') border-red-500 @enderror"
                                   onchange="previewImage(event)">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
                        </div>
                        <div id="imagePreview" class="hidden">
                            <img id="preview" src="" alt="Preview" class="w-24 h-24 object-cover rounded-lg border-2 border-gray-200">
                        </div>
                    </div>
                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Catatan tambahan tentang rekening ini...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <a href="{{ route('banks.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    <i class="fas fa-times mr-2"></i> Batal
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Simpan Bank
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const input = event.target;
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
