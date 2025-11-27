<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Preview Email Template
            </h2>
            <a href="{{ route('admin.email-templates.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">{{ $emailTemplate->name }}</h3>
                </x-slot>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Subject:</p>
                        <p class="font-semibold">{{ $rendered['subject'] }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Body:</p>
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="prose dark:prose-invert max-w-none">
                                {!! nl2br(e($rendered['body'])) !!}
                            </div>
                        </div>
                    </div>

                    @if($sampleVariables)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Sample Variables Used:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($sampleVariables as $key => $value)
                                    <x-badge variant="info" size="sm">{{ $key }}: {{ $value }}</x-badge>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

