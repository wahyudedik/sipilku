<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Tambah Kupon
            </h2>
            <a href="{{ route('admin.coupons.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Tambah Kupon</h3>
                </x-slot>

                <form action="{{ route('admin.coupons.store') }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Kode Kupon</x-slot>
                            <x-text-input type="text" name="code" value="{{ old('code') }}" placeholder="Kosongkan untuk auto-generate" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan untuk generate otomatis</p>
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Nama Kupon</x-slot>
                            <x-text-input type="text" name="name" value="{{ old('name') }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Deskripsi</x-slot>
                            <x-textarea-input name="description" rows="3">{{ old('description') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Tipe</x-slot>
                            <select name="type" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                                <option value="percentage" {{ old('type') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Fixed Amount (Rp)</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Nilai</x-slot>
                            <x-text-input type="number" name="value" value="{{ old('value') }}" step="0.01" min="0" required />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Untuk percentage: 0-100, untuk fixed: jumlah dalam rupiah</p>
                            <x-input-error :messages="$errors->get('value')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Minimum Purchase (Rp)</x-slot>
                            <x-text-input type="number" name="minimum_purchase" value="{{ old('minimum_purchase') }}" step="0.01" min="0" />
                            <x-input-error :messages="$errors->get('minimum_purchase')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Maximum Discount (Rp)</x-slot>
                            <x-text-input type="number" name="maximum_discount" value="{{ old('maximum_discount') }}" step="0.01" min="0" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal diskon yang bisa diberikan (untuk percentage)</p>
                            <x-input-error :messages="$errors->get('maximum_discount')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Usage Limit</x-slot>
                            <x-text-input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Total penggunaan maksimal (kosongkan untuk unlimited)</p>
                            <x-input-error :messages="$errors->get('usage_limit')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Usage Limit Per User</x-slot>
                            <x-text-input type="number" name="usage_limit_per_user" value="{{ old('usage_limit_per_user') }}" min="1" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Penggunaan maksimal per user (kosongkan untuk unlimited)</p>
                            <x-input-error :messages="$errors->get('usage_limit_per_user')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Start Date</x-slot>
                            <x-text-input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" />
                            <x-input-error :messages="$errors->get('starts_at')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Expiry Date</x-slot>
                            <x-text-input type="datetime-local" name="expires_at" value="{{ old('expires_at') }}" />
                            <x-input-error :messages="$errors->get('expires_at')" class="mt-2" />
                        </x-form-group>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Aktif
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.coupons.index') }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

