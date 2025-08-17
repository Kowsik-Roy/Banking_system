<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Send Money</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        @if (session('error'))
            <div class="p-3 mb-4 bg-red-100 text-red-700 rounded">{{ session('error') }}</div>
        @endif
        @if (session('success'))
            <div class="p-3 mb-4 bg-green-100 text-green-700 rounded">{{ session('success') }}</div>
        @endif

        <form id="transferForm" method="POST" action="{{ route('transfer.store') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="session_id" id="session_id">
            <input type="hidden" name="client_public" id="client_public">
            <input type="hidden" name="hmac" id="hmac">
            <input type="hidden" name="nonce" id="nonce">

            <div>
                <label class="block text-sm font-medium">Receiver Phone (account #)</label>
                <input name="receiver_phone" class="mt-1 w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Amount</label>
                <input name="amount" type="number" min="0.01" step="0.01" class="mt-1 w-full border rounded p-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">PIN</label>
                <input name="pin" type="password" maxlength="4" class="mt-1 w-full border rounded p-2" required>
                <small class="text-gray-500">4 digits</small>
            </div>

            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Send</button>
        </form>
    </div>

    <script>
        // BigInt powmod (fast exponentiation)
        function modPow(base, exp, mod) {
            base = base % mod;
            let result = 1n;
            while (exp > 0n) {
                if (exp & 1n) result = (result * base) % mod;
                base = (base * base) % mod;
                exp >>= 1n;
            }
            return result;
        }

        // Convert hex <-> BigInt
        const hexToBigInt = (h) => BigInt('0x' + h);
        const bigIntToHex = (b) => b.toString(16);

        // WebCrypto helpers
        async function sha256(bytes) {
            const digest = await crypto.subtle.digest('SHA-256', bytes);
            return new Uint8Array(digest);
        }
        async function hmacSha256(keyBytes, message) {
            const key = await crypto.subtle.importKey('raw', keyBytes, {name:'HMAC', hash:'SHA-256'}, false, ['sign']);
            const sig = await crypto.subtle.sign('HMAC', key, message);
            return new Uint8Array(sig);
        }
        function utf8(s){ return new TextEncoder().encode(s); }
        function b64(bytes){ return btoa(String.fromCharCode(...bytes)); }

        const form = document.getElementById('transferForm');
        const sessionInput = document.getElementById('session_id');
        const clientPublicInput = document.getElementById('client_public');
        const hmacInput = document.getElementById('hmac');
        const nonceInput = document.getElementById('nonce');

        let dhState = null; // {p,g,serverPublic,clientPriv,sharedKey,session_id,nonce}

        // Step A: init DH session from server on page load
        (async function initDH() {
            const res = await fetch("{{ route('transfer.init') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await res.json();
            const p = BigInt(data.p);
            const g = 2n;
            const serverPub = hexToBigInt(data.serverPublic);

            // client private (128-bit demo)
            const rand = crypto.getRandomValues(new Uint32Array(4));
            let x = 0n;
            for (let i=0;i<rand.length;i++) x = (x<<32n) | BigInt(rand[i]);

            const clientPub = modPow(g, x, p);
            // shared = serverPub^x mod p
            const shared = modPow(serverPub, x, p);
            // derive 32-byte key via SHA-256(hex(shared))
            const sharedHex = bigIntToHex(shared);
            const sharedKey = await sha256(utf8(sharedHex));

            dhState = {
                p, g, serverPub, clientPriv: x, clientPub,
                sharedKey, session_id: data.session_id, nonce: data.nonce
            };

            sessionInput.value = data.session_id;
            clientPublicInput.value = bigIntToHex(clientPub);
            nonceInput.value = data.nonce;
        })();

        form.addEventListener('submit', async (e) => {
            if (!dhState) {
                e.preventDefault();
                alert('Secure session not ready. Please try again.');
                return;
            }
            // Build plaintext payload to HMAC
            const receiver = form.receiver_phone.value;
            const amount = Number(form.amount.value).toFixed(2);
            const payload = `${receiver}|${amount}|${dhState.nonce}`;

            const sig = await hmacSha256(dhState.sharedKey, utf8(payload));
            hmacInput.value = b64(sig);
        });
    </script>
</x-app-layout>
