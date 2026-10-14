@extends('layouts.app')

@section('title', 'Stock Adjustment')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="bi bi-sliders me-2"></i>Stock Adjustment
                </h1>
                <a href="{{ route('transactions.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back
                </a>
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle me-1"></i>
                Use this form when physical count differs from system quantity (damage, theft, counting errors, etc.).
                The system will automatically create an adjustment transaction and update stock.
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('transactions.adjustment.process') }}" id="adjustmentForm">
                        @csrf

                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product <span class="text-danger">*</span></label>
                            <select name="product_id" id="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                                <option value="">— Select Product —</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-qty="{{ $product->quantity }}"
                                            {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current System Quantity</label>
                            <input type="text" id="system_qty" class="form-control" readonly value="—">
                        </div>

                        <div class="mb-3">
                            <label for="physical_count" class="form-label">Physical Count <span class="text-danger">*</span></label>
                            <input type="number" name="physical_count" id="physical_count"
                                   class="form-control @error('physical_count') is-invalid @enderror"
                                   value="{{ old('physical_count') }}" min="0" required>
                            @error('physical_count')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="diffHint" class="form-text"></div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                            <select name="reason" id="reason" class="form-select @error('reason') is-invalid @enderror" required>
                                <option value="">— Select Reason —</option>
                                <option value="Physical Count Discrepancy" {{ old('reason') === 'Physical Count Discrepancy' ? 'selected' : '' }}>Physical Count Discrepancy</option>
                                <option value="Damaged Goods" {{ old('reason') === 'Damaged Goods' ? 'selected' : '' }}>Damaged Goods</option>
                                <option value="Theft / Loss" {{ old('reason') === 'Theft / Loss' ? 'selected' : '' }}>Theft / Loss</option>
                                <option value="System Error Correction" {{ old('reason') === 'System Error Correction' ? 'selected' : '' }}>System Error Correction</option>
                                <option value="Other" {{ old('reason') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-1"></i> Apply Adjustment
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
    const systemQty = document.getElementById('system_qty');
    const physicalCount = document.getElementById('physical_count');
    const diffHint = document.getElementById('diffHint');

    function updateSystemQty() {
        const opt = productSelect.options[productSelect.selectedIndex];
        if (!opt || !opt.value) {
            systemQty.value = '—';
            diffHint.textContent = '';
            return;
        }
        systemQty.value = opt.dataset.qty;
        updateDiff();
    }

    function updateDiff() {
        const sys = parseInt(systemQty.value, 10);
        const phys = parseInt(physicalCount.value, 10);
        if (isNaN(sys) || isNaN(phys)) {
            diffHint.textContent = '';
            return;
        }
        const diff = phys - sys;
        if (diff === 0) {
            diffHint.innerHTML = '<span class="text-muted">No difference — no adjustment needed.</span>';
        } else if (diff > 0) {
            diffHint.innerHTML = `<span class="text-success">Will increase stock by <strong>+${diff}</strong></span>`;
        } else {
            diffHint.innerHTML = `<span class="text-danger">Will decrease stock by <strong>${diff}</strong></span>`;
        }
    }

    productSelect.addEventListener('change', updateSystemQty);
    physicalCount.addEventListener('input', updateDiff);
    updateSystemQty();
});
</script>
@endpush
