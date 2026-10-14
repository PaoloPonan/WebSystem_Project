@extends('layouts.app')

@section('title', 'Transaction Details')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="bi bi-receipt me-2"></i>Transaction #{{ $transaction->id }}
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
            <form action="{{ route('transactions.destroy', $transaction) }}" method="POST"
                  onsubmit="return confirm('Delete this transaction? Stock will be reversed.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Transaction Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Type</dt>
                        <dd class="col-sm-8">
                            <span class="badge {{ $transaction->badge_class }}">{{ $transaction->type_label }}</span>
                        </dd>

                        <dt class="col-sm-4">Quantity</dt>
                        <dd class="col-sm-8">
                            @if($transaction->type === 'stock_out')
                                <span class="text-danger fw-semibold">-{{ $transaction->quantity }}</span>
                            @elseif($transaction->type === 'adjustment' && $transaction->quantity < 0)
                                <span class="text-danger fw-semibold">{{ $transaction->quantity }}</span>
                            @else
                                <span class="text-success fw-semibold">+{{ abs($transaction->quantity) }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Reference</dt>
                        <dd class="col-sm-8">{{ $transaction->reference_number ?? '—' }}</dd>

                        <dt class="col-sm-4">Notes</dt>
                        <dd class="col-sm-8">{{ $transaction->notes ?? '—' }}</dd>

                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8">{{ $transaction->created_at->format('M d, Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Product</h5>
                </div>
                <div class="card-body">
                    @if($transaction->product)
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8">
                                <a href="{{ route('products.show', $transaction->product) }}">
                                    {{ $transaction->product->name }}
                                </a>
                            </dd>
                            <dt class="col-sm-4">SKU</dt>
                            <dd class="col-sm-8"><code>{{ $transaction->product->sku }}</code></dd>
                            <dt class="col-sm-4">Current Qty</dt>
                            <dd class="col-sm-8">
                                {{ $transaction->product->quantity }}
                                <span class="badge bg-{{ $transaction->product->stock_status_color }} ms-1">
                                    {{ $transaction->product->stock_status }}
                                </span>
                            </dd>
                            <dt class="col-sm-4">Category</dt>
                            <dd class="col-sm-8">{{ $transaction->product->category }}</dd>
                        </dl>
                    @else
                        <p class="text-muted mb-0">Product no longer available.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Performed By</h5>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $transaction->user->name ?? 'N/A' }}</strong></p>
                    <p class="text-muted mb-0 small">{{ $transaction->user->email ?? '' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
