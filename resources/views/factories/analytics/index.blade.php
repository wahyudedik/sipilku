@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Factory Analytics</h1>
        <div id="analytics-dashboard" data-factory-uuid="{{ $factory->uuid ?? '' }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                <div class="p-4 bg-white border rounded-md">Views: <span id="views">-</span></div>
                <div class="p-4 bg-white border rounded-md">Orders: <span id="orders">-</span></div>
                <div class="p-4 bg-white border rounded-md">Revenue: <span id="revenue">-</span></div>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Top Products</h2>
                <ul id="top-products" class="list-disc pl-4"></ul>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const el = document.getElementById('analytics-dashboard');
                const uuid = el?.dataset?.factoryUuid;
                if (!uuid) return;

                fetch(`/factories/${uuid}/analytics/dashboard`)
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('views').innerText = data.views ?? 0;
                        document.getElementById('orders').innerText = data.orders_count ?? 0;
                        document.getElementById('revenue').innerText = (data.revenue ?? 0).toLocaleString();
                    });
                fetch(
                        `/factories/type/${document.querySelector('[name="factory_type"]')?.value ?? '1'}/analytics/product-popularity`)
                    .then(r => r.json())
                    .then(items => {
                        const ul = document.getElementById('top-products');
                        ul.innerHTML = '';
                        items.forEach(i => {
                            const li = document.createElement('li');
                            li.innerText = `${i.product_name} — ${i.total_qty} pcs`;
                            ul.appendChild(li);
                        });
                    }).catch(e => {});
            });
        </script>
    @endpush
@endsection
