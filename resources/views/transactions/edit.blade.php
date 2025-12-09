@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('transactions.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Edit Transaksi</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Transaction Type -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Tipe Transaksi <span class="text-red-500">*</span></label>
                        <select name="type" id="transactionType" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('type') border-red-500 @enderror" required>
                            <option value="">Pilih Tipe</option>
                            <option value="income" {{ old('type', $transaction->type) == 'income' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="expense" {{ old('type', $transaction->type) == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                            <option value="transfer" {{ old('type', $transaction->type) == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                        @error('type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bank -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Bank Sumber <span class="text-red-500">*</span></label>
                        <select name="bank_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('bank_id') border-red-500 @enderror" required>
                            <option value="">Pilih Bank</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('bank_id', $transaction->bank_id) == $bank->id ? 'selected' : '' }}>
                                {{ $bank->bank_name }} - {{ $bank->account_nickname }}
                            </option>
                            @endforeach
                        </select>
                        @error('bank_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Card -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Kartu (Opsional)</label>
                        <select name="card_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('card_id') border-red-500 @enderror">
                            <option value="">Tidak Pakai Kartu</option>
                            @foreach($cards as $card)
                            <option value="{{ $card->id }}" {{ old('card_id', $transaction->card_id) == $card->id ? 'selected' : '' }}>
                                {{ $card->card_name }} - {{ $card->bank->bank_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('card_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $transaction->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->icon }} {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Nominal <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror" 
                            placeholder="100000" step="0.01" required>
                        @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Metode Pembayaran <span class="text-red-500">*</span></label>
                        <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('payment_method') border-red-500 @enderror" required>
                            <option value="">Pilih Metode</option>
                            <option value="Cash" {{ old('payment_method', $transaction->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Debit Card" {{ old('payment_method', $transaction->payment_method) == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                            <option value="Credit Card" {{ old('payment_method', $transaction->payment_method) == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Bank Transfer" {{ old('payment_method', $transaction->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="E-Wallet" {{ old('payment_method', $transaction->payment_method) == 'E-Wallet' ? 'selected' : '' }}>E-Wallet</option>
                            <option value="QRIS" {{ old('payment_method', $transaction->payment_method) == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                        </select>
                        @error('payment_method')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Source -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Sumber (untuk Pemasukan)</label>
                        <input type="text" name="source" value="{{ old('source', $transaction->source) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('source') border-red-500 @enderror" 
                            placeholder="Contoh: Gaji, Bonus, dll">
                        @error('source')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- To Bank -->
                    <div id="toBankField" style="display: {{ old('type', $transaction->type) == 'transfer' ? 'block' : 'none' }};">
                        <label class="block text-gray-700 font-medium mb-2">Bank Tujuan <span class="text-red-500">*</span></label>
                        <select name="to_bank_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('to_bank_id') border-red-500 @enderror">
                            <option value="">Pilih Bank Tujuan</option>
                            @foreach($banks as $bank)
                            <option value="{{ $bank->id }}" {{ old('to_bank_id', $transaction->to_bank_id) == $bank->id ? 'selected' : '' }}>
                                {{ $bank->bank_name }} - {{ $bank->account_nickname }}
                            </option>
                            @endforeach
                        </select>
                        @error('to_bank_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Nomor Referensi</label>
                        <input type="text" name="reference_number" value="{{ old('reference_number', $transaction->reference_number) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('reference_number') border-red-500 @enderror" 
                            placeholder="Nomor invoice/referensi">
                        @error('reference_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Date -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Tanggal Transaksi <span class="text-red-500">*</span></label>
                        <input type="datetime-local" name="transaction_date" value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d\TH:i')) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('transaction_date') border-red-500 @enderror" required>
                        @error('transaction_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                        <textarea name="description" rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" 
                            placeholder="Catatan transaksi">{{ old('description', $transaction->description) }}</textarea>
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
                    <a href="{{ route('transactions.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-3 rounded-lg text-center transition">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#transactionType').on('change', function() {
            if ($(this).val() === 'transfer') {
                $('#toBankField').show();
                $('select[name="to_bank_id"]').prop('required', true);
            } else {
                $('#toBankField').hide();
                $('select[name="to_bank_id"]').prop('required', false);
            }
        });
    });
</script>
@endpush
@endsection
