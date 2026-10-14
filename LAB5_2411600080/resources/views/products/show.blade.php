@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>{{ $product->name }}</h5>
                    <div>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">SKU</div>
                        <div class="col-sm-8"><code>{{ $product->sku }}</code></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Category</div>
                        <div class="col-sm-8"><span class="badge bg-secondary">{{ $product->category }}</span></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Description</div>
                        <div class="col-sm-8">{{ $product->description ?? '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Quantity in Stock</div>
                        <div class="col-sm-8">
                            <strong>{{ $product->quantity }}</strong>
                            @if($product->isOutOfStock())
                                <span class="badge bg-danger ms-2">Out of Stock</span>
                            @elseif($product->isLowStock())
                                <span class="badge bg-warning text-dark ms-2">Low Stock</span>
                            @else
                                <span class="badge bg-success ms-2">In Stock</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Reorder Level</div>
                        <div class="col-sm-8">{{ $product->reorder_level }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Unit Price</div>
                        <div class="col-sm-8">${{ number_format($product->unit_price, 2) }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Inventory Value</div>
                        <div class="col-sm-8"><strong class="text-success">${{ number_format($product->inventory_value, 2) }}</strong></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Supplier</div>
                        <div class="col-sm-8">{{ $product->supplier ?? '—' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted">Created</div>
                        <div class="col-sm-8">{{ $product->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-muted">Last Updated</div>
                        <div class="col-sm-8">{{ $product->updated_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
                <div class="card-footer bg-white d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to List
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this product?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
