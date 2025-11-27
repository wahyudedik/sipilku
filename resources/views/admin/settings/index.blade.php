<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                System Configuration
            </h2>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <!-- Group Tabs -->
    <x-card class="mb-6">
        <div class="flex space-x-2 border-b">
            @foreach($groups as $groupName)
                <a href="{{ route('admin.settings.index', ['group' => $groupName]) }}" 
                   class="px-4 py-2 {{ $group === $groupName ? 'border-b-2 border-primary-500 text-primary-600 font-semibold' : 'text-gray-600 hover:text-primary-600' }}">
                    {{ ucfirst($groupName) }}
                </a>
            @endforeach
        </div>
    </x-card>

    <form action="{{ route('admin.settings.update') }}" method="POST">
        @csrf
        <input type="hidden" name="group" value="{{ $group }}">

        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">{{ ucfirst($group) }} Settings</h3>
                </div>
            </x-slot>

            <div class="space-y-6">
                @forelse($settings as $setting)
                    <div class="border-b pb-4 last:border-b-0">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ str_replace('_', ' ', ucwords($setting->key, '_')) }}
                                </label>
                                @if($setting->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $setting->description }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                        <input type="hidden" name="settings[{{ $loop->index }}][type]" value="{{ $setting->type }}">

                        @if($setting->type === 'boolean' || $setting->type === 'bool')
                            <div class="mt-2">
                                <input type="checkbox" 
                                       name="settings[{{ $loop->index }}][value]" 
                                       value="1"
                                       {{ $setting->value === '1' || $setting->value === 'true' ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            </div>
                        @elseif($setting->type === 'text')
                            <textarea name="settings[{{ $loop->index }}][value]" 
                                      rows="4"
                                      class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">{{ $setting->value }}</textarea>
                        @elseif($setting->type === 'json' || $setting->type === 'array')
                            <textarea name="settings[{{ $loop->index }}][value]" 
                                      rows="6"
                                      class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 font-mono text-sm">{{ is_string($setting->value) ? $setting->value : json_encode($setting->value, JSON_PRETTY_PRINT) }}</textarea>
                        @else
                            <input type="{{ $setting->type === 'integer' || $setting->type === 'int' ? 'number' : ($setting->type === 'float' ? 'number' : 'text') }}" 
                                   name="settings[{{ $loop->index }}][value]" 
                                   value="{{ $setting->value }}"
                                   step="{{ $setting->type === 'float' ? '0.01' : '1' }}"
                                   class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <p>No settings found for this group.</p>
                    </div>
                @endforelse

                @if($settings->count() > 0)
                    <div class="flex justify-end mt-6">
                        <x-button variant="primary" size="md" type="submit">Save Settings</x-button>
                    </div>
                @endif
            </div>
        </x-card>
    </form>
</x-app-with-sidebar>

