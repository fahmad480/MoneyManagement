@extends('layouts.app')

@section('title', 'Transfer Antar Rekening')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Transfer Antar Rekening</h1>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- Error Message -->
        @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Transfer Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center mb-4 pb-4 border-b">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <h2 class="text-xl font-bold text-gray-800">Form Transfer</h2>
                            <p class="text-sm text-gray-500">Transfer saldo antar rekening Anda sendiri</p>
                        </div>
                    </div>

                    <form action="{{ route('transfer.process') }}" method="POST" id="transferForm">
                        @csrf
                        
                        <!-- From Bank -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-university mr-2 text-gray-500"></i>
                                Dari Rekening <span class="text-red-500">*</span>
                            </label>
                            <select name="from_bank_id" id="fromBank" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('from_bank_id') border-red-500 @enderror" required>
                                <option value="">Pilih Rekening Sumber</option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" 
                                    data-balance="{{ $bank->current_balance }}"
                                    data-name="{{ $bank->bank_name }} - {{ $bank->account_nickname }}"
                                    {{ old('from_bank_id') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->bank_name }} - {{ $bank->account_nickname }} (Rp {{ number_format($bank->current_balance, 0, ',', '.') }})
                                </option>
                                @endforeach
                            </select>
                            @error('from_bank_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <div id="fromBankBalance" class="mt-2 text-sm text-gray-600 hidden">
                                <i class="fas fa-wallet mr-1"></i>
                                Saldo: <span class="font-semibold" id="fromBalanceAmount"></span>
                            </div>
                        </div>

                        <!-- To Bank -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-university mr-2 text-gray-500"></i>
                                Ke Rekening <span class="text-red-500">*</span>
                            </label>
                            <select name="to_bank_id" id="toBank" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('to_bank_id') border-red-500 @enderror" required>
                                <option value="">Pilih Rekening Tujuan</option>
                                @foreach($banks as $bank)
                                <option value="{{ $bank->id }}" 
                                    data-name="{{ $bank->bank_name }} - {{ $bank->account_nickname }}"
                                    {{ old('to_bank_id') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->bank_name }} - {{ $bank->account_nickname }}
                                </option>
                                @endforeach
                            </select>
                            @error('to_bank_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-money-bill-wave mr-2 text-gray-500"></i>
                                Nominal Transfer <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500 font-medium">Rp</span>
                                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" 
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror" 
                                    placeholder="0" step="0.01" min="0.01" required>
                            </div>
                            @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Minimal transfer Rp 1</p>
                        </div>

                        <!-- Transaction Charge -->
                        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <input type="checkbox" name="has_charge" id="hasCharge" value="1" 
                                    {{ old('has_charge') ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-1">
                                <div class="ml-3 flex-1">
                                    <label for="hasCharge" class="font-medium text-gray-700 cursor-pointer">
                                        <i class="fas fa-coins mr-2 text-amber-600"></i>
                                        Ada Biaya Transaksi
                                    </label>
                                    <p class="text-sm text-gray-600 mt-1">Centang jika ada biaya admin, BI Fast, atau biaya lainnya</p>
                                </div>
                            </div>

                            <div id="chargeFields" class="mt-4 space-y-3 hidden">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Biaya</label>
                                    <select name="charge_type" id="chargeType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                        <option value="">Pilih Tipe Biaya</option>
                                        <option value="BI Fast" {{ old('charge_type') == 'BI Fast' ? 'selected' : '' }}>BI Fast</option>
                                        <option value="Admin Fee" {{ old('charge_type') == 'Admin Fee' ? 'selected' : '' }}>Biaya Admin</option>
                                        <option value="Transfer Fee" {{ old('charge_type') == 'Transfer Fee' ? 'selected' : '' }}>Biaya Transfer</option>
                                        <option value="Other" {{ old('charge_type') == 'Other' ? 'selected' : '' }}>Lainnya</option>
                                    </select>
                                    @error('charge_type')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Biaya</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                                        <input type="number" name="charge_amount" id="chargeAmount" value="{{ old('charge_amount') }}" 
                                            class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                            placeholder="0" step="0.01" min="0">
                                    </div>
                                    @error('charge_amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">
                                <i class="fas fa-comment mr-2 text-gray-500"></i>
                                Catatan (Opsional)
                            </label>
                            <textarea name="description" rows="3" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" 
                                placeholder="Tambahkan catatan untuk transfer ini">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submitBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Transfer Sekarang
                        </button>
                    </form>
                </div>
            </div>

            <!-- Summary Panel -->
            <div class="lg:col-span-1">
                <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow-lg p-6 text-white sticky top-4">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-receipt mr-2"></i>
                        Ringkasan Transfer
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <p class="text-xs text-blue-100 mb-1">Dari</p>
                            <p class="font-semibold" id="summaryFrom">-</p>
                        </div>
                        
                        <div class="text-center">
                            <i class="fas fa-arrow-down text-2xl"></i>
                        </div>
                        
                        <div class="bg-white bg-opacity-10 rounded-lg p-3">
                            <p class="text-xs text-blue-100 mb-1">Ke</p>
                            <p class="font-semibold" id="summaryTo">-</p>
                        </div>
                        
                        <div class="border-t border-blue-400 pt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm">Nominal Transfer</span>
                                <span class="font-semibold" id="summaryAmount">Rp 0</span>
                            </div>
                            <div id="summaryChargeRow" class="flex justify-between mb-2 hidden">
                                <span class="text-sm">Biaya (<span id="summaryChargeType">-</span>)</span>
                                <span class="font-semibold" id="summaryCharge">Rp 0</span>
                            </div>
                            <div class="border-t border-blue-400 pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="font-bold">Total Debet</span>
                                    <span class="font-bold text-xl" id="summaryTotal">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-800 bg-opacity-50 rounded-lg p-3 mt-4">
                            <p class="text-xs text-blue-100 mb-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                Info
                            </p>
                            <p class="text-xs">Transfer akan diproses secara real-time. Pastikan data sudah benar sebelum melanjutkan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Show/hide charge fields
    $('#hasCharge').on('change', function() {
        if ($(this).is(':checked')) {
            $('#chargeFields').removeClass('hidden');
            $('#chargeType').prop('required', true);
            $('#chargeAmount').prop('required', true);
        } else {
            $('#chargeFields').addClass('hidden');
            $('#chargeType').prop('required', false);
            $('#chargeAmount').prop('required', false);
            $('#chargeType').val('');
            $('#chargeAmount').val('');
            updateSummary();
        }
    });

    // Trigger on page load if old value exists
    if ($('#hasCharge').is(':checked')) {
        $('#chargeFields').removeClass('hidden');
        $('#chargeType').prop('required', true);
        $('#chargeAmount').prop('required', true);
    }

    // Update from bank balance display
    $('#fromBank').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const balance = selectedOption.data('balance');
        const name = selectedOption.data('name');
        
        if (balance !== undefined) {
            $('#fromBalanceAmount').text('Rp ' + formatNumber(balance));
            $('#fromBankBalance').removeClass('hidden');
        } else {
            $('#fromBankBalance').addClass('hidden');
        }
        
        if (name) {
            $('#summaryFrom').text(name);
        } else {
            $('#summaryFrom').text('-');
        }
        
        validateTransfer();
    });

    // Update to bank display
    $('#toBank').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        const name = selectedOption.data('name');
        
        if (name) {
            $('#summaryTo').text(name);
        } else {
            $('#summaryTo').text('-');
        }
        
        validateTransfer();
    });

    // Update summary on amount change
    $('#amount, #chargeAmount, #chargeType').on('input change', function() {
        updateSummary();
    });

    function updateSummary() {
        const amount = parseFloat($('#amount').val()) || 0;
        const chargeAmount = parseFloat($('#chargeAmount').val()) || 0;
        const chargeType = $('#chargeType option:selected').text();
        const hasCharge = $('#hasCharge').is(':checked');
        
        $('#summaryAmount').text('Rp ' + formatNumber(amount));
        
        if (hasCharge && chargeAmount > 0) {
            $('#summaryChargeRow').removeClass('hidden');
            $('#summaryCharge').text('Rp ' + formatNumber(chargeAmount));
            $('#summaryChargeType').text(chargeType || '-');
        } else {
            $('#summaryChargeRow').addClass('hidden');
        }
        
        const total = amount + (hasCharge ? chargeAmount : 0);
        $('#summaryTotal').text('Rp ' + formatNumber(total));
        
        validateTransfer();
    }

    function validateTransfer() {
        const fromBank = $('#fromBank').val();
        const toBank = $('#toBank').val();
        const amount = parseFloat($('#amount').val()) || 0;
        const balance = parseFloat($('#fromBank option:selected').data('balance')) || 0;
        const chargeAmount = parseFloat($('#chargeAmount').val()) || 0;
        const hasCharge = $('#hasCharge').is(':checked');
        
        const totalDeduction = amount + (hasCharge ? chargeAmount : 0);
        
        // Check if same bank selected
        if (fromBank && toBank && fromBank === toBank) {
            alert('Rekening sumber dan tujuan tidak boleh sama!');
            $('#toBank').val('');
            return;
        }
        
        // Check sufficient balance
        if (amount > 0 && totalDeduction > balance) {
            $('#submitBtn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed');
            $('#amount').addClass('border-red-500');
        } else {
            $('#submitBtn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed');
            $('#amount').removeClass('border-red-500');
        }
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Form submission confirmation
    $('#transferForm').on('submit', function(e) {
        const amount = parseFloat($('#amount').val()) || 0;
        const fromBank = $('#fromBank option:selected').data('name');
        const toBank = $('#toBank option:selected').data('name');
        const hasCharge = $('#hasCharge').is(':checked');
        const chargeAmount = parseFloat($('#chargeAmount').val()) || 0;
        
        let message = `Konfirmasi transfer Rp ${formatNumber(amount)} dari ${fromBank} ke ${toBank}`;
        if (hasCharge && chargeAmount > 0) {
            message += `\n\nBiaya transaksi: Rp ${formatNumber(chargeAmount)}`;
            message += `\nTotal yang akan didebet: Rp ${formatNumber(amount + chargeAmount)}`;
        }
        message += `\n\nLanjutkan transfer?`;
        
        if (!confirm(message)) {
            e.preventDefault();
        }
    });

    // Initialize summary
    updateSummary();
});
</script>
@endpush
@endsection
