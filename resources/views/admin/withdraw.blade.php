<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Withdraw Money
            </h2>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Users
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if(session('success'))
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

            @if(session('error'))
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- User Information Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="bg-gradient-to-r from-red-600 to-pink-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white">User Information</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-red-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-white">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-semibold text-gray-900">{{ $user->name }}</h4>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Current Balance</span>
                                    <span class="text-lg font-bold text-green-600">${{ number_format($user->balance, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Account Status</span>
                                    @if($user->is_verified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Verified
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            Unverified
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                    <span class="text-sm font-medium text-gray-600">Phone</span>
                                    <span class="text-sm text-gray-900">{{ $user->phone ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Withdraw Form Card -->
                <div class="lg:col-span-2">
                    <div class="bg-red-50 overflow-hidden shadow-lg rounded-xl border border-red-200">
                        <div class="bg-red-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white">Withdraw Funds</h3>
                        </div>
                        <div class="p-6">
                            <form method="POST" action="{{ route('admin.withdraw', $user->id) }}" class="space-y-6">
                                @csrf
                                
                                <div>
                                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-3">
                                        Withdraw Amount
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-gray-500 text-lg font-medium">$</span>
                                        </div>
                                        <input type="number" 
                                               name="amount" 
                                               id="amount" 
                                               step="0.01" 
                                               min="0.01" 
                                               max="{{ $user->balance }}"
                                               required
                                               class="block w-full pl-8 pr-4 py-4 text-lg font-medium text-gray-900 bg-gray-50 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 placeholder-gray-400"
                                               placeholder="Enter amount (e.g., 100.00)">
                                    </div>
                                    @error('amount')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>



                                <!-- Transaction Summary -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Transaction Summary</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Account:</span>
                                            <span class="font-medium text-gray-900">{{ $user->name }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Amount:</span>
                                            <span class="font-medium text-gray-900" id="summary-amount">$0.00</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Remaining Balance:</span>
                                            <span class="font-medium text-green-600" id="remaining-balance">${{ number_format($user->balance, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Warning Message -->
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-red-800">Withdrawal Warning</h3>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>This action will permanently remove funds from the user's account. Please ensure the amount is correct before proceeding.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex justify-end space-x-4 pt-4">
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 border border-transparent rounded-xl font-semibold text-white text-sm uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transform hover:scale-105 transition-all duration-200 shadow-lg">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                        Withdraw Money
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateSummary() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const currentBalance = {{ $user->balance }};
            const remainingBalance = Math.max(0, currentBalance - amount);
            
            document.getElementById('summary-amount').textContent = '$' + amount.toFixed(2);
            document.getElementById('remaining-balance').textContent = '$' + remainingBalance.toFixed(2);
        }

        // Update summary when amount changes
        document.getElementById('amount').addEventListener('input', updateSummary);
        
        // Initialize summary
        updateSummary();
    </script>
</x-app-layout>
