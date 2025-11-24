<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Pengaturan Notifikasi
            </h2>
            <a href="{{ route('notifications.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <x-card>
        <form action="{{ route('notifications.update-preferences') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                        Pilih jenis notifikasi yang ingin Anda terima
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Anda dapat mengatur apakah ingin menerima notifikasi via email dan/atau in-app untuk setiap jenis notifikasi.
                    </p>
                </div>

                @foreach($notificationTypes as $type => $label)
                    @php
                        $preference = $preferences->get($type) ?? new \App\Models\NotificationPreference([
                            'type' => $type,
                            'email_enabled' => true,
                            'database_enabled' => true,
                        ]);
                    @endphp
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 dark:text-gray-100 mb-3">{{ $label }}</h4>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="hidden" name="preferences[{{ $type }}][email_enabled]" value="0">
                                <input type="checkbox" 
                                       name="preferences[{{ $type }}][email_enabled]" 
                                       value="1"
                                       {{ $preference->email_enabled ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Email</span>
                            </label>
                            <label class="flex items-center">
                                <input type="hidden" name="preferences[{{ $type }}][database_enabled]" value="0">
                                <input type="checkbox" 
                                       name="preferences[{{ $type }}][database_enabled]" 
                                       value="1"
                                       {{ $preference->database_enabled ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">In-App (Notifikasi di aplikasi)</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end">
                    <x-button variant="primary" size="md" type="submit">
                        Simpan Pengaturan
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>
</x-app-with-sidebar>

