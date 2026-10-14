<?php $__env->startSection('title', 'Products'); ?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0"><i class="bi bi-box-seam me-2"></i>Product Inventory</h1>
        <a href="<?php echo e(route('products.create')); ?>" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Add New Product
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Reorder</th>
                            <th>Unit Price</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="<?php echo e($product->isOutOfStock() ? 'table-danger' : ($product->isLowStock() ? 'table-warning' : '')); ?>">
                                <td><code><?php echo e($product->sku); ?></code></td>
                                <td>
                                    <strong><?php echo e($product->name); ?></strong>
                                    <?php if($product->description): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($product->description, 40)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-secondary"><?php echo e($product->category); ?></span></td>
                                <td><?php echo e($product->quantity); ?></td>
                                <td><?php echo e($product->reorder_level); ?></td>
                                <td>$<?php echo e(number_format($product->unit_price, 2)); ?></td>
                                <td>
                                    <?php if($product->isOutOfStock()): ?>
                                        <span class="badge bg-danger">Out of Stock</span>
                                    <?php elseif($product->isLowStock()): ?>
                                        <span class="badge bg-warning text-dark">Low Stock</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">In Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($product->supplier ?? '—'); ?></td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('products.show', $product)); ?>" class="btn btn-outline-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('products.edit', $product)); ?>" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('products.destroy', $product)); ?>" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No products found. <a href="<?php echo e(route('products.create')); ?>">Add your first product</a>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($products->hasPages()): ?>
            <div class="card-footer bg-white">
                <?php echo e($products->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\LAB6_2411600080\resources\views/products/index.blade.php ENDPATH**/ ?>