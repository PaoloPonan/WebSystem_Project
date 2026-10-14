@extends('layouts.app')

@section('title', 'Inventory Transactions')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="bi bi-arrow-left-right me-2"></i>Inventory Transactions
            </h1>
            <p class="text-muted mb-0">Track all stock movements in real-time</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.adjustment') }}" class="btn btn-outline-secondary">
                <i class="bi bi-sliders me-1"></i> Stock Adjustment
            </a>
            <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i> Record Transaction
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('transactions.index') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Product</label>
                    <select name="product_id" class="form-select">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} ({{ $product->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">Type</label>
                    <select name="type" class="form-select">
                        <option value="all">All Types</option>
                        @foreach(\App\Models\InventoryTransaction::types() as $key => $label)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route('transactions.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Transactions table --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Date/Time</th>
                                <th>Product</th>
                                <th>Type</th>
                                <th class="text-end">Quantity</th>
                                <th>Reference</th>
                                <th>User</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $txn)
                                <tr>
                                    <td>
                                        <div>{{ $txn->created_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $txn->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('products.show', $txn->product) }}" class="text-decoration-none">
                                            <strong>{{ $txn->product->name ?? 'N/A' }}</strong>
                                        </a>
                                        <br><small class="text-muted">{{ $txn->product->sku ?? '' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $txn->badge_class }}">{{ $txn->type_label }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($txn->type === 'stock_out')
                                            <span class="text-danger fw-semibold">-{{ $txn->quantity }}</span>
                                        @elseif($txn->type === 'adjustment' && $txn->quantity < 0)
                                            <span class="text-danger fw-semibold">{{ $txn->quantity }}</span>
                                        @else
                                            <span class="text-success fw-semibold">+{{ abs($txn->quantity) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $txn->reference_number ?? '—' }}</td>
                                    <td>{{ $txn->user->name ?? 'N/A' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('transactions.show', $txn) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('transactions.destroy', $txn) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Delete this transaction? Stock will be reversed.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox display-4 text-muted"></i>
                    <p class="mt-3 text-muted">No transactions found.</p>
                    <a href="{{ route('transactions.create') }}" class="btn btn-primary">
                        Record First Transaction
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
