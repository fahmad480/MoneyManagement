@extends('layouts.app')

@section('title', 'Verifikasi Email')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-2xl">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-envelope-open-text text-6xl text-blue-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Verifikasi Email Anda
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek inbox atau folder spam Anda.
            </p>
        </div>
        
        <div id="alert-container"></div>
        
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
            <div class="flex">
                <i class="fas fa-exclamation-triangle text-yellow-400 mt-0.5"></i>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Anda harus memverifikasi email Anda sebelum dapat mengakses dashboard.
                    </p>
                </div>
            </div>
        </div>
        
        <form id="resend-verification-form">
            @csrf
            <button type="submit" id="resend-btn" 
                    class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                    <i class="fas fa-paper-plane text-blue-500 group-hover:text-blue-400"></i>
                </span>
                <span id="resend-text">Kirim Ulang Email Verifikasi</span>
                <span id="resend-loading" class="hidden">
                    <i class="fas fa-spinner fa-spin"></i> Mengirim...
                </span>
            </button>
        </form>
        
        <div class="flex justify-center space-x-4">
            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                <i class="fas fa-home mr-1"></i> Ke Dashboard
            </a>
            <span class="text-gray-300">|</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#resend-verification-form').on('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        $('#resend-btn').prop('disabled', true);
        $('#resend-text').hide();
        $('#resend-loading').show();
        
        // Clear previous alerts
        $('#alert-container').html('');
        
        $.ajax({
            url: '{{ route("verification.send") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Show success message
                $('#alert-container').html(
                    '<div class="rounded-md bg-green-50 p-4 mb-4">' +
                        '<div class="flex">' +
                            '<i class="fas fa-check-circle text-green-400 mt-0.5"></i>' +
                            '<div class="ml-3">' +
                                '<p class="text-sm font-medium text-green-800">' + response.message + '</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
                
                // Re-enable button
                setTimeout(function() {
                    $('#resend-btn').prop('disabled', false);
                    $('#resend-text').show();
                    $('#resend-loading').hide();
                }, 3000);
            },
            error: function(xhr) {
                // Re-enable button
                $('#resend-btn').prop('disabled', false);
                $('#resend-text').show();
                $('#resend-loading').hide();
                
                let message = xhr.responseJSON.message || 'Terjadi kesalahan saat mengirim email.';
                
                $('#alert-container').html(
                    '<div class="rounded-md bg-red-50 p-4 mb-4">' +
                        '<div class="flex">' +
                            '<i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>' +
                            '<div class="ml-3">' +
                                '<p class="text-sm font-medium text-red-800">' + message + '</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            }
        });
    });
});
</script>
@endpush
