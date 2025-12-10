@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-2xl">
        <div>
            <div class="flex justify-center">
                <i class="fas fa-lock text-6xl text-blue-600"></i>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Reset Password
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Masukkan password baru Anda
            </p>
        </div>
        
        <div id="alert-container"></div>
        
        <form class="mt-8 space-y-6" id="reset-password-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password" name="password" type="password" required 
                           class="appearance-none rounded-lg relative block w-full pl-10 pr-10 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Password baru">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('password')">
                        <i id="password-eye" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                    </div>
                </div>
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-gray-400"></i>
                    </div>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                           class="appearance-none rounded-lg relative block w-full pl-10 pr-10 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" 
                           placeholder="Konfirmasi password">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePassword('password_confirmation')">
                        <i id="password_confirmation-eye" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" id="reset-btn" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <i class="fas fa-check text-blue-500 group-hover:text-blue-400"></i>
                    </span>
                    <span id="reset-text">Reset Password</span>
                    <span id="reset-loading" class="hidden">
                        <i class="fas fa-spinner fa-spin"></i> Loading...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

$(document).ready(function() {
    $('#reset-password-form').on('submit', function(e) {
        e.preventDefault();
        
        // Disable button and show loading
        $('#reset-btn').prop('disabled', true);
        $('#reset-text').hide();
        $('#reset-loading').show();
        
        // Clear previous alerts
        $('#alert-container').html('');
        
        $.ajax({
            url: '{{ route("password.update") }}',
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
                
                // Redirect to login
                setTimeout(function() {
                    window.location.href = '{{ route("login") }}';
                }, 2000);
            },
            error: function(xhr) {
                // Re-enable button
                $('#reset-btn').prop('disabled', false);
                $('#reset-text').show();
                $('#reset-loading').hide();
                
                let errors = xhr.responseJSON.errors;
                let message = xhr.responseJSON.message || 'Terjadi kesalahan saat reset password.';
                
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
