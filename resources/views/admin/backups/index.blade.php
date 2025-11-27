<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Backup System
            </h2>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <!-- Disk Space Info -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Disk Space</h3>
        </x-slot>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total</p>
                <p class="font-semibold">{{ number_format($diskSpace['total'] / 1024 / 1024 / 1024, 2) }} GB</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Used</p>
                <p class="font-semibold text-yellow-600">{{ number_format($diskSpace['used'] / 1024 / 1024 / 1024, 2) }} GB</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Free</p>
                <p class="font-semibold text-green-600">{{ number_format($diskSpace['free'] / 1024 / 1024 / 1024, 2) }} GB</p>
            </div>
        </div>
    </x-card>

    <!-- Create Backup -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Create Backup</h3>
        </x-slot>
        <form action="{{ route('admin.backups.create') }}" method="POST">
            @csrf
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <x-form-group label="Backup Type" name="backup_type">
                        <select name="backup_type" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                            <option value="full">Full Backup (Database + Files)</option>
                            <option value="database">Database Only</option>
                            <option value="files">Files Only</option>
                        </select>
                    </x-form-group>
                </div>
                <x-button variant="primary" size="md" type="submit">Create Backup</x-button>
            </div>
        </form>
    </x-card>

    <!-- Backup Files -->
    @if(count($backupFiles) > 0)
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Backup Files</h3>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">File Name</th>
                            <th class="text-left p-3">Size</th>
                            <th class="text-left p-3">Created At</th>
                            <th class="text-right p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backupFiles as $file)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-3 font-medium">{{ $file['name'] }}</td>
                                <td class="p-3">{{ number_format($file['size'] / 1024 / 1024, 2) }} MB</td>
                                <td class="p-3">{{ $file['created_at'] }}</td>
                                <td class="p-3 text-right">
                                    <div class="flex justify-end space-x-2">
                                        <a href="{{ route('admin.backups.download', $file['name']) }}" class="text-primary-600 hover:text-primary-800">
                                            Download
                                        </a>
                                        <form action="{{ route('admin.backups.destroy', $file['name']) }}" method="POST" class="inline" onsubmit="return confirm('Hapus backup ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>No backup files found.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

