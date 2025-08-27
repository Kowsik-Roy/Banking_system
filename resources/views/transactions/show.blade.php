<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Transaction Details') }}
            </h2>
            <a href="{{ route('transactions.index') }}" 
               class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to History
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Transaction Overview -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Transaction Overview</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Transaction ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Date & Time</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $transaction->created_at->format('F d, Y \a\t g:i A') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Amount</dt>
                                    <dd class="mt-1 text-2xl font-bold text-gray-900">${{ number_format($transaction->amount, 2) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="mt-1">
                                        @if($transaction->sender_id === Auth::id())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                                Sent
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                </svg>
                                                Received
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                                <circle cx="4" cy="4" r="3"/>
                                            </svg>
                                            {{ $transaction->status ?? 'Completed' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">From</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">{{ substr($transaction->sender->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $transaction->sender->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $transaction->sender->phone }}</p>
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">To</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-green-600">{{ substr($transaction->receiver->name, 0, 1) }}</span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $transaction->receiver->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $transaction->receiver->phone }}</p>
                                            </div>
                                        </div>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Session ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $transaction->session_id }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Security Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">HMAC Signature</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono break-all">{{ $transaction->hmac }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Shared Key (Encrypted)</dt>
                                    <dd class="mt-1 text-sm text-gray-900 font-mono break-all">{{ substr($transaction->shared_key, 0, 50) }}...</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-blue-800">Secure Transaction</h3>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>This transaction was secured using Diffie-Hellman key exchange and HMAC authentication. All transaction data is encrypted and verified for integrity.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ledger Entries -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ledger Entries</h3>
                </div>
                <div class="p-6">
                    @if($transaction->ledgerEntries->count() > 0)
                        <div class="space-y-4">
                            @foreach($transaction->ledgerEntries as $entry)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center {{ $entry->entry_type === 'debit' ? 'bg-red-100' : 'bg-green-100' }}">
                                                <span class="text-sm font-medium {{ $entry->entry_type === 'debit' ? 'text-red-600' : 'text-green-600' }}">
                                                    {{ strtoupper(substr($entry->entry_type, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ ucfirst($entry->entry_type) }} - {{ $entry->user->name }}
                                                </p>
                                                <p class="text-sm text-gray-500">Amount: ${{ number_format($entry->amount, 2) }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">Hash</p>
                                            <p class="text-xs font-mono text-gray-900 break-all">{{ $entry->hash }}</p>
                                        </div>
                                    </div>
                                    @if($entry->prev_hash)
                                        <div class="mt-3 pt-3 border-t border-gray-100">
                                            <p class="text-xs text-gray-500">Previous Hash: <span class="font-mono">{{ $entry->prev_hash }}</span></p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="mt-2 text-sm">No ledger entries found</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
