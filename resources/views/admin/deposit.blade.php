<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Deposit Money to {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- User Information -->
                    <div class="mb-6 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">User Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-500 dark:text-gray-400">Name:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $user->name }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 dark:text-gray-400">Email:</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ $user->email }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 dark:text-gray-400">Current Balance:</span>
                                <span class="text-green-600 font-semibold">${{ number_format($user->balance, 2) }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-500 dark:text-gray-400">Status:</span>
                                @if($user->is_verified)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        Unverified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Deposit Form -->
                    <form method="POST" action="{{ route('admin.deposit', $user->id) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Deposit Amount
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" 
                                       name="amount" 
                                       id="amount" 
                                       step="0.01" 
                                       min="0.01" 
                                       required
                                       class="pl-7 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="0.00">
                            </div>
                            @error('amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Back to Users
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Deposit Money
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
