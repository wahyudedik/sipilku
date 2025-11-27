<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Edit Email Template
            </h2>
            <a href="{{ route('admin.email-templates.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Edit Email Template</h3>
                </x-slot>

                <form action="{{ route('admin.email-templates.update', $emailTemplate) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Template Name</x-slot>
                            <x-text-input type="text" name="name" value="{{ old('name', $emailTemplate->name) }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Type</x-slot>
                            <select name="type" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                                <option value="email" {{ old('type', $emailTemplate->type) === 'email' ? 'selected' : '' }}>Email</option>
                                <option value="notification" {{ old('type', $emailTemplate->type) === 'notification' ? 'selected' : '' }}>Notification</option>
                                <option value="sms" {{ old('type', $emailTemplate->type) === 'sms' ? 'selected' : '' }}>SMS</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Subject</x-slot>
                            <x-text-input type="text" name="subject" value="{{ old('subject', $emailTemplate->subject) }}" required />
                            <x-input-error :messages="$errors->get('subject')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Body</x-slot>
                            <textarea name="body" rows="15" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500 font-mono text-sm" required>{{ old('body', $emailTemplate->body) }}</textarea>
                            <x-input-error :messages="$errors->get('body')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Available Variables (comma separated)</x-slot>
                            <x-text-input type="text" name="variables_string" value="{{ old('variables_string', $emailTemplate->variables ? implode(', ', $emailTemplate->variables) : '') }}" />
                        </x-form-group>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Aktif
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.email-templates.index') }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const variablesInput = document.querySelector('input[name="variables_string"]');
            if (variablesInput && variablesInput.value) {
                const variables = variablesInput.value.split(',').map(v => v.trim()).filter(v => v);
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'variables';
                hiddenInput.value = JSON.stringify(variables);
                this.appendChild(hiddenInput);
            }
        });
    </script>
    @endpush
</x-app-with-sidebar>

