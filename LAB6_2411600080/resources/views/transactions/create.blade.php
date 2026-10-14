@extends('layouts.app')

@section('title', 'Record Transaction')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-plus-circle me-2"></i>Record Stock Transaction
                </h1>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('transactions.store') }}" id="transactionForm">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">— Select Product —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-qty="{{ $product->quantity }}"
                                            data-reorder="{{ $product->reorder_level }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }}) — Qty: {{ $product->quantity }}
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="stockInfo" class="form-text mt-1" style="display:none;"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Transaction Type <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 flex-wrap">
                                @foreach($types as $key => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="type" id="type_{{ $key }}"
                                               value="{{ $key }}" {{ old('type', 'stock_in') === $key ? 'checked' : '' }} required>
                                        <label class="form-check-label" for="type_{{ $key }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                            @error('type')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                   value="{{ old('quantity') }}" min="1" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="qtyHint" class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number"
                                   class="form-control @error('reference_number') is-invalid @enderror"
                                   value="{{ old('reference_number') }}" maxlength="100"
                                   placeholder="e.g. PO-2024-001, SO-1234">
                            @error('reference_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" rows="3"
                                      class="form-control @error('notes') is-invalid @enderror"
                                      maxlength="1000">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Record Transaction
                            </button>
                            <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productSelect = document.getElementById('product_id');
    const stockInfo = document.getElementById('stockInfo');
    const qtyInput = document.getElementById('quantity');
    const qtyHint = document.getElementById('qtyHint');
    const typeRadios = document.querySelectorAll('input[name="type"]');

    function updateStockInfo() {
        const opt = productSelect.options[productSelect.selectedIndex];
        if (!opt || !opt.value) {
            stockInfo.style.display = 'none';
            qtyHint.textContent = '';
            qtyInput.removeAttribute('max');
            return;
        }
        const qty = parseInt(opt.dataset.qty, 10);
        const reorder = parseInt(opt.dataset.reorder, 10);
        let statusClass = 'text-success';
        let status = 'In Stock';
        if (qty <= 0) { status = 'Out of Stock'; statusClass = 'text-danger'; }
        else if (qty <= reorder) { status = 'Low Stock'; statusClass = 'text-warning'; }

        stockInfo.innerHTML = `Current stock: <strong>${qty}</strong> · Reorder level: ${reorder} · <span class="${statusClass}">${status}</span>`;
        stockInfo.style.display = 'block';

        updateQtyConstraints();
    }

    function updateQtyConstraints() {
        const type = document.querySelector('input[name="type"]:checked')?.value;
        const opt = productSelect.options[productSelect.selectedIndex];
        if (!opt || !opt.value) return;

        const available = parseInt(opt.dataset.qty, 10);
        if (type === 'stock_out') {
            qtyInput.max = available;
            qtyHint.textContent = `Maximum available: ${available}`;
            qtyHint.className = 'form-text text-muted';
        } else {
            qtyInput.removeAttribute('max');
            qtyHint.textContent = type === 'stock_in' ? 'Quantity to add to stock' : 'For adjustments use the dedicated Stock Adjustment form';
            qtyHint.className = 'form-text text-muted';
        }
    }

    productSelect.addEventListener('change', updateStockInfo);
    typeRadios.forEach(r => r.addEventListener('change', updateQtyConstraints));
    updateStockInfo();
});
</script>
@endpush
