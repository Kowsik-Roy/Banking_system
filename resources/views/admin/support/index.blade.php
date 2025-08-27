<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Support Conversations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($conversations->count() > 0)
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex justify-between items-start">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <span class="text-blue-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h3 class="font-semibold text-gray-900">{{ $conversation->user->name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $conversation->user->email }}</p>
                                                <p class="text-xs text-gray-500">Started {{ $conversation->created_at->format('M d, Y H:i') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-gray-600">
                                                {{ $conversation->messages->count() }} messages
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $conversation->messages->last() ? $conversation->messages->last()->created_at->format('M d, Y H:i') : 'No messages' }}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($conversation->messages->count() > 0)
                                        <div class="mt-3 p-3 bg-gray-50 rounded text-sm text-gray-700">
                                            <strong>Latest message:</strong> 
                                            {{ Str::limit($conversation->messages->last()->sender_type === 'user' ? 'User sent a message' : 'Admin replied', 100) }}
                                        </div>
                                    @endif
                                    
                                    <div class="mt-4 flex justify-end">
                                        <a href="{{ route('admin.support.show', $conversation) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                            Open Chat
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg">No open support conversations</div>
                            <p class="text-gray-400 mt-2">When users start support chats, they will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
