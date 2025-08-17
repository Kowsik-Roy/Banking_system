<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Transfer Money') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto">
        @if(session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('transfer.store') }}" class="bg-white dark:bg-gray-800 shadow p-6 rounded-lg space-y-4">
            @csrf

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Receiver Phone</label>
                <input type="text" name="phone" id="phone" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
                <input type="number" name="amount" id="amount" required min="1"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Your 4-Digit PIN</label>
                <input type="password" name="pin" id="pin" required maxlength="4"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Send Money
            </button>
        </form>
    </div>
</x-app-layout>
