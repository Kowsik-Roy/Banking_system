<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Support Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(!$conversation)
                <!-- No active conversation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Need Help?</h3>
                        <p class="text-gray-500 mb-6">Start a conversation with our support team for assistance.</p>
                        <form action="{{ route('conversations.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Start Conversation
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- Active conversation -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200 bg-gray-50">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">Support Conversation</h3>
                            <form action="{{ route('conversations.close', $conversation) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                                    Close Conversation
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div id="messages-container" class="h-96 overflow-y-auto p-4 space-y-4">
                        <!-- Messages will be loaded here -->
                    </div>

                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200">
                        <form id="message-form" class="space-y-3">
                            @csrf
                            <textarea id="message-input" name="content" 
                                   class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm px-3 py-2 text-gray-900 bg-white resize-none" 
                                   placeholder="Type your message..." maxlength="1000" rows="3" required></textarea>
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                    Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($conversation)
    <script>
        const conversationId = {{ $conversation->id }};
        let lastMessageId = 0;



        // Load messages
        function loadMessages() {
            fetch(`/support/${conversationId}/messages`)
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
            div.className = `flex ${message.sender_type === 'user' ? 'justify-end' : 'justify-start'}`;
            
            const messageClass = message.sender_type === 'user' 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-200 text-gray-900';
            
            div.innerHTML = `
                <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg ${messageClass}">
                    <div class="text-sm">${message.content}</div>
                    <div class="text-xs mt-1 opacity-75">${message.created_at}</div>
                </div>
            `;
            
            return div;
        }

        // Send message
        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const input = document.getElementById('message-input');
            const content = input.value.trim();
            
            if (!content) return;
            
            // Get CSRF token from the form
            const csrfToken = document.querySelector('input[name="_token"]').value;
            console.log('CSRF Token:', csrfToken);
            console.log('Content:', content);
            console.log('Conversation ID:', conversationId);
            
            fetch(`/support/${conversationId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ content: content })
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.log('Error response text:', text);
                        throw new Error(`HTTP error! status: ${response.status}, text: ${text}`);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Success response:', data);
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

        // Load messages initially
        loadMessages();

        // Poll for new messages every 5 seconds
        setInterval(loadMessages, 5000);
    </script>
    @endif
</x-app-layout>
