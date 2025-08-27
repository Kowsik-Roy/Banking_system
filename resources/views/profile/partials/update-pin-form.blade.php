<section>
    <header class="mb-6">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ __('Update Security PIN') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('Change your 4-digit security PIN for secure transactions.') }}
                </p>
            </div>
        </div>
    </header>

    <form method="post" action="{{ route('profile.update-pin') }}" class="space-y-6">
        @csrf
        @method('patch')

        <!-- Current PIN -->
        <div>
            <label for="current_pin" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Current PIN') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input 
                    id="current_pin" 
                    name="current_pin" 
                    type="password" 
                    maxlength="4" 
                    pattern="[0-9]{4}"
                    class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="••••"
                    required
                >
            </div>
            @error('current_pin')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- New PIN -->
        <div>
            <label for="new_pin" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('New PIN') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input 
                    id="new_pin" 
                    name="new_pin" 
                    type="password" 
                    maxlength="4" 
                    pattern="[0-9]{4}"
                    class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="••••"
                    required
                >
            </div>
            @error('new_pin')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm New PIN -->
        <div>
            <label for="new_pin_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Confirm New PIN') }}
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <input 
                    id="new_pin_confirmation" 
                    name="new_pin_confirmation" 
                    type="password" 
                    maxlength="4" 
                    pattern="[0-9]{4}"
                    class="pl-10 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="••••"
                    required
                >
            </div>
            @error('new_pin_confirmation')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- PIN Security Notice -->
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-purple-800">PIN Security</h3>
                    <div class="mt-2 text-sm text-purple-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>Your PIN is used for secure money transfers</li>
                            <li>Keep your PIN confidential and don't share it</li>
                            <li>Choose a PIN that's easy to remember but hard to guess</li>
                            <li>You'll need this PIN for all financial transactions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between pt-4">
            <div class="flex items-center">
                @if (session('status') === 'pin-updated')
                    <div class="flex items-center text-sm text-green-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        {{ __('PIN updated successfully!') }}
                    </div>
                @endif
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                {{ __('Update PIN') }}
            </button>
        </div>
    </form>
</section>

