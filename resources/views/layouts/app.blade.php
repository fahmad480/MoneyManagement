<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#4F46E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Jangan Boros">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="application-name" content="Jangan Boros">
    
    <!-- PWA Icons -->
    <link rel="icon" type="image/png" sizes="72x72" href="/icons/icon-72x72.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/icons/icon-96x96.png">
    <link rel="icon" type="image/png" sizes="128x128" href="/icons/icon-128x128.png">
    <link rel="icon" type="image/png" sizes="144x144" href="/icons/icon-144x144.png">
    <link rel="icon" type="image/png" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="icon" type="image/png" sizes="384x384" href="/icons/icon-384x384.png">
    <link rel="icon" type="image/png" sizes="512x512" href="/icons/icon-512x512.png">
    
    <!-- Apple Touch Icons -->
    <link rel="apple-touch-icon" sizes="72x72" href="/icons/icon-72x72.png">
    <link rel="apple-touch-icon" sizes="96x96" href="/icons/icon-96x96.png">
    <link rel="apple-touch-icon" sizes="128x128" href="/icons/icon-128x128.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/icons/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="192x192" href="/icons/icon-192x192.png">
    <link rel="apple-touch-icon" sizes="384x384" href="/icons/icon-384x384.png">
    <link rel="apple-touch-icon" sizes="512x512" href="/icons/icon-512x512.png">
    
    <!-- Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen">
        @auth
            @include('layouts.navigation')
        @endauth
        
        <main class="@auth md:ml-64 @endauth min-h-screen">
            @auth
                <div class="py-6 px-4 sm:px-6 lg:px-8">
                    <!-- Email Verification Alert -->
                    @if(is_null(Auth::user()->email_verified_at))
                    <div x-data="{ show: true, sending: false }" x-show="show" class="email-verification-alert bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800 px-4 py-3 rounded-lg mb-6" role="alert">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start flex-1">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 text-xl mt-0.5"></i>
                                <div class="flex-1">
                                    <p class="font-semibold mb-1">Email Anda Belum Diverifikasi</p>
                                    <p class="text-sm mb-3">Untuk keamanan akun Anda, silakan verifikasi email Anda. Kami telah mengirimkan link verifikasi ke <strong>{{ Auth::user()->email }}</strong></p>
                                    <form id="resend-verification-form" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                x-bind:disabled="sending"
                                                class="text-sm bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                            <span x-show="!sending">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Kirim Ulang Email Verifikasi
                                            </span>
                                            <span x-show="sending" x-cloak>
                                                <i class="fas fa-spinner fa-spin mr-1"></i>
                                                Mengirim...
                                            </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <button @click="show = false" class="text-yellow-600 hover:text-yellow-800 ml-3">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-50 border-l-4 border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-red-50 border-l-4 border-red-400 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-600 hover:text-red-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div x-data="{ show: true }" x-show="show" class="bg-red-50 border-l-4 border-red-400 text-red-800 px-4 py-3 rounded-lg mb-6" role="alert">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-red-600 mr-3 text-xl mt-0.5"></i>
                            <div class="flex-1">
                                <p class="font-semibold mb-2">Terdapat kesalahan pada input:</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <button @click="show = false" class="text-red-600 hover:text-red-800 ml-3">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    @endif
                    
                    @yield('content')
                </div>
            @else
                @yield('content')
            @endauth
        </main>
    </div>
    
    @stack('scripts')
    
    <!-- Email Verification Resend Script -->
    @auth
    @if(is_null(Auth::user()->email_verified_at))
    <script>
    $(document).ready(function() {
        $('#resend-verification-form').on('submit', function(e) {
            e.preventDefault();
            
            // Get Alpine.js component
            const alpineData = Alpine.$data(this);
            alpineData.sending = true;
            
            $.ajax({
                url: '{{ route("verification.send") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    alpineData.sending = false;
                    
                    // Show success message
                    const successAlert = `
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-green-50 border-l-4 border-green-400 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                                <span>${response.message}</span>
                            </div>
                            <button @click="show = false" class="text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    $('.email-verification-alert').after(successAlert);
                },
                error: function(xhr) {
                    alpineData.sending = false;
                    
                    let message = xhr.responseJSON?.message || 'Terjadi kesalahan saat mengirim email verifikasi';
                    
                    // Show error message
                    const errorAlert = `
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" class="bg-red-50 border-l-4 border-red-400 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                                <span>${message}</span>
                            </div>
                            <button @click="show = false" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    $('.email-verification-alert').after(errorAlert);
                }
            });
        });
    });
    </script>
    @endif
    @endauth
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/serviceworker.js')
                    .then(registration => {
                        console.log('ServiceWorker registration successful:', registration.scope);
                    })
                    .catch(err => {
                        console.log('ServiceWorker registration failed:', err);
                    });
            });
        }
        
        // Install PWA prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            
            // Show install button/banner if you want
            const installBanner = document.createElement('div');
            installBanner.id = 'pwa-install-banner';
            installBanner.className = 'fixed bottom-4 right-4 bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3';
            installBanner.innerHTML = `
                <i class="fas fa-download"></i>
                <span>Install aplikasi ini</span>
                <button onclick="installPWA()" class="bg-white text-indigo-600 px-3 py-1 rounded text-sm font-semibold hover:bg-gray-100">Install</button>
                <button onclick="dismissInstall()" class="text-white hover:text-gray-200 ml-2"><i class="fas fa-times"></i></button>
            `;
            document.body.appendChild(installBanner);
        });
        
        function installPWA() {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.remove();
            
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User accepted the install prompt');
                    }
                    deferredPrompt = null;
                });
            }
        }
        
        function dismissInstall() {
            const banner = document.getElementById('pwa-install-banner');
            if (banner) banner.remove();
        }
        
        // Track app installation
        window.addEventListener('appinstalled', (evt) => {
            console.log('App was installed');
        });
    </script>
</body>
</html>
