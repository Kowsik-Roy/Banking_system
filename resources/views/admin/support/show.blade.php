<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Support Chat - {{ $conversation->user->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <!-- Action Buttons -->
            <div class="mb-6 flex justify-between items-center">
                <a href="{{ route('admin.support.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to List
                </a>
                <form action="{{ route('admin.support.close', $conversation) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Close Conversation
                    </button>
                </form>
            </div>
            <!-- User Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                                <span class="text-white font-medium text-lg">
                                    {{ strtoupper(substr($conversation->user->name, 0, 1)) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900">{{ $conversation->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $conversation->user->email }}</p>
                            <p class="text-sm text-gray-500">{{ $conversation->user->phone }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-gray-400">Started {{ $conversation->created_at->format('M d, Y H:i') }}</p>
                            <p class="text-xs text-gray-400">{{ $messages->count() }} messages</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Interface -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Messages Container -->
                <div id="messages-container" class="h-96 overflow-y-auto p-4 space-y-4">
                    @foreach($messages as $message)
                        <div class="flex {{ $message->sender_type === 'admin' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_type === 'admin' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                                <div class="text-sm">{{ $message->decrypted_content ?? $message->encrypted_content }}</div>
                                <div class="text-xs mt-1 opacity-75">{{ $message->created_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="p-4 border-t border-gray-200">
                    @if($messages->where('sender_type', 'user')->count() > 0)
                        <form id="message-form" class="space-y-3">
                            @csrf
                            <textarea id="message-input" name="content" 
                                   class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm px-3 py-2 text-gray-900 bg-white resize-none" 
                                   placeholder="Type your response..." maxlength="1000" rows="3" required></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Send
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 text-sm">Waiting for user to send a message...</p>
                            <p class="text-gray-400 text-xs mt-1">You can only reply to messages from users</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        const conversationId = {{ $conversation->id }};
        let lastMessageId = 0;



        // Load messages
        function loadMessages() {
            fetch(`/admin/support/${conversationId}/messages`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('messages-container');
                    container.innerHTML = '';
                    
                    data.messages.forEach(message => {
                        const messageDiv = createMessageElement(message);
                        container.appendChild(messageDiv);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                    
                    container.scrollTop = container.scrollHeight;
                })
                .catch(error => console.error('Error loading messages:', error));
        }

        // Create message element
        function createMessageElement(message) {
            const div = document.createElement('div');
            div.className = `flex ${message.sender_type === 'admin' ? 'justify-end' : 'justify-start'}`;
            
            const messageClass = message.sender_type === 'admin' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-900';
            
            div.innerHTML = `
                <div class="max-w-xs lg:max-md px-4 py-2 rounded-lg ${messageClass}">
                    <div class="text-sm">${message.content}</div>
                    <div class="text-xs mt-1 opacity-75">${message.created_at}</div>
                </div>
            `;
            
            return div;
        }

        // Send message
        const messageForm = document.getElementById('message-form');
        if (messageForm) {
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const input = document.getElementById('message-input');
                const content = input.value.trim();
                
                if (!content) return;
                
                // Get CSRF token from the form
                const csrfToken = document.querySelector('input[name="_token"]').value;
                console.log('Admin sending message:', { content, conversationId, csrfToken });
                
                fetch(`/admin/support/${conversationId}/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ content: content })
                })
                .then(response => {
                    console.log('Admin response status:', response.status);
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.log('Admin error response:', text);
                            throw new Error(`HTTP error! status: ${response.status}, text: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Admin success response:', data);
                    if (data.success) {
                        input.value = '';
                        loadMessages();
                    } else {
                        alert('Error sending message: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert('Error sending message. Please try again. Error: ' + error.message);
                });
            });
        }

        // Load messages initially
        loadMessages();

        // Poll for new messages every 5 seconds
        setInterval(loadMessages, 5000);
    </script>
</x-app-layout>
