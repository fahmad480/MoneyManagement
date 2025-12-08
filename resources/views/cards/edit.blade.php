@extends('layouts.app')

@section('title', 'Edit Kartu')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('cards.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Edit Kartu</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('cards.update', $card->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bank -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Bank <span class="text-red-500">*</span></label>
                        <select name="bank_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bank_id') border-red-500 @enderror" required>
                            <option value="">Pilih Bank</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ (old('bank_id', $card->bank_id) == $bank->id) ? 'selected' : '' }}>
                                {{ $bank->bank_name }} - {{ $bank->account_nickname }}
                            </option>
                            @endforeach
                        </select>
                        @error('bank_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card Name -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Nama Kartu <span class="text-red-500">*</span></label>
                        <input type="text" name="card_name" value="{{ old('card_name', $card->card_name) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('card_name') border-red-500 @enderror" 
                            placeholder="Contoh: BCA Debit - Silver" required>
                        @error('card_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card Number -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Nomor Kartu</label>
                        <input type="text" name="card_number" value="{{ old('card_number', $card->card_number) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('card_number') border-red-500 @enderror" 
                            placeholder="1234 5678 9012 3456" maxlength="20">
                        @error('card_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card Type -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Tipe Kartu <span class="text-red-500">*</span></label>
                        <select name="card_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('card_type') border-red-500 @enderror" required>
                            <option value="">Pilih Tipe</option>
                            <option value="debit" {{ old('card_type', $card->card_type) == 'debit' ? 'selected' : '' }}>Debit</option>
                            <option value="credit" {{ old('card_type', $card->card_type) == 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                        @error('card_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card Form -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Bentuk Kartu <span class="text-red-500">*</span></label>
                        <select name="card_form" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('card_form') border-red-500 @enderror" required>
                            <option value="">Pilih Bentuk</option>
                            <option value="physical" {{ old('card_form', $card->card_form) == 'physical' ? 'selected' : '' }}>Physical</option>
                            <option value="virtual" {{ old('card_form', $card->card_form) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                        </select>
                        @error('card_form')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Limit -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Limit Transaksi</label>
                        <input type="number" name="transaction_limit" value="{{ old('transaction_limit', $card->transaction_limit) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('transaction_limit') border-red-500 @enderror" 
                            placeholder="10000000" step="0.01">
                        @error('transaction_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Tanggal Kadaluarsa</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $card->expiry_date ? $card->expiry_date->format('Y-m-d') : '') }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('expiry_date') border-red-500 @enderror">
                        @error('expiry_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                        <textarea name="description" rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror" 
                            placeholder="Catatan tambahan tentang kartu ini">{{ old('description', $card->description) }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 mt-6">
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>
                        Update
                    </button>
                    <a href="{{ route('cards.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-3 rounded-lg text-center transition">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
