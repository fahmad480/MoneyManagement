@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h1 class="text-3xl font-bold text-gray-800">Edit User</h1>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            placeholder="John Doe" required>
                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                            placeholder="user@example.com" required>
                        @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('phone') border-red-500 @enderror" 
                            placeholder="08123456789">
                        @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Password Baru</label>
                        <input type="password" name="password" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                            placeholder="Kosongkan jika tidak diubah">
                        @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                            placeholder="Ulangi password baru">
                    </div>

                    <!-- Role -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Role <span class="text-red-500">*</span></label>
                        <select name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror" required>
                            <option value="">Pilih Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $user->roles->first()->name ?? '') == $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Verification Status -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Status Verifikasi Email</label>
                        <div class="flex items-center space-x-6">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="email_verification_status" value="verified" 
                                    @if(old('email_verification_status', !is_null($user->email_verified_at) ? 'verified' : 'unverified') === 'verified') checked @endif
                                    class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="flex items-center">
                                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                                    <span class="text-gray-700">Terverifikasi</span>
                                </span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" name="email_verification_status" value="unverified" 
                                    @if(old('email_verification_status', !is_null($user->email_verified_at) ? 'verified' : 'unverified') === 'unverified') checked @endif
                                    class="mr-2 text-blue-600 focus:ring-blue-500">
                                <span class="flex items-center">
                                    <i class="fas fa-times-circle text-red-600 mr-2"></i>
                                    <span class="text-gray-700">Belum Terverifikasi</span>
                                </span>
                            </label>
                        </div>
                        @if(!is_null($user->email_verified_at))
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Diverifikasi pada: {{ $user->email_verified_at->format('d M Y, H:i') }}
                        </p>
                        @endif
                        @error('email_verification_status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Photo -->
                    @if($user->profile_photo)
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Foto Profile Saat Ini</label>
                        <img src="{{ Storage::url($user->profile_photo) }}" class="h-32 w-32 object-cover rounded-lg" alt="Current photo">
                    </div>
                    @endif

                    <!-- Profile Photo -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Ubah Foto Profile</label>
                        <input type="file" name="profile_photo" accept="image/*" onchange="previewImage(event)"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('profile_photo') border-red-500 @enderror">
                        @error('profile_photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <div id="imagePreview" class="mt-3 hidden">
                            <img id="preview" class="h-32 w-32 object-cover rounded-lg" alt="Preview">
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-2">Alamat</label>
                        <textarea name="address" rows="3" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('address') border-red-500 @enderror" 
                            placeholder="Alamat lengkap">{{ old('address', $user->address) }}</textarea>
                        @error('address')
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
                    <a href="{{ route('admin.users.index') }}" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-medium py-3 rounded-lg text-center transition">
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
    function previewImage(event) {
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('imagePreview');
        const file = event.target.files[0];
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endpush
@endsection
