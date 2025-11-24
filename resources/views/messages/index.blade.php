<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Pesan
        </h2>
    </x-slot>

    @if($conversationsWithDetails->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($conversationsWithDetails as $conversation)
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" 
                        onclick="window.location='{{ route('messages.chat', $conversation['user']) }}'">
                    <div class="flex items-start space-x-4">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if($conversation['user']->avatar)
                                <img src="{{ Storage::url($conversation['user']->avatar) }}" 
                                     alt="{{ $conversation['user']->name }}"
                                     class="w-12 h-12 rounded-full object-cover">
                            @else
                                <div class="w-12 h-12 rounded-full bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-600 dark:text-gray-300 font-medium">
                                        {{ strtoupper(substr($conversation['user']->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            @if($conversation['unread_count'] > 0)
                                <span class="absolute -mt-2 ml-8 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ $conversation['unread_count'] }}
                                </span>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $conversation['user']->name }}
                                </h3>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $conversation['last_message_at']->diffForHumans() }}
                                </span>
                            </div>
                            @if($conversation['last_message'])
                                <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                    @if($conversation['last_message']->sender_id === auth()->id())
                                        <span class="text-gray-500">Anda: </span>
                                    @endif
                                    @if($conversation['last_message']->attachments && count($conversation['last_message']->attachments) > 0)
                                        <span class="text-primary-600">📎 {{ count($conversation['last_message']->attachments) }} file</span>
                                    @else
                                        {{ Str::limit($conversation['last_message']->message, 100) }}
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada pesan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum memiliki percakapan.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

