@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="bi bi-speedometer2 me-2"></i>Inventory Dashboard</h1>
            <small class="text-muted">Last updated: <span id="lastUpdated">{{ now()->format('h:i:s A') }}</span></small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.create') }}" class="btn btn-outline-primary">
                <i class="bi bi-arrow-left-right me-1"></i> Record Transaction
            </a>
            <a href="{{ route('products.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Statistics Cards (IDs for AJAX updates) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Products</h6>
                            <h2 class="mb-0 fw-bold text-primary" id="stat-total-products">{{ $totalProducts }}</h2>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-box-seam fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Low Stock</h6>
                            <h2 class="mb-0 fw-bold text-warning" id="stat-low-stock">{{ $lowStockCount }}</h2>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-exclamation-triangle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Out of Stock</h6>
                            <h2 class="mb-0 fw-bold text-danger" id="stat-out-of-stock">{{ $outOfStockCount }}</h2>
                        </div>
                        <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-x-circle fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Inventory Value</h6>
                            <h2 class="mb-0 fw-bold text-success" id="stat-inventory-value">₱{{ number_format($totalInventoryValue, 2) }}</h2>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10 text-success">
                            <i class="bi bi-currency-dollar fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Low Stock Alerts -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-warning bg-opacity-10 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                </div>
                <div class="card-body p-0" id="low-stock-list">
                    @if($lowStockProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Reorder</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                        <tr>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </td>
                                            <td><span class="badge bg-warning text-dark">{{ $product->quantity }}</span></td>
                                            <td>{{ $product->reorder_level }}</td>
                                            <td>
                                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle fs-3 text-success"></i>
                            <p class="mb-0 mt-2">All products are sufficiently stocked.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary bg-opacity-10 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary"><i class="bi bi-clock-history me-2"></i>Recent Transactions</h5>
                    <a href="{{ route('transactions.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0" id="recent-transactions-list">
                    @if($recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Type</th>
                                        <th>Qty</th>
                                        <th>When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $txn)
                                        <tr>
                                            <td>
                                                <strong>{{ $txn->product->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td><span class="badge {{ $txn->badge_class }}">{{ $txn->type_label }}</span></td>
                                            <td>
                                                @if($txn->type === 'stock_out' || ($txn->type === 'adjustment' && $txn->quantity < 0))
                                                    <span class="text-danger">{{ $txn->signed_quantity }}</span>
                                                @else
                                                    <span class="text-success">+{{ abs($txn->quantity) }}</span>
                                                @endif
                                            </td>
                                            <td><small class="text-muted">{{ $txn->created_at->diffForHumans() }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <p class="mb-0">No transactions yet. <a href="{{ route('transactions.create') }}">Record your first transaction</a>.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Category Summary -->
    @if($categorySummary->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success bg-opacity-10 border-0">
                    <h5 class="mb-0 text-success"><i class="bi bi-pie-chart me-2"></i>Inventory by Category</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($categorySummary as $cat)
                            <div class="col-md-4 col-sm-6">
                                <div class="p-3 border rounded">
                                    <h6 class="mb-1">{{ $cat->category }}</h6>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">{{ $cat->count }} products</span>
                                        <strong class="text-success">₱{{ number_format($cat->value, 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
(function () {
    const POLL_INTERVAL = 30000; // 30 seconds

    function formatMoney(val) {
        return '₱' + Number(val).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    async function refreshStats() {
        try {
            const res = await fetch('{{ route("api.dashboard.stats") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const data = await res.json();

            document.getElementById('stat-total-products').textContent = data.total_products;
            document.getElementById('stat-low-stock').textContent = data.low_stock_count;
            document.getElementById('stat-out-of-stock').textContent = data.out_of_stock_count;
            document.getElementById('stat-inventory-value').textContent = formatMoney(data.total_inventory_value);

            const ts = new Date(data.updated_at);
            document.getElementById('lastUpdated').textContent = ts.toLocaleTimeString();
        } catch (e) {
            console.warn('Dashboard poll failed', e);
        }
    }

    async function refreshRecentTransactions() {
        try {
            const res = await fetch('{{ route("api.transactions.recent") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const items = await res.json();
            const container = document.getElementById('recent-transactions-list');
            if (!items.length) return;

            let html = '<div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Product</th><th>Type</th><th>Qty</th><th>When</th></tr></thead><tbody>';
            items.forEach(t => {
                const qtyClass = (t.type === 'stock_out' || t.signed_quantity < 0) ? 'text-danger' : 'text-success';
                const qtyText = t.signed_quantity > 0 ? '+' + t.signed_quantity : t.signed_quantity;
                const badge = t.type === 'stock_in' ? 'bg-success' : (t.type === 'stock_out' ? 'bg-danger' : 'bg-warning text-dark');
                html += '<tr><td><strong>' + t.product_name + '</strong></td><td><span class="badge ' + badge + '">' + t.type_label + '</span></td><td><span class="' + qtyClass + '">' + qtyText + '</span></td><td><small class="text-muted">' + t.created_at_human + '</small></td></tr>';
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        } catch (e) {
            console.warn('Recent transactions poll failed', e);
        }
    }

    setTimeout(function () {
        refreshStats();
        refreshRecentTransactions();
    }, 2000);

    setInterval(function () {
        refreshStats();
        refreshRecentTransactions();
    }, POLL_INTERVAL);
})();
</script>
@endpush
