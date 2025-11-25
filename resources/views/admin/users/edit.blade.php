<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Edit User
            </h2>
            <a href="{{ route('admin.users.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Edit User: {{ $user->name }}</h3>
                </x-slot>

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Dasar</h4>
                            <div class="space-y-4">
                                <x-form-group>
                                    <x-slot name="label">Nama</x-slot>
                                    <x-text-input type="text" name="name" value="{{ old('name', $user->name) }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Email</x-slot>
                                    <x-text-input type="email" name="email" value="{{ old('email', $user->email) }}" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Phone</x-slot>
                                    <x-text-input type="text" name="phone" value="{{ old('phone', $user->phone) }}" />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Password (Kosongkan jika tidak ingin mengubah)</x-slot>
                                    <x-text-input type="password" name="password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Konfirmasi Password</x-slot>
                                    <x-text-input type="password" name="password_confirmation" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Status</h4>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="is_active" value="1" 
                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Aktif
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_seller" id="is_seller" value="1" 
                                           {{ old('is_seller', $user->is_seller) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                    <label for="is_seller" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        Seller
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Roles</h4>
                            <div class="space-y-2">
                                @foreach($roles as $role)
                                    <div class="flex items-center">
                                        <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->name }}" 
                                               {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <label for="role_{{ $role->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                            {{ ucfirst($role->name) }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.users.index') }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan Perubahan</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

