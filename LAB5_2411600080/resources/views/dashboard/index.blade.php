@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-speedometer2 me-2"></i>Inventory Dashboard</h1>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add Product
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card stat-card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted text-uppercase mb-1">Total Products</h6>
                            <h2 class="mb-0 fw-bold text-primary">{{ $totalProducts }}</h2>
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
                            <h2 class="mb-0 fw-bold text-warning">{{ $lowStockCount }}</h2>
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
                            <h2 class="mb-0 fw-bold text-danger">{{ $outOfStockCount }}</h2>
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
                            <h2 class="mb-0 fw-bold text-success">₱{{ number_format($totalInventoryValue, 2) }}</h2>
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
                <div class="card-header bg-warning bg-opacity-10 border-0">
                    <h5 class="mb-0 text-warning"><i class="bi bi-exclamation-triangle me-2"></i>Low Stock Alerts</h5>
                </div>
                <div class="card-body p-0">
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
                                            <td>
                                                <span class="badge {{ $product->isOutOfStock() ? 'bg-danger' : 'bg-warning text-dark' }}">
                                                    {{ $product->quantity }}
                                                </span>
                                            </td>
                                            <td>{{ $product->reorder_level }}</td>
                                            <td>
                                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-check-circle fs-1"></i>
                            <p class="mt-2 mb-0">All products are well stocked!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary bg-opacity-10 border-0">
                    <h5 class="mb-0 text-primary"><i class="bi bi-clock-history me-2"></i>Recently Added</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentProducts as $product)
                                        <tr>
                                            <td>
                                                <strong>{{ $product->name }}</strong><br>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $product->category }}</span></td>
                                            <td>₱{{ number_format($product->unit_price, 2) }}</td>
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
                            <p class="mb-0">No products yet. <a href="{{ route('products.create') }}">Add your first product</a>.</p>
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