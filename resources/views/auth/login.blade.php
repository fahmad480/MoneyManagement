@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-2xl">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-wallet text-6xl text-blue-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ config('app.name') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masuk ke akun Anda
            </p>
        </div>
        
        <div id="alert-container"></div>
        
        <form class="mt-8 space-y-6" id="login-form">
            @csrf
            <div class="rounded-md shadow-sm space-y-4">
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
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" autocomplete="current-password" required 
                               class="appearance-none rounded-lg relative block w-full pl-10 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                               placeholder="Password">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-blue-600 hover:text-blue-500">
                        Lupa password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" id="login-btn" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-sign-in-alt text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    <span id="login-text">Masuk</span>
                    <span id="login-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </span>
                </button>
            </div>
            
            <div class="text-center">
                <span class="text-sm text-gray-600">Belum punya akun?</span>
                <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500">
                    Daftar sekarang
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#login-form').on('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        $('#login-btn').prop('disabled', true);
        $('#login-text').hide();
        $('#login-loading').show();
        
        // Clear previous alerts
        $('#alert-container').html('');
        
        $.ajax({
            url: '{{ route("login") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                // Show success message
                $('#alert-container').html(
                    '<div class="rounded-md bg-green-50 p-4 mb-4">' +
                        '<div class="flex">' +
                            '<i class="fas fa-check-circle text-green-400 mt-0.5"></i>' +
                            '<div class="ml-3">' +
                                '<p class="text-sm font-medium text-green-800">Login berhasil! Mengalihkan...</p>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
                
                // Redirect to dashboard
                setTimeout(function() {
                    window.location.href = '{{ route("dashboard") }}';
                }, 1000);
            },
            error: function(xhr) {
                // Re-enable button
                $('#login-btn').prop('disabled', false);
                $('#login-text').show();
                $('#login-loading').hide();
                
                let errors = xhr.responseJSON.errors;
                let message = xhr.responseJSON.message || 'Terjadi kesalahan saat login.';
                
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
