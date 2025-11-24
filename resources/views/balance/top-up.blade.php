<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Top-up Saldo
            </h2>
            <a href="{{ route('balance.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Top-up Form -->
        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Isi Saldo</h3>
                </x-slot>
                <form id="topUpForm">
                    @csrf
                    <div class="space-y-6">
                        <!-- Current Balance -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Saldo Saat Ini</p>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Amount Input -->
                        <div>
                            <x-form-group>
                                <x-slot name="label">Jumlah Top-up</x-slot>
                                <x-text-input 
                                    type="number" 
                                    name="amount" 
                                    id="amount" 
                                    placeholder="Masukkan jumlah top-up"
                                    min="10000"
                                    max="10000000"
                                    step="1000"
                                    required />
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Minimum: Rp 10.000 | Maximum: Rp 10.000.000
                                </p>
                            </x-form-group>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Pilih Jumlah Cepat:</p>
                            <div class="grid grid-cols-4 gap-2">
                                <button type="button" class="quick-amount-btn px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm" data-amount="50000">
                                    Rp 50.000
                                </button>
                                <button type="button" class="quick-amount-btn px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm" data-amount="100000">
                                    Rp 100.000
                                </button>
                                <button type="button" class="quick-amount-btn px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm" data-amount="250000">
                                    Rp 250.000
                                </button>
                                <button type="button" class="quick-amount-btn px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 text-sm" data-amount="500000">
                                    Rp 500.000
                                </button>
                            </div>
                        </div>

                        <!-- Payment Method Info -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Metode Pembayaran:</strong> Pembayaran akan diproses melalui Midtrans dengan berbagai pilihan metode pembayaran (Kartu Kredit/Debit, E-Wallet, Virtual Account).
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div>
                            <x-button variant="primary" size="lg" type="submit" class="w-full" id="submitBtn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                Lanjutkan Pembayaran
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Info Sidebar -->
        <div class="space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Informasi</h3>
                </x-slot>
                <div class="space-y-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100 mb-1">Keuntungan Saldo:</p>
                        <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                            <li>Pembayaran lebih cepat</li>
                            <li>Tidak perlu input data setiap transaksi</li>
                            <li>Dapat digunakan untuk semua produk</li>
                        </ul>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-gray-100 mb-1">Cara Top-up:</p>
                        <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                            <li>Masukkan jumlah top-up</li>
                            <li>Klik "Lanjutkan Pembayaran"</li>
                            <li>Pilih metode pembayaran</li>
                            <li>Saldo akan langsung ditambahkan setelah pembayaran berhasil</li>
                        </ol>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Payment Processing Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
                <p class="text-gray-600 dark:text-gray-400">Memproses pembayaran...</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('topUpForm');
            const amountInput = document.getElementById('amount');
            const submitBtn = document.getElementById('submitBtn');
            const paymentModal = document.getElementById('paymentModal');
            const quickAmountBtns = document.querySelectorAll('.quick-amount-btn');

            // Quick amount buttons
            quickAmountBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const amount = this.dataset.amount;
                    amountInput.value = amount;
                });
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const amount = parseFloat(amountInput.value);

                if (!amount || amount < 10000 || amount > 10000000) {
                    alert('Jumlah top-up harus antara Rp 10.000 - Rp 10.000.000');
                    return;
                }

                // Show loading
                paymentModal.classList.remove('hidden');
                submitBtn.disabled = true;

                // Process top-up
                fetch('{{ route("balance.process-top-up") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ amount: amount })
                })
                .then(response => response.json())
                .then(data => {
                    paymentModal.classList.add('hidden');
                    
                    if (data.success && data.snap_token) {
                        // Embed Snap.js
                        snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                window.location.href = '{{ route("balance.index") }}?status=success';
                            },
                            onPending: function(result) {
                                window.location.href = '{{ route("balance.index") }}?status=pending';
                            },
                            onError: function(result) {
                                window.location.href = '{{ route("balance.index") }}?status=error';
                            },
                            onClose: function() {
                                paymentModal.classList.add('hidden');
                                submitBtn.disabled = false;
                            }
                        });
                    } else {
                        alert('Terjadi kesalahan saat memproses top-up. Silakan coba lagi.');
                        submitBtn.disabled = false;
                    }
                })
                .catch(error => {
                    paymentModal.classList.add('hidden');
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat memproses top-up. Silakan coba lagi.');
                    submitBtn.disabled = false;
                });
            });
        });
    </script>
    @endpush
</x-app-with-sidebar>

