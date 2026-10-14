@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>{{ $product->name }}</h5>
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-light btn-sm">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">SKU</div>
                        <div class="col-sm-7"><code>{{ $product->sku }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Category</div>
                        <div class="col-sm-7"><span class="badge bg-secondary">{{ $product->category }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Quantity</div>
                        <div class="col-sm-7">
                            <strong>{{ $product->quantity }}</strong>
                            <span class="badge bg-{{ $product->stock_status_color }} ms-1">{{ $product->stock_status }}</span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Reorder Level</div>
                        <div class="col-sm-7">{{ $product->reorder_level }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Unit Price</div>
                        <div class="col-sm-7">₱{{ number_format($product->unit_price, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Inventory Value</div>
                        <div class="col-sm-7"><strong class="text-success">₱{{ number_format($product->inventory_value, 2) }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Supplier</div>
                        <div class="col-sm-7">{{ $product->supplier ?? '—' }}</div>
                    </div>
                    @if($product->description)
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Description</div>
                        <div class="col-sm-7">{{ $product->description }}</div>
                    </div>
                    @endif
                    <div class="row mb-3">
                        <div class="col-sm-5 text-muted">Created</div>
                        <div class="col-sm-7">{{ $product->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-5 text-muted">Last Updated</div>
                        <div class="col-sm-7">{{ $product->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('transactions.create') }}?product_id={{ $product->id }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-left-right me-1"></i> Transaction
                        </a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Transaction History</h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $transactions = $product->transactions()->with('user')->recent()->paginate(15);
                        $running = $product->quantity;
                        // Build running balance backwards from current quantity
                        $txns = $transactions->getCollection();
                        $balances = [];
                        $bal = $product->quantity;
                        foreach ($txns as $t) {
                            $balances[$t->id] = $bal;
                            // reverse the effect to go backwards in time
                            if ($t->type === 'stock_in') {
                                $bal -= abs($t->quantity);
                            } elseif ($t->type === 'stock_out') {
                                $bal += abs($t->quantity);
                            } else {
                                $bal -= $t->quantity;
                            }
                        }
                    @endphp

                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Balance</th>
                                        <th>User</th>
                                        <th>Ref</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $txn)
                                        <tr>
                                            <td>
                                                <div>{{ $txn->created_at->format('M d, Y') }}</div>
                                                <small class="text-muted">{{ $txn->created_at->format('h:i A') }}</small>
                                            </td>
                                            <td><span class="badge {{ $txn->badge_class }}">{{ $txn->type_label }}</span></td>
                                            <td class="text-end">
                                                @if($txn->type === 'stock_out' || ($txn->type === 'adjustment' && $txn->quantity < 0))
                                                    <span class="text-danger">{{ $txn->signed_quantity }}</span>
                                                @else
                                                    <span class="text-success">+{{ abs($txn->quantity) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-end fw-semibold">{{ $balances[$txn->id] ?? '—' }}</td>
                                            <td>{{ $txn->user->name ?? 'N/A' }}</td>
                                            <td><small>{{ $txn->reference_number ?? '—' }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <p class="mb-2">No transactions recorded for this product yet.</p>
                            <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-primary">Record Transaction</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
