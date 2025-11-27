<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Email Template Management
            </h2>
            <a href="{{ route('admin.email-templates.create') }}">
                <x-button variant="primary" size="sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Template
                </x-button>
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.email-templates.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari template..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="type" 
                    :options="[
                        'all' => 'Semua Tipe',
                        'email' => 'Email',
                        'notification' => 'Notification',
                        'sms' => 'SMS'
                    ]" 
                    value="{{ request('type', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'active' => 'Active',
                        'inactive' => 'Inactive'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.email-templates.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($templates->count() > 0)
        <div class="space-y-4">
            @foreach($templates as $template)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $template->name }}
                                </h3>
                                <x-badge :variant="$template->is_active ? 'success' : 'default'" size="sm">
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </x-badge>
                                <x-badge variant="info" size="sm">{{ ucfirst($template->type) }}</x-badge>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <strong>Subject:</strong> {{ $template->subject }}
                            </p>
                            @if($template->variables)
                                <div class="flex flex-wrap gap-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Variables:</span>
                                    @foreach($template->variables as $var)
                                        <x-badge variant="secondary" size="xs">{{ $var }}</x-badge>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.email-templates.preview', $template) }}" class="text-primary-600 hover:text-primary-800">
                                Preview
                            </a>
                            <a href="{{ route('admin.email-templates.edit', $template) }}" class="text-primary-600 hover:text-primary-800">
                                Edit
                            </a>
                            <form action="{{ route('admin.email-templates.destroy', $template) }}" method="POST" class="inline" onsubmit="return confirm('Hapus template ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>Tidak ada template ditemukan.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

