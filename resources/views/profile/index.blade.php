@extends('layouts.app')

@section('title', 'Pengaturan Akun')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Pengaturan Akun</h1>
        <p class="text-gray-600 mt-1">Kelola informasi akun dan keamanan Anda</p>
    </div>

    <div id="alert-container"></div>

    <!-- Profile Information Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-user-circle text-blue-600 text-2xl mr-3"></i>
            <h2 class="text-xl font-semibold text-gray-900">Informasi Akun</h2>
        </div>

        <form id="profile-form" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Profile Photo -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Profil</label>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            @if(Auth::user()->profile_photo)
                                <img id="profile-preview" src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="Profile" class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">
                            @else
                                <div id="profile-preview" class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center border-2 border-gray-300">
                                    <i class="fas fa-user text-blue-600 text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <input type="file" id="profile_photo" name="profile_photo" accept="image/*" class="hidden" onchange="previewImage(event)">
                            <button type="button" onclick="document.getElementById('profile_photo').click()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-camera mr-2"></i>Ubah Foto
                            </button>
                            <p class="text-xs text-gray-500 mt-1">JPG, PNG (Max 2MB)</p>
                        </div>
                    </div>
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input type="tel" id="phone" name="phone" value="{{ Auth::user()->phone }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" id="address" name="address" value="{{ Auth::user()->address }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" id="profile-btn" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <span id="profile-text"><i class="fas fa-save mr-2"></i>Simpan Perubahan</span>
                    <span id="profile-loading" class="hidden"><i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Change Password Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center mb-4">
            <i class="fas fa-lock text-amber-600 text-2xl mr-3"></i>
            <h2 class="text-xl font-semibold text-gray-900">Ubah Password</h2>
        </div>

        <form id="password-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Current Password -->
                <div class="md:col-span-2">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePasswordField('current_password')">
                            <i id="current_password-eye" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </div>
                    </div>
                </div>

                <!-- New Password -->
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePasswordField('new_password')">
                            <i id="new_password-eye" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePasswordField('new_password_confirmation')">
                            <i id="new_password_confirmation-eye" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" id="password-btn" class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                    <span id="password-text"><i class="fas fa-key mr-2"></i>Ubah Password</span>
                    <span id="password-loading" class="hidden"><i class="fas fa-spinner fa-spin mr-2"></i>Mengubah...</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Delete Account Card -->
    <div class="bg-white rounded-lg shadow-md p-6 border-2 border-red-200">
        <div class="flex items-center mb-4">
            <i class="fas fa-exclamation-triangle text-red-600 text-2xl mr-3"></i>
            <h2 class="text-xl font-semibold text-gray-900">Hapus Akun</h2>
        </div>

        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
                <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan. Semua data Anda termasuk bank, kartu, transaksi, dan kategori akan dihapus permanen.
                    </p>
                </div>
            </div>
        </div>

        <form id="delete-form">
            @csrf
            <div class="space-y-4">
                <!-- Password Confirmation -->
                <div>
                    <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" id="delete_password" name="password" required
                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer" onclick="togglePasswordField('delete_password')">
                            <i id="delete_password-eye" class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
                        </div>
                    </div>
                </div>

                <!-- Confirmation Text -->
                <div>
                    <label for="confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Ketik <code class="bg-gray-200 px-2 py-1 rounded text-red-600 font-bold">DELETE</code> untuk konfirmasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="confirmation" name="confirmation" required
                           placeholder="DELETE"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" id="delete-btn" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <span id="delete-text"><i class="fas fa-trash-alt mr-2"></i>Hapus Akun Permanen</span>
                    <span id="delete-loading" class="hidden"><i class="fas fa-spinner fa-spin mr-2"></i>Menghapus...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePasswordField(fieldId) {
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

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('profile-preview');
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="w-20 h-20 rounded-full object-cover border-2 border-gray-300">';
        }
        reader.readAsDataURL(file);
    }
}

function showAlert(message, type = 'success') {
    const alertTypes = {
        success: { icon: 'fa-check-circle', color: 'green' },
        error: { icon: 'fa-exclamation-circle', color: 'red' },
        warning: { icon: 'fa-exclamation-triangle', color: 'yellow' }
    };
    
    const alert = alertTypes[type];
    const alertHtml = `
        <div class="rounded-md bg-${alert.color}-50 p-4 mb-4">
            <div class="flex">
                <i class="fas ${alert.icon} text-${alert.color}-400 mt-0.5"></i>
                <div class="ml-3">
                    <p class="text-sm font-medium text-${alert.color}-800">${message}</p>
                </div>
            </div>
        </div>
    `;
    
    $('#alert-container').html(alertHtml);
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        $('#alert-container').html('');
    }, 5000);
}

$(document).ready(function() {
    // Profile Update Form
    $('#profile-form').on('submit', function(e) {
        e.preventDefault();
        
        $('#profile-btn').prop('disabled', true);
        $('#profile-text').hide();
        $('#profile-loading').show();
        $('#alert-container').html('');
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("profile.update") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showAlert(response.message, 'success');
                $('#profile-btn').prop('disabled', false);
                $('#profile-text').show();
                $('#profile-loading').hide();
            },
            error: function(xhr) {
                let message = xhr.responseJSON.message || 'Terjadi kesalahan';
                let errors = xhr.responseJSON.errors;
                
                if (errors) {
                    message += '<ul class="list-disc pl-5 mt-2">';
                    $.each(errors, function(key, value) {
                        message += '<li>' + value[0] + '</li>';
                    });
                    message += '</ul>';
                }
                
                showAlert(message, 'error');
                $('#profile-btn').prop('disabled', false);
                $('#profile-text').show();
                $('#profile-loading').hide();
            }
        });
    });

    // Password Update Form
    $('#password-form').on('submit', function(e) {
        e.preventDefault();
        
        $('#password-btn').prop('disabled', true);
        $('#password-text').hide();
        $('#password-loading').show();
        $('#alert-container').html('');
        
        $.ajax({
            url: '{{ route("profile.password") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                showAlert(response.message, 'success');
                $('#password-form')[0].reset();
                $('#password-btn').prop('disabled', false);
                $('#password-text').show();
                $('#password-loading').hide();
            },
            error: function(xhr) {
                let message = xhr.responseJSON.message || 'Terjadi kesalahan';
                let errors = xhr.responseJSON.errors;
                
                if (errors) {
                    message += '<ul class="list-disc pl-5 mt-2">';
                    $.each(errors, function(key, value) {
                        message += '<li>' + value[0] + '</li>';
                    });
                    message += '</ul>';
                }
                
                showAlert(message, 'error');
                $('#password-btn').prop('disabled', false);
                $('#password-text').show();
                $('#password-loading').hide();
            }
        });
    });

    // Delete Account Form
    $('#delete-form').on('submit', function(e) {
        e.preventDefault();
        
        // Double confirmation
        if (!confirm('Apakah Anda YAKIN ingin menghapus akun? Tindakan ini TIDAK DAPAT dibatalkan!')) {
            return;
        }
        
        $('#delete-btn').prop('disabled', true);
        $('#delete-text').hide();
        $('#delete-loading').show();
        $('#alert-container').html('');
        
        $.ajax({
            url: '{{ route("profile.delete") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                showAlert('Akun berhasil dihapus. Mengalihkan...', 'success');
                setTimeout(function() {
                    window.location.href = response.redirect;
                }, 2000);
            },
            error: function(xhr) {
                let message = xhr.responseJSON.message || 'Terjadi kesalahan';
                let errors = xhr.responseJSON.errors;
                
                if (errors) {
                    message += '<ul class="list-disc pl-5 mt-2">';
                    $.each(errors, function(key, value) {
                        message += '<li>' + value[0] + '</li>';
                    });
                    message += '</ul>';
                }
                
                showAlert(message, 'error');
                $('#delete-btn').prop('disabled', false);
                $('#delete-text').show();
                $('#delete-loading').hide();
            }
        });
    });
});
</script>
@endpush
