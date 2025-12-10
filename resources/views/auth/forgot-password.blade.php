@extends('layouts.app')

@section('title', 'Lupa Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-2xl">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-key text-6xl text-blue-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Lupa Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan email Anda dan kami akan mengirimkan link untuk reset password
            </p>
        </div>
        
        <div id="alert-container"></div>
        
        <form class="mt-8 space-y-6" id="forgot-password-form">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Email address">
                </div>
            </div>

            <div>
                <button type="submit" id="reset-btn" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-paper-plane text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    <span id="reset-text">Kirim Link Reset Password</span>
                    <span id="reset-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin"></i> Mengirim...
                    </span>
                </button>
            </div>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#forgot-password-form').on('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        $('#reset-btn').prop('disabled', true);
        $('#reset-text').hide();
        $('#reset-loading').show();
        
        // Clear previous alerts
        $('#alert-container').html('');
        
        $.ajax({
            url: '{{ route("password.email") }}',
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
                
                // Clear form
                $('#forgot-password-form')[0].reset();
                
                // Re-enable button
                $('#reset-btn').prop('disabled', false);
                $('#reset-text').show();
                $('#reset-loading').hide();
            },
            error: function(xhr) {
                // Re-enable button
                $('#reset-btn').prop('disabled', false);
                $('#reset-text').show();
                $('#reset-loading').hide();
                
                let errors = xhr.responseJSON.errors;
                let message = xhr.responseJSON.message || 'Terjadi kesalahan saat mengirim email.';
                
                let errorHtml = '<div class="rounded-md bg-red-50 p-4 mb-4">' +
                    '<div class="flex">' +
                        '<i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>' +
                        '<div class="ml-3">' +
                            '<h3 class="text-sm font-medium text-red-800">' + message + '</h3>';
                
                if (errors) {
                    errorHtml += '<div class="mt-2 text-sm text-red-700"><ul class="list-disc pl-5 space-y-1">';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul></div>';
                }
                
                errorHtml += '</div></div></div>';
                
                $('#alert-container').html(errorHtml);
            }
        });
    });
});
</script>
@endpush
