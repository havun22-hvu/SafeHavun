@extends('layouts.guest')

@section('title', 'QR Scan')

@section('content')
<div class="text-center">
    <h3 class="text-lg font-medium text-white mb-2">Login bevestigen</h3>
    <p class="text-sm text-gray-400 mb-6">Wil je inloggen op een ander apparaat?</p>

    <div id="qr-info" class="mb-6 p-4 bg-white/5 rounded-lg">
        <p class="text-sm text-gray-300">Browser: <span id="device-browser">Onbekend</span></p>
        <p class="text-sm text-gray-300">OS: <span id="device-os">Onbekend</span></p>
    </div>

    <div id="status-pending">
        <button onclick="approveLogin()" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors mb-3">
            Ja, log in
        </button>
        <button onclick="window.close(); window.history.back();" class="w-full py-3 px-4 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
            Annuleren
        </button>
    </div>

    <div id="status-success" class="hidden">
        <svg class="w-16 h-16 mx-auto text-emerald-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-emerald-400">Login bevestigd!</p>
        <p class="text-sm text-gray-400 mt-2">Het andere apparaat wordt nu ingelogd.</p>
    </div>

    <div id="status-error" class="hidden">
        <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p id="error-message" class="text-red-400">QR code ongeldig of verlopen</p>
        <a href="/" class="inline-block mt-4 text-emerald-400 hover:underline">Terug naar home</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
const urlParams = new URLSearchParams(window.location.search);
const token = urlParams.get('token');

if (!token || token.length !== 64) {
    document.getElementById('status-pending').classList.add('hidden');
    document.getElementById('status-error').classList.remove('hidden');
}

async function approveLogin() {
    try {
        const res = await fetch('/auth/qr/approve', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ token }),
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('status-pending').classList.add('hidden');
            document.getElementById('status-success').classList.remove('hidden');
        } else {
            document.getElementById('error-message').textContent = data.message || 'QR code ongeldig of verlopen';
            document.getElementById('status-pending').classList.add('hidden');
            document.getElementById('status-error').classList.remove('hidden');
        }
    } catch (err) {
        document.getElementById('status-pending').classList.add('hidden');
        document.getElementById('status-error').classList.remove('hidden');
    }
}
</script>
@endpush
