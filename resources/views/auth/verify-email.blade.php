<x-app-layout>
    <div class="max-w-md mx-auto mt-10 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold mb-4 text-center">Email Verification</h2>

        @if (session('success'))
            <div class="mb-4 text-green-600 font-semibold text-center">
                {{ session('success') }}
            </div>
            <div class="text-center">
                <a href="{{ route('dashboard') }}" class="text-indigo-600 hover:underline">Go to Dashboard</a>
            </div>
        @else
            <p class="mb-4 text-sm text-gray-600">
                Please enter the 6-digit verification code sent to your email address.
            </p>

            @if (session('error'))
                <div class="mb-4 text-red-600 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verify.email.code') }}">
                @csrf

                <label for="code" class="block mb-2 text-sm font-medium text-gray-700">Verification Code</label>
                <input type="text" name="code" maxlength="6" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md mb-4 focus:ring-blue-500">

                <button type="submit"
                        class="w-full bg-indigo-600 text-black py-2 rounded hover:bg-indigo-700 transition">
                    Verify
                </button>
            </form>
        @endif
    </div>
</x-app-layout>
