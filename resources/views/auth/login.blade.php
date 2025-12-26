@extends('layouts.guest')

@section('title', 'Inloggen')

@section('content')
<div id="login-container">
    <!-- Loading state -->
    <div id="loading-state" class="text-center py-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500 mx-auto"></div>
        <p class="mt-2 text-sm text-gray-400">Laden...</p>
    </div>

    <!-- PIN Login Section (known devices) -->
    <div id="pin-login-section" class="hidden">
        <div class="text-center mb-6">
            <p id="welcome-user" class="text-lg font-medium text-white"></p>
            <p class="text-sm text-gray-400">Voer je PIN in</p>
        </div>

        <!-- PIN Dots -->
        <div class="flex justify-center gap-3 mb-6">
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
        </div>

        <p id="pin-error" class="text-center text-red-400 text-sm mb-4 hidden"></p>

        <!-- Numpad -->
        <div class="grid grid-cols-3 gap-3 max-w-xs mx-auto">
            <button type="button" onclick="addPin('1')" class="numpad-btn">1</button>
            <button type="button" onclick="addPin('2')" class="numpad-btn">2</button>
            <button type="button" onclick="addPin('3')" class="numpad-btn">3</button>
            <button type="button" onclick="addPin('4')" class="numpad-btn">4</button>
            <button type="button" onclick="addPin('5')" class="numpad-btn">5</button>
            <button type="button" onclick="addPin('6')" class="numpad-btn">6</button>
            <button type="button" onclick="addPin('7')" class="numpad-btn">7</button>
            <button type="button" onclick="addPin('8')" class="numpad-btn">8</button>
            <button type="button" onclick="addPin('9')" class="numpad-btn">9</button>
            <!-- Left: Biometric (mobile) or QR (desktop) -->
            <button type="button" id="biometric-btn" onclick="startBiometric()" class="numpad-btn bg-emerald-900/50 hover:bg-emerald-800/50 hidden">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </button>
            <button type="button" id="qr-btn" onclick="toggleQrModal()" class="numpad-btn bg-blue-900/50 hover:bg-blue-800/50 hidden">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </button>
            <button type="button" onclick="addPin('0')" class="numpad-btn">0</button>
            <button type="button" onclick="removePin()" class="numpad-btn">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                </svg>
            </button>
        </div>

        <div class="text-center mt-6">
            <button type="button" onclick="showPasswordLogin()" class="text-sm text-emerald-400 hover:underline">
                Ander account? Login met wachtwoord
            </button>
        </div>
    </div>

    <!-- QR Login Modal -->
    <div id="qr-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/70" onclick="if(event.target === this) toggleQrModal()">
        <div class="glass rounded-2xl p-6 m-4 max-w-sm w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-white">Inloggen met telefoon</h3>
                <button onclick="toggleQrModal()" class="text-gray-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="text-center">
                <div id="qr-container" class="w-48 h-48 mx-auto bg-white rounded-lg flex items-center justify-center mb-4">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>
                </div>
                <p class="text-sm text-gray-300 mb-2">Scan met je telefoon waarop je al bent ingelogd</p>
                <p id="qr-status" class="text-xs text-gray-400">QR code laden...</p>
                <p id="qr-timer" class="text-xs text-emerald-400 hidden mt-1">Verloopt over <span id="timer">5:00</span></p>
            </div>
        </div>
    </div>

    <!-- Password Login Form -->
    <div id="password-login-section" class="hidden">
        <form method="POST" action="/login" id="loginForm">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-300 mb-1">E-mailadres</label>
                <input type="email" name="email" id="email" required autofocus
                    class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                    placeholder="voorbeeld@email.nl" value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-300 mb-1">Wachtwoord</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                        <svg id="eye-closed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                        <svg id="eye-open" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center mb-6">
                <input type="checkbox" name="remember" id="remember" checked
                    class="w-4 h-4 rounded border-gray-600 bg-white/10 text-emerald-500 focus:ring-emerald-500">
                <label for="remember" class="ml-2 text-sm text-gray-300">Onthoud mij</label>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors">
                Inloggen
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deviceFingerprint = null;
let currentPin = '';

async function generateFingerprint() {
    const data = [
        navigator.userAgent,
        navigator.language,
        screen.width + 'x' + screen.height,
        screen.colorDepth,
        new Date().getTimezoneOffset(),
        navigator.hardwareConcurrency || 'unknown',
        navigator.platform
    ].join('|');
    const encoder = new TextEncoder();
    const hashBuffer = await crypto.subtle.digest('SHA-256', encoder.encode(data));
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

function togglePassword() {
    const field = document.getElementById('password');
    const eyeOpen = document.getElementById('eye-open');
    const eyeClosed = document.getElementById('eye-closed');
    if (field.type === 'password') {
        field.type = 'text';
        eyeOpen.classList.remove('hidden');
        eyeClosed.classList.add('hidden');
    } else {
        field.type = 'password';
        eyeOpen.classList.add('hidden');
        eyeClosed.classList.remove('hidden');
    }
}

function showPasswordLogin() {
    document.getElementById('pin-login-section').classList.add('hidden');
    document.getElementById('password-login-section').classList.remove('hidden');
}

function addPin(digit) {
    if (currentPin.length >= 5) return;
    currentPin += digit;
    updatePinDots();
    if (currentPin.length === 5) submitPin();
}

function removePin() {
    currentPin = currentPin.slice(0, -1);
    updatePinDots();
    hidePinError();
}

function updatePinDots() {
    document.querySelectorAll('.pin-dot').forEach((dot, i) => {
        dot.classList.toggle('filled', i < currentPin.length);
    });
}

function showPinError(msg) {
    const el = document.getElementById('pin-error');
    el.textContent = msg;
    el.classList.remove('hidden');
    document.querySelectorAll('.pin-dot').forEach(dot => {
        dot.classList.add('animate-shake');
        setTimeout(() => dot.classList.remove('animate-shake'), 300);
    });
    currentPin = '';
    updatePinDots();
}

function hidePinError() {
    document.getElementById('pin-error').classList.add('hidden');
}

async function submitPin() {
    try {
        const res = await fetch('/auth/pin/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ fingerprint: deviceFingerprint, pin: currentPin }),
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = data.redirect || '/';
        } else {
            showPinError(data.message || 'Onjuiste PIN');
        }
    } catch (err) {
        showPinError('Er ging iets mis');
    }
}

async function startBiometric() {
    if (!window.PublicKeyCredential) {
        showPinError('Biometrie niet ondersteund');
        return;
    }
    try {
        const optRes = await fetch('/auth/passkey/login/options', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
        if (!optRes.ok) { showPinError('Geen passkey gevonden'); return; }
        const options = await optRes.json();
        if (!options.challenge) { showPinError('Geen passkey gevonden'); return; }

        const publicKeyOptions = {
            challenge: base64urlToBuffer(options.challenge),
            timeout: options.timeout || 60000,
            rpId: options.rpId,
            userVerification: options.userVerification || 'preferred',
        };
        if (options.allowCredentials?.length > 0) {
            publicKeyOptions.allowCredentials = options.allowCredentials.map(c => ({
                id: base64urlToBuffer(c.id), type: c.type, transports: c.transports || ['internal', 'hybrid'],
            }));
        }
        const credential = await navigator.credentials.get({ publicKey: publicKeyOptions });
        const loginRes = await fetch('/auth/passkey/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                id: credential.id,
                rawId: bufferToBase64url(credential.rawId),
                type: credential.type,
                response: {
                    authenticatorData: bufferToBase64url(credential.response.authenticatorData),
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    signature: bufferToBase64url(credential.response.signature),
                    userHandle: credential.response.userHandle ? bufferToBase64url(credential.response.userHandle) : null,
                },
            }),
        });
        if (loginRes.ok) window.location.href = '/';
        else showPinError('Biometrie mislukt');
    } catch (err) {
        if (err.name !== 'NotAllowedError') showPinError('Biometrie geannuleerd');
    }
}

// QR Code functions
let qrToken = null, pollInterval = null, timerInterval = null, expiresIn = 300, qrModalOpen = false;

function toggleQrModal() {
    const modal = document.getElementById('qr-modal');
    qrModalOpen = !qrModalOpen;
    if (qrModalOpen) {
        modal.classList.remove('hidden');
        if (!qrToken) generateQr();
    } else {
        modal.classList.add('hidden');
    }
}

async function generateQr() {
    const container = document.getElementById('qr-container');
    container.innerHTML = '<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-500"></div>';
    document.getElementById('qr-status').textContent = 'QR code laden...';
    document.getElementById('qr-timer').classList.add('hidden');

    try {
        const res = await fetch('/auth/qr/generate', {
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
        const data = await res.json();
        if (data.success) {
            qrToken = data.token;
            expiresIn = 300;
            const qrUrl = `${window.location.origin}/auth/qr/scan?token=${data.token}`;
            container.innerHTML = `<img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${encodeURIComponent(qrUrl)}" alt="QR" class="rounded">`;
            document.getElementById('qr-status').textContent = 'Scan met je telefoon';
            document.getElementById('qr-timer').classList.remove('hidden');
            startPolling();
            startTimer();
        }
    } catch (err) {
        container.innerHTML = '<span class="text-red-400 text-xs">QR laden mislukt</span>';
    }
}

function startPolling() {
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(async () => {
        try {
            const res = await fetch(`/auth/qr/${qrToken}/status`);
            const data = await res.json();
            if (data.status === 'approved') {
                clearInterval(pollInterval);
                clearInterval(timerInterval);
                document.getElementById('qr-status').textContent = 'Ingelogd! Doorsturen...';
                window.location.href = data.redirect || '/';
            } else if (data.status === 'expired') {
                clearInterval(pollInterval);
                clearInterval(timerInterval);
                showQrExpired();
            }
        } catch (err) {}
    }, 2000);
}

function startTimer() {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
        expiresIn--;
        const mins = Math.floor(expiresIn / 60);
        const secs = expiresIn % 60;
        document.getElementById('timer').textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
        if (expiresIn <= 0) {
            clearInterval(timerInterval);
            clearInterval(pollInterval);
            showQrExpired();
        }
    }, 1000);
}

function showQrExpired() {
    document.getElementById('qr-container').innerHTML = '<button onclick="generateQr()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">Vernieuw QR</button>';
    document.getElementById('qr-status').textContent = 'QR code verlopen';
    document.getElementById('qr-timer').classList.add('hidden');
    qrToken = null;
}

// Base64url helpers
function base64urlToBuffer(b64) {
    const padding = '='.repeat((4 - b64.length % 4) % 4);
    const base64 = b64.replace(/-/g, '+').replace(/_/g, '/') + padding;
    return Uint8Array.from(atob(base64), c => c.charCodeAt(0)).buffer;
}
function bufferToBase64url(buf) {
    const bytes = new Uint8Array(buf);
    let str = '';
    for (const b of bytes) str += String.fromCharCode(b);
    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
}

// Keyboard support
document.addEventListener('keydown', e => {
    if (document.getElementById('pin-login-section').classList.contains('hidden')) return;
    if (e.key >= '0' && e.key <= '9') { e.preventDefault(); addPin(e.key); }
    else if (e.key === 'Backspace') { e.preventDefault(); removePin(); }
    else if (e.key === 'Enter' && currentPin.length === 5) { e.preventDefault(); submitPin(); }
});

// Init
(async function() {
    deviceFingerprint = await generateFingerprint();
    try {
        const res = await fetch('/auth/pin/check-device', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ fingerprint: deviceFingerprint }),
        });
        const data = await res.json();
        document.getElementById('loading-state').classList.add('hidden');

        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);

        if (data.has_device && data.has_pin) {
            document.getElementById('pin-login-section').classList.remove('hidden');
            document.getElementById('welcome-user').textContent = `Welkom terug${data.user_name ? ', ' + data.user_name : ''}!`;
            if (isMobile && data.has_biometric && window.PublicKeyCredential) {
                document.getElementById('biometric-btn').classList.remove('hidden');
                setTimeout(() => startBiometric(), 500);
            } else if (!isMobile) {
                document.getElementById('qr-btn').classList.remove('hidden');
            }
        } else {
            document.getElementById('password-login-section').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('password-login-section').classList.remove('hidden');
    }
})();
</script>
@endpush
