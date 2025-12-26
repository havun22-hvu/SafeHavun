@extends('layouts.guest')

@section('title', 'PIN Instellen')

@section('content')
<div id="step-indicator" class="flex justify-center gap-2 mb-6">
    <div id="step-1-dot" class="w-3 h-3 rounded-full bg-emerald-500"></div>
    <div id="step-2-dot" class="w-3 h-3 rounded-full bg-gray-600"></div>
</div>

<!-- Step 1: Choose method -->
<div id="step-choose" class="text-center">
    <div class="mb-6">
        <svg class="w-16 h-16 mx-auto text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
    </div>
    <h3 class="text-lg font-medium text-white mb-2">Sneller inloggen</h3>
    <p class="text-sm text-gray-400 mb-6">Kies hoe je de volgende keer wilt inloggen op dit apparaat.</p>

    <div class="space-y-3">
        <button type="button" onclick="showPinSetup()" class="w-full p-4 border border-white/20 rounded-xl hover:border-emerald-500 transition-colors text-left flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-900/50 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-white">PIN code (5 cijfers)</p>
                <p class="text-sm text-gray-400">Snel en eenvoudig</p>
            </div>
        </button>

        <button type="button" id="biometric-option" onclick="setupBiometric()" class="hidden w-full p-4 border border-white/20 rounded-xl hover:border-emerald-500 transition-colors text-left items-center gap-4">
            <div class="w-12 h-12 bg-blue-900/50 rounded-full flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                </svg>
            </div>
            <div>
                <p class="font-medium text-white">Vingerafdruk / Face ID</p>
                <p class="text-sm text-gray-400">Snelste optie (alleen mobiel)</p>
            </div>
        </button>
    </div>

    <div class="mt-6">
        <a href="/" class="text-sm text-gray-400 hover:text-white">Overslaan</a>
    </div>
</div>

<!-- Step 2: PIN Setup -->
<div id="step-pin" class="hidden">
    <div class="text-center mb-6">
        <h3 class="text-lg font-medium text-white mb-2">Kies je PIN</h3>
        <p id="pin-status" class="text-sm text-gray-400">Voer een 5-cijferige PIN in</p>
    </div>

    <div class="flex justify-center gap-3 mb-6">
        <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
        <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
        <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
        <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
        <div class="pin-dot w-4 h-4 rounded-full border-2 border-emerald-500 bg-transparent"></div>
    </div>

    <p id="pin-error" class="text-center text-red-400 text-sm mb-4 hidden"></p>

    <div class="grid grid-cols-3 gap-3 max-w-xs mx-auto">
        <button type="button" onclick="addSetupPin('1')" class="numpad-btn">1</button>
        <button type="button" onclick="addSetupPin('2')" class="numpad-btn">2</button>
        <button type="button" onclick="addSetupPin('3')" class="numpad-btn">3</button>
        <button type="button" onclick="addSetupPin('4')" class="numpad-btn">4</button>
        <button type="button" onclick="addSetupPin('5')" class="numpad-btn">5</button>
        <button type="button" onclick="addSetupPin('6')" class="numpad-btn">6</button>
        <button type="button" onclick="addSetupPin('7')" class="numpad-btn">7</button>
        <button type="button" onclick="addSetupPin('8')" class="numpad-btn">8</button>
        <button type="button" onclick="addSetupPin('9')" class="numpad-btn">9</button>
        <button type="button" onclick="goBack()" class="numpad-btn text-sm">Terug</button>
        <button type="button" onclick="addSetupPin('0')" class="numpad-btn">0</button>
        <button type="button" onclick="removeSetupPin()" class="numpad-btn">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
            </svg>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
let deviceFingerprint = null;
let currentPin = '';
let confirmPin = '';
let isConfirming = false;

async function generateFingerprint() {
    const data = [navigator.userAgent, navigator.language, screen.width + 'x' + screen.height, screen.colorDepth, new Date().getTimezoneOffset(), navigator.hardwareConcurrency || 'unknown', navigator.platform].join('|');
    const hashBuffer = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(data));
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

(async function() {
    deviceFingerprint = await generateFingerprint();
    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    if (isMobile && window.PublicKeyCredential) {
        try {
            const available = await PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
            if (available) {
                document.getElementById('biometric-option').classList.remove('hidden');
                document.getElementById('biometric-option').classList.add('flex');
            }
        } catch (e) {}
    }
})();

function showPinSetup() {
    document.getElementById('step-choose').classList.add('hidden');
    document.getElementById('step-pin').classList.remove('hidden');
    document.getElementById('step-1-dot').classList.replace('bg-emerald-500', 'bg-gray-600');
    document.getElementById('step-2-dot').classList.replace('bg-gray-600', 'bg-emerald-500');
}

function goBack() {
    document.getElementById('step-pin').classList.add('hidden');
    document.getElementById('step-choose').classList.remove('hidden');
    document.getElementById('step-2-dot').classList.replace('bg-emerald-500', 'bg-gray-600');
    document.getElementById('step-1-dot').classList.replace('bg-gray-600', 'bg-emerald-500');
    currentPin = ''; confirmPin = ''; isConfirming = false;
    updatePinDots();
}

function addSetupPin(digit) {
    const pin = isConfirming ? confirmPin : currentPin;
    if (pin.length >= 5) return;
    if (isConfirming) confirmPin += digit; else currentPin += digit;
    updatePinDots();

    if (!isConfirming && currentPin.length === 5) {
        setTimeout(() => {
            isConfirming = true;
            updatePinDots();
            document.getElementById('pin-status').textContent = 'Bevestig je PIN';
        }, 300);
    } else if (isConfirming && confirmPin.length === 5) {
        if (currentPin === confirmPin) savePin();
        else {
            showPinError('PINs komen niet overeen');
            currentPin = ''; confirmPin = ''; isConfirming = false;
            updatePinDots();
            document.getElementById('pin-status').textContent = 'Voer een 5-cijferige PIN in';
        }
    }
}

function removeSetupPin() {
    if (isConfirming) confirmPin = confirmPin.slice(0, -1);
    else currentPin = currentPin.slice(0, -1);
    updatePinDots();
    hidePinError();
}

function updatePinDots() {
    const pin = isConfirming ? confirmPin : currentPin;
    document.querySelectorAll('.pin-dot').forEach((dot, i) => dot.classList.toggle('filled', i < pin.length));
}

function showPinError(msg) {
    document.getElementById('pin-error').textContent = msg;
    document.getElementById('pin-error').classList.remove('hidden');
}

function hidePinError() {
    document.getElementById('pin-error').classList.add('hidden');
}

async function savePin() {
    document.querySelectorAll('.numpad-btn').forEach(btn => btn.disabled = true);
    document.getElementById('pin-status').textContent = 'Even geduld...';

    try {
        const res = await fetch('/auth/pin/setup', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ fingerprint: deviceFingerprint, pin: currentPin }),
        });
        const data = await res.json();
        if (data.success) window.location.href = '/';
        else {
            document.querySelectorAll('.numpad-btn').forEach(btn => btn.disabled = false);
            showPinError(data.message || 'Er ging iets mis');
            currentPin = ''; confirmPin = ''; isConfirming = false;
            updatePinDots();
            document.getElementById('pin-status').textContent = 'Voer een 5-cijferige PIN in';
        }
    } catch (err) {
        document.querySelectorAll('.numpad-btn').forEach(btn => btn.disabled = false);
        showPinError('Verbindingsfout');
    }
}

async function setupBiometric() {
    if (!window.PublicKeyCredential) { alert('Niet ondersteund'); return; }
    try {
        const optRes = await fetch('/auth/passkey/register/options', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        });
        if (!optRes.ok) throw new Error('Kon niet starten');
        const options = await optRes.json();

        const credential = await navigator.credentials.create({
            publicKey: {
                challenge: base64urlToBuffer(options.challenge),
                rp: options.rp,
                user: { id: base64urlToBuffer(options.user.id), name: options.user.name, displayName: options.user.displayName },
                pubKeyCredParams: options.pubKeyCredParams,
                timeout: 60000,
                authenticatorSelection: { authenticatorAttachment: 'platform', userVerification: 'required' },
            }
        });

        const regRes = await fetch('/auth/passkey/register', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                id: credential.id,
                rawId: bufferToBase64url(credential.rawId),
                type: credential.type,
                response: {
                    clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                    attestationObject: bufferToBase64url(credential.response.attestationObject),
                },
            }),
        });
        if (regRes.ok) {
            await fetch('/auth/pin/biometric', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ fingerprint: deviceFingerprint }),
            });
            window.location.href = '/';
        }
    } catch (err) {
        if (err.name !== 'NotAllowedError') alert('Biometrie mislukt: ' + err.message);
    }
}

function base64urlToBuffer(b64) {
    const padding = '='.repeat((4 - b64.length % 4) % 4);
    return Uint8Array.from(atob(b64.replace(/-/g, '+').replace(/_/g, '/') + padding), c => c.charCodeAt(0)).buffer;
}
function bufferToBase64url(buf) {
    let str = ''; for (const b of new Uint8Array(buf)) str += String.fromCharCode(b);
    return btoa(str).replace(/\+/g, '-').replace(/\//g, '_').replace(/=/g, '');
}

document.addEventListener('keydown', e => {
    if (document.getElementById('step-pin').classList.contains('hidden')) return;
    if (e.key >= '0' && e.key <= '9') { e.preventDefault(); addSetupPin(e.key); }
    else if (e.key === 'Backspace') { e.preventDefault(); removeSetupPin(); }
    else if (e.key === 'Escape') { e.preventDefault(); goBack(); }
});
</script>
@endpush
