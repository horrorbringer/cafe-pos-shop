<?php
    $templates = [
        'classic' => ['outer' => '=', 'inner' => '-', 'header_align' => 'left', 'tight' => false, 'show_outer' => true, 'label' => 'Classic'],
        'minimal' => ['outer' => '', 'inner' => '-', 'header_align' => 'left', 'tight' => true, 'show_outer' => false, 'label' => 'Minimal'],
        'detailed' => ['outer' => '=', 'inner' => '=', 'header_align' => 'center', 'tight' => false, 'show_outer' => true, 'label' => 'Detailed'],
        'compact' => ['outer' => '-', 'inner' => '-', 'header_align' => 'left', 'tight' => true, 'show_outer' => true, 'label' => 'Compact'],
        'branded' => ['outer' => '*', 'inner' => '·', 'header_align' => 'center', 'tight' => false, 'show_outer' => true, 'label' => 'Branded'],
    ];
    $t = $templates[$template] ?? $templates['classic'];
    $outer = $t['show_outer'] ? str_repeat($t['outer'], $width) : '';
    $inner = str_repeat($t['inner'], $width);
    $tight = $t['tight'];
    $nl = $tight ? '' : "\n";
    $gap = $tight ? '' : "\n";
?>
<div class="rounded-xl bg-gray-50 dark:bg-gray-900/50 ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden sticky top-4" x-data>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-eye','class' => 'w-4 h-4 text-gray-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-eye','class' => 'w-4 h-4 text-gray-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $attributes = $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950)): ?>
<?php $component = $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950; ?>
<?php unset($__componentOriginalbfc641e0710ce04e5fe02876ffc6f950); ?>
<?php endif; ?>
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300"><?php echo e(__('Receipt Preview')); ?></span>
        <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-mono"><?php echo e($width); ?>w</span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($template): ?>
            <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                style="background:<?php echo e(match($template) { 'classic' => '#e0e7ff', 'minimal' => '#f0fdf4', 'detailed' => '#fef3c7', 'compact' => '#fce7f3', 'branded' => '#ede9fe', default => '#e0e7ff' }); ?>;color:<?php echo e(match($template) { 'classic' => '#3730a3', 'minimal' => '#166534', 'detailed' => '#92400e', 'compact' => '#9d174d', 'branded' => '#5b21b6', default => '#3730a3' }); ?>">
                <?php echo e($t['label']); ?>

            </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="ml-auto text-xs text-gray-400 animate-pulse"><?php echo e(__('Live')); ?></span>
    </div>
    <div class="p-5 flex justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm" style="max-width:340px;width:100%">
            <div class="font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 dark:text-stone-300" style="max-height:400px;overflow-y:auto">
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($outer): ?>
<?php echo e($outer); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showLogo && $logoUrl): ?>
<img src="<?php echo e($logoUrl); ?>" alt="Logo" style="display:block;margin:0 auto 4px;max-height:32px;max-width:<?php echo e(min($width * 6, 180)); ?>px;object-fit:contain">
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<div style="<?php echo \Illuminate\Support\Arr::toCssStyles(['text-align:center' => $t['header_align'] === 'center']) ?>"><?php echo e(strtoupper($shopName)); ?></div>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddress && $address): ?>
<div style="<?php echo \Illuminate\Support\Arr::toCssStyles(['text-align:center' => $t['header_align'] === 'center']) ?>"><?php echo e($address); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPhone && $phone): ?>
<div style="<?php echo \Illuminate\Support\Arr::toCssStyles(['text-align:center' => $t['header_align'] === 'center']) ?>"><?php echo e(__('Tel:').' '.$phone); ?></div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php echo e($outer ?: ''); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($header): ?>
<?php echo e($header); ?>

<?php echo e($inner); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php
    $metaParts = [];
    if ($showOrderType) $metaParts[] = __('Type').': '.($orderType ?? 'Dine-in');
    if ($showTable) $metaParts[] = __('Table').': '.($orderTable ?? '5');
    $metaLine = implode('  ', $metaParts);
?>
<?php echo e(__('Order #')); ?>: <?php echo e($orderNumber ?? 'PREVIEW'); ?><?php echo e($tight ? ' | ' : "\n"); ?><?php echo e(__('Date')); ?>: <?php echo e($orderDate ?? now()->format('d M Y, H:i')); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($metaLine): ?>
<?php echo e($metaLine); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showCashier): ?>
<?php echo e(__('Cashier')); ?>: <?php echo e($cashierName ?? 'Admin'); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php echo e($gap); ?>

<?php echo e($inner); ?>

<?php echo e(__('QTY')); ?>  <?php echo e(__('ITEM')); ?><?php echo e(str_repeat(' ', max(1, $width - 14 - mb_strlen(__('ITEM')) - mb_strlen(__('PRICE'))))); ?><?php echo e(__('PRICE')); ?>

<?php echo e($inner); ?>

1x   Cold Brew Latte         4.50
2x   Blueberry Muffin        7.00
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModifiers): ?>
     + Extra Blueberries
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
1x   Espresso                3.00
<?php echo e($inner); ?>

<?php echo e($tight ? '' : "\n"); ?><?php echo e(__('Subtotal')); ?><?php echo e(str_repeat(' ', max(1, $width - 9 - mb_strlen(__('Subtotal'))))); ?>14.50
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showDiscount): ?>
<?php echo e(__('Discount')); ?><?php echo e(str_repeat(' ', max(1, $width - 9 - mb_strlen(__('Discount'))))); ?>-1.45
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php echo e(__('Tax')); ?><?php echo e(str_repeat(' ', max(1, $width - 3 - mb_strlen(__('Tax'))))); ?>1.31
<?php echo e($inner); ?>

<?php echo e(__('Total')); ?><?php echo e(str_repeat(' ', max(1, $width - 5 - mb_strlen(__('Total'))))); ?>14.36
<?php echo e($inner); ?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPayment): ?>
<?php echo e(__('Paid')); ?><?php echo e(str_repeat(' ', max(1, $width - 4 - mb_strlen(__('Paid'))))); ?>20.00
<?php echo e(__('Change')); ?><?php echo e(str_repeat(' ', max(1, $width - 6 - mb_strlen(__('Change'))))); ?>5.64
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showNotes && !empty($sampleNotes)): ?>
<?php echo e($inner); ?>

<?php echo e(__('Notes')); ?>: <?php echo e($sampleNotes); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footer): ?>
<?php echo e($inner); ?>

<?php echo e($footer); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($outer): ?>
<?php echo e($outer); ?>

<?php else: ?>
<?php echo e($inner); ?>

<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views/filament/receipt-preview-live.blade.php ENDPATH**/ ?>