<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Send Money') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Transfer Form Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Money Transfer</h3>
                    <p class="text-sm text-gray-500 mt-1">Send money securely to other Gganbu Banking users</p>
                </div>
                
                <div class="p-6">
                    <form id="transferForm" method="POST" action="{{ route('transfer.store') }}" class="space-y-6">
                        @csrf

                        <input type="hidden" name="session_id" id="session_id">
                        <input type="hidden" name="client_public" id="client_public">
                        <input type="hidden" name="hmac" id="hmac">
                        <input type="hidden" name="nonce" id="nonce">

                        <!-- Receiver Phone -->
                        <div>
                            <label for="receiver_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Receiver Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input 
                                    id="receiver_phone"
                                    name="receiver_phone" 
                                    type="tel" 
                                    class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="+1234567890"
                                    required
                                >
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Enter the phone number of the recipient</p>
                        </div>

                        <!-- Amount -->
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Transfer Amount
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input 
                                    id="amount"
                                    name="amount" 
                                    type="number" 
                                    min="0.01" 
                                    step="0.01" 
                                    class="pl-7 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="0.00"
                                    required
                                >
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Minimum amount: $0.01</p>
                        </div>

                        <!-- PIN -->
                        <div>
                            <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">
                                Security PIN
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <input 
                                    id="pin"
                                    name="pin" 
                                    type="password" 
                                    maxlength="4" 
                                    class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                    placeholder="••••"
                                    required
                                >
                            </div>
                            <p class="mt-1 text-sm text-gray-500">Enter your 4-digit security PIN</p>
                        </div>

                        <!-- Current Balance Display -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Available Balance</p>
                                    <p class="text-lg font-semibold text-gray-900">${{ number_format(Auth::user()->balance, 2) }}</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-between pt-4">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Send Money
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Secure Transfer</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>This transfer is secured with Diffie-Hellman key exchange and HMAC authentication. Your transaction details are encrypted and verified for security.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
            try {
                const res = await fetch("{{ route('transfer.init') }}", {
                    method: 'POST',
                    headers: { 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });
                
                if (!res.ok) {
                    throw new Error(`HTTP error! status: ${res.status}`);
                }
                
                const data = await res.json();
                console.log('DH Init Response:', data);
                
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
                
                console.log('DH Session initialized successfully');
            } catch (error) {
                console.error('DH Init Error:', error);
                alert('Failed to initialize secure session. Please refresh the page and try again.');
            }
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
