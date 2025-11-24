<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Proses Pembayaran
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card class="mb-6">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary-600 mx-auto mb-4"></div>
                    <p class="text-gray-600 dark:text-gray-400">Memproses pembayaran...</p>
                </div>
            </x-card>

            <x-card id="paymentForm" style="display: none;">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Pembayaran Online</h3>
                </x-slot>
                <div id="snap-container"></div>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderUuid = '{{ $order->uuid }}';
            
            // Process payment
            fetch('{{ route("payments.process", $order) }}', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.snap_token) {
                    document.getElementById('paymentForm').style.display = 'block';
                    
                    // Embed Snap.js
                    snap.pay(data.snap_token, {
                        onSuccess: function(result) {
                            window.location.href = '{{ route("payments.status", $order) }}?status=success';
                        },
                        onPending: function(result) {
                            window.location.href = '{{ route("payments.status", $order) }}?status=pending';
                        },
                        onError: function(result) {
                            window.location.href = '{{ route("payments.status", $order) }}?status=error';
                        },
                        onClose: function() {
                            window.location.href = '{{ route("orders.show", $order) }}';
                        }
                    });
                } else {
                    alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                    window.location.href = '{{ route("orders.show", $order) }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.');
                window.location.href = '{{ route("orders.show", $order) }}';
            });
        });
    </script>
    @endpush
</x-app-layout>

