<?php
    $shopName = \App\Domain\Shop\Models\Setting::getValue('shop_name', config('app.name', 'My Cafe'));
    $address = \App\Domain\Shop\Models\Setting::getValue('shop_address', '');
    $phone = \App\Domain\Shop\Models\Setting::getValue('shop_phone', '');
    $header = \App\Domain\Shop\Models\Setting::getValue('receipt_header', '');
    $footer = \App\Domain\Shop\Models\Setting::getValue('receipt_footer', '');
    $printer = \App\Domain\Shop\Models\Setting::getValue('receipt_printer', 'default');
    $currency = \App\Domain\Shop\Models\Setting::getValue('shop_currency', 'USD');
    $showAddress = \App\Domain\Shop\Models\Setting::getValue('receipt_show_address', true);
    $showPhone = \App\Domain\Shop\Models\Setting::getValue('receipt_show_phone', true);
    $width = match ($printer) { 'thermal' => 32, 'pdf' => 48, default => 40 };
?>

<div class="mt-8">
    <div class="flex items-center gap-2 mb-3">
        <?php if (isset($component)) { $__componentOriginalbfc641e0710ce04e5fe02876ffc6f950 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbfc641e0710ce04e5fe02876ffc6f950 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.icon','data' => ['icon' => 'heroicon-o-eye','class' => 'w-5 h-5 text-gray-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'heroicon-o-eye','class' => 'w-5 h-5 text-gray-400']); ?>
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
        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Receipt Preview</span>
        <span class="text-xs text-gray-400 dark:text-gray-500">(<?php echo e($width); ?> chars width)</span>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
        <div class="p-6 flex justify-center">
            <div class="bg-stone-50 dark:bg-gray-800 rounded-lg p-5 shadow-sm" style="max-width:360px;width:100%">
                <div class="font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 dark:text-stone-300" style="max-height:400px;overflow-y:auto">
                    <?php echo e(str_repeat('=', $width)); ?>

                    <?php echo e(strtoupper($shopName)); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showAddress && $address): ?>
                        <?php echo e($address); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPhone && $phone): ?>
                        Tel: <?php echo e($phone); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php echo e(str_repeat('=', $width)); ?>


                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($header): ?>
                        <?php echo e($header); ?>

                        <?php echo e(str_repeat('-', $width)); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    Order #: ORD-20260623-0001
                    Date: 23 Jun 2026, 14:30
                    Type: Dine-in
                    Table: 5
                    Cashier: Admin

                    <?php echo e(str_repeat('-', $width)); ?>

                    QTY  ITEM                    PRICE
                    <?php echo e(str_repeat('-', $width)); ?>

                    1x   Cold Brew Latte         4.50
                    2x   Blueberry Muffin        7.00
                         + Extra Blueberries
                    1x   Espresso                3.00
                    <?php echo e(str_repeat('-', $width)); ?>

                    Subtotal                    14.50
                    Discount                   -1.45
                    Tax                         1.31
                    <?php echo e(str_repeat('-', $width)); ?>

                    TOTAL                      14.36
                    <?php echo e(str_repeat('-', $width)); ?>


                    Paid                        20.00
                    Change                      5.64

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footer): ?>
                        <?php echo e(str_repeat('-', $width)); ?>

                        <?php echo e($footer); ?>

                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php echo e(str_repeat('=', $width)); ?>

                </div>
            </div>
        </div>
        <div class="px-6 pb-4 text-center text-xs text-gray-400 dark:text-gray-500">
            Preview updates after saving settings.
        </div>
    </div>
</div>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views\filament\receipt-preview.blade.php ENDPATH**/ ?>