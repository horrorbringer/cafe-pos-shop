<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['dropdown' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['dropdown' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $current = app()->getLocale();
    $locales = collect(config('app.supported_locales', []))->mapWithKeys(fn ($code) => [
        $code => $code === 'en' ? __('English') : __(ucfirst($code)),
    ]);
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dropdown): ?>
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" @keydown.escape="open = false"
            class="flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider border border-gray-200 text-gray-500 dark:border-gray-600 dark:text-gray-300 transition-colors hover:bg-gray-50 dark:hover:bg-gray-700"
            aria-haspopup="true" :aria-expanded="open" aria-label="<?php echo e(__('Language')); ?>">
            <span><?php echo e(strtoupper($current)); ?></span>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>
        <div x-show="open" @click.outside="open = false" @keydown.escape="open = false" x-cloak
            class="absolute right-0 mt-1 w-32 rounded-lg shadow-lg border bg-white border-gray-200 dark:bg-gray-800 dark:border-gray-700 py-1 z-50"
            role="menu">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('language.switch', $code)); ?>"
                    class="block px-3 py-1.5 text-xs font-medium transition-colors"
                    :class="'<?php echo e($current); ?>' === '<?php echo e($code); ?>' ? 'text-amber-600 font-bold' : 'text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white'"
                    role="menuitem" <?php if($current === $code): ?> aria-current="true" <?php endif; ?>>
                    <?php echo e($label); ?>

                </a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="flex items-center gap-1" role="group" aria-label="<?php echo e(__('Language')); ?>">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <a href="<?php echo e(route('language.switch', $code)); ?>"
                class="px-2 py-1 rounded-md text-xs font-semibold uppercase tracking-wider transition-colors
                <?php echo e($current === $code ? 'bg-amber-100 text-amber-700' : 'text-gray-400 hover:text-gray-600'); ?>"
                <?php if($current === $code): ?> aria-current="true" <?php endif; ?>
                aria-label="<?php echo e($label); ?>">
                <?php echo e(strtoupper($code)); ?>

            </a>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views/components/language-switcher.blade.php ENDPATH**/ ?>