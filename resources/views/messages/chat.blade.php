<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" 
                         alt="{{ $user->name }}"
                         class="w-10 h-10 rounded-full object-cover">
                @else
                    <div class="w-10 h-10 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                        <span class="text-gray-600 dark:text-gray-300 font-medium">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        {{ $user->name }}
                    </h2>
                    @if($order)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Order: {{ $order->orderable->title ?? 'N/A' }}
                        </p>
                    @endif
                </div>
            </div>
            <a href="{{ route('messages.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="flex flex-col h-[calc(100vh-200px)]">
        <!-- Messages Container -->
        <div id="messagesContainer" class="flex-1 overflow-y-auto space-y-4 mb-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            @foreach($messages as $message)
                <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%] {{ $message->sender_id === auth()->id() ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100' }} rounded-lg p-3 shadow-sm">
                        @if($message->sender_id !== auth()->id())
                            <p class="text-xs font-medium mb-1 opacity-75">
                                {{ $message->sender->name }}
                            </p>
                        @endif
                        <p class="whitespace-pre-wrap">{{ $message->message }}</p>
                        
                        @if($message->attachments && count($message->attachments) > 0)
                            <div class="mt-2 space-y-2">
                                @foreach($message->attachments as $index => $attachment)
                                    <div class="flex items-center space-x-2 p-2 bg-black bg-opacity-10 dark:bg-white dark:bg-opacity-10 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <a href="{{ route('messages.download-attachment', ['message' => $message, 'index' => $index]) }}" 
                                           class="text-sm hover:underline">
                                            {{ $attachment['name'] }}
                                        </a>
                                        <span class="text-xs opacity-75">
                                            ({{ number_format($attachment['size'] / 1024, 2) }} KB)
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <p class="text-xs mt-2 opacity-75">
                            {{ $message->created_at->format('d M Y H:i') }}
                            @if($message->sender_id === auth()->id() && $message->is_read)
                                <span class="ml-2">✓✓</span>
                            @endif
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Form -->
        <form id="messageForm" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-2">
            @csrf
            <input type="hidden" name="receiver_id" value="{{ $user->id }}">
            @if($order)
                <input type="hidden" name="order_id" value="{{ $order->id }}">
            @endif

            <div class="flex-1">
                <x-textarea-input 
                    name="message" 
                    id="messageInput"
                    rows="2" 
                    placeholder="Ketik pesan..." 
                    class="w-full"
                    required></x-textarea-input>
            </div>

            <div class="flex flex-col space-y-2">
                <label class="cursor-pointer">
                    <input type="file" 
                           name="attachments[]" 
                           id="attachmentsInput"
                           multiple 
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.7z,.jpg,.jpeg,.png,.gif,.webp,.dwg,.skp,.rvt"
                           class="hidden">
                    <x-button variant="secondary" size="sm" type="button" onclick="document.getElementById('attachmentsInput').click()">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                    </x-button>
                </label>
                <x-button variant="primary" size="sm" type="submit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                </x-button>
            </div>
        </form>

        <!-- Attachments Preview -->
        <div id="attachmentsPreview" class="mt-2 hidden">
            <div class="flex flex-wrap gap-2">
                <!-- Preview items will be added here by JavaScript -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Setup Echo for real-time messaging
        const userId = {{ auth()->id() }};
        const otherUserId = {{ $user->id }};
        const channelName = 'chat.' + Math.min(userId, otherUserId) + '.' + Math.max(userId, otherUserId);

        // Only setup Echo if it's available
        if (window.Echo) {
            Echo.private(channelName)
            .listen('.message.sent', (e) => {
                // Add new message to chat
                const messagesContainer = document.getElementById('messagesContainer');
                const isOwnMessage = e.message.sender_id === userId;
                
                const messageDiv = document.createElement('div');
                messageDiv.className = `flex ${isOwnMessage ? 'justify-end' : 'justify-start'}`;
                messageDiv.innerHTML = `
                    <div class="max-w-[70%] ${isOwnMessage ? 'bg-primary-600 text-white' : 'bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100'} rounded-lg p-3 shadow-sm">
                        ${!isOwnMessage ? `<p class="text-xs font-medium mb-1 opacity-75">${e.message.sender.name}</p>` : ''}
                        <p class="whitespace-pre-wrap">${e.message.message || ''}</p>
                        ${e.message.attachments && e.message.attachments.length > 0 ? `
                            <div class="mt-2 space-y-2">
                                ${e.message.attachments.map((attachment, index) => `
                                    <div class="flex items-center space-x-2 p-2 bg-black bg-opacity-10 dark:bg-white dark:bg-opacity-10 rounded">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span class="text-sm">${attachment.name}</span>
                                        <span class="text-xs opacity-75">(${(attachment.size / 1024).toFixed(2)} KB)</span>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                        <p class="text-xs mt-2 opacity-75">
                            ${new Date(e.message.created_at).toLocaleString('id-ID')}
                            ${isOwnMessage ? '<span class="ml-2">✓</span>' : ''}
                        </p>
                    </div>
                `;
                
                messagesContainer.appendChild(messageDiv);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;

                // Mark as read if it's not our message
                if (!isOwnMessage) {
                    fetch('{{ route("messages.mark-read") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            message_ids: [e.message.id]
                        })
                    });
                }
            });
        } else {
            console.warn('Laravel Echo not available. Real-time messaging disabled.');
        }

        // Handle form submission with AJAX
        document.getElementById('messageForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const messageInput = document.getElementById('messageInput');
            const attachmentsInput = document.getElementById('attachmentsInput');
            
            // Check if message or attachments exist
            if (!messageInput.value.trim() && attachmentsInput.files.length === 0) {
                return;
            }

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messageInput.value = '';
                    attachmentsInput.value = '';
                    document.getElementById('attachmentsPreview').classList.add('hidden');
                    document.getElementById('attachmentsPreview').innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });

        // Handle attachments preview
        document.getElementById('attachmentsInput').addEventListener('change', function(e) {
            const preview = document.getElementById('attachmentsPreview');
            preview.innerHTML = '';
            
            if (this.files.length > 0) {
                preview.classList.remove('hidden');
                
                Array.from(this.files).forEach((file, index) => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center space-x-2 p-2 bg-gray-100 dark:bg-gray-700 rounded';
                    div.innerHTML = `
                        <span class="text-sm">${file.name}</span>
                        <span class="text-xs text-gray-500">(${(file.size / 1024).toFixed(2)} KB)</span>
                    `;
                    preview.querySelector('div').appendChild(div);
                });
            } else {
                preview.classList.add('hidden');
            }
        });

        // Scroll to bottom on load
        window.addEventListener('load', function() {
            const messagesContainer = document.getElementById('messagesContainer');
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        });
    </script>
    @endpush
</x-app-with-sidebar>

