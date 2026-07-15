<?php use \App\Domain\Shared\Enums\OrderStatus; ?>

<div class="min-h-screen bg-gradient-to-br from-stone-900 via-stone-800 to-stone-900 text-white flex flex-col"
    wire:poll.3s
    x-data="{ prevCount: <?php echo e($this->activeOrder?->items->count() ?? 0); ?> }"
    x-init="setInterval(() => {
        let newCount = <?php echo e($this->activeOrder?->items->count() ?? 0); ?>;
        if (newCount > prevCount) {
            $refs.itemsList?.scrollTo({ top: $refs.itemsList.scrollHeight, behavior: 'smooth' });
        }
        prevCount = newCount;
    }, 3000)">

    
    <div class="px-8 py-5 flex items-center justify-between border-b border-white/10 shrink-0">
        <div>
            <h1 class="text-2xl font-bold tracking-tight"><?php echo e($this->settings['store_name']); ?></h1>
            <p class="text-sm text-white/50 mt-0.5"><?php echo e(now()->format('l, M d, Y • h:i A')); ?></p>
        </div>
        <div class="text-right">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeOrder): ?>
                <span class="text-2xl font-mono font-bold text-amber-400"><?php echo e($this->activeOrder->order_number); ?></span>
            <?php else: ?>
                <span class="text-lg text-white/40"><?php echo e(__('No active order')); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeOrder): ?>
        
        <div class="px-8 py-3 flex items-center gap-3 border-b border-white/10 shrink-0">
            <span class="px-3 py-1 rounded-full text-sm font-medium
                <?php if($this->activeOrder->status === OrderStatus::Draft): ?> bg-stone-600 text-white
                <?php elseif($this->activeOrder->status === OrderStatus::Pending): ?> bg-yellow-500 text-black
                <?php elseif($this->activeOrder->status === OrderStatus::Paid): ?> bg-green-500 text-white
                <?php else: ?> bg-stone-600 text-white <?php endif; ?>">
                <?php echo e($this->activeOrder->status->label()); ?>

            </span>
            <span class="text-sm text-white/60"><?php echo e($this->activeOrder->order_type->label()); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeOrder->table_number): ?>
                <span class="text-sm text-white/60">&middot; <?php echo e(__('Table')); ?> <?php echo e($this->activeOrder->table_number); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <span class="text-sm text-white/60 ml-auto"><?php echo e($this->activeOrder->items->count()); ?> <?php echo e(__('item(s)')); ?></span>
        </div>

        
        <div class="flex-1 overflow-y-auto px-8 py-4 space-y-2" x-ref="itemsList">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->activeOrder->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="bg-white/10 rounded-2xl px-6 py-4 flex items-center gap-5 transition-all"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-x-8"
                    x-transition:enter-end="opacity-100 translate-x-0">
                    
                    <div class="w-14 h-14 rounded-full bg-amber-500/20 border-2 border-amber-400/40 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-amber-400"><?php echo e($item->quantity); ?></span>
                    </div>

                    
                    <div class="flex-1 min-w-0">
                        <h3 class="text-xl font-bold leading-tight"><?php echo e($item->product_name); ?></h3>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->variant_name): ?>
                            <p class="text-base text-white/50 mt-0.5"><?php echo e($item->variant_name); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->modifiers->isNotEmpty()): ?>
                            <div class="flex flex-wrap gap-1.5 mt-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $item->modifiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modifier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <span class="px-2.5 py-0.5 rounded-full text-sm bg-white/10 text-white/70">
                                        <?php echo e($modifier->modifier_option_name); ?>

                                    </span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->notes): ?>
                            <p class="text-sm text-white/40 italic mt-1">&ldquo;<?php echo e($item->notes); ?>&rdquo;</p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="text-right shrink-0">
                        <p class="text-xl font-bold text-amber-400">$<?php echo e(number_format($item->total_price, 2)); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item->quantity > 1): ?>
                            <p class="text-sm text-white/40">$<?php echo e(number_format($item->unit_price, 2)); ?> <?php echo e(__('each')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>

        
        <div class="border-t border-white/10 px-8 py-5 space-y-2 shrink-0 bg-white/5">
            <div class="flex justify-between text-lg">
                <span class="text-white/60"><?php echo e(__('Subtotal')); ?></span>
                <span class="font-semibold">$<?php echo e(number_format($this->activeOrder->subtotal, 2)); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeOrder->discount > 0): ?>
                <div class="flex justify-between text-lg text-green-400">
                    <span><?php echo e(__('Discount')); ?></span>
                    <span class="font-semibold">-$<?php echo e(number_format($this->activeOrder->discount, 2)); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div class="flex justify-between text-lg">
                <span class="text-white/60"><?php echo e(__('Tax')); ?></span>
                <span class="font-semibold">$<?php echo e(number_format($this->activeOrder->tax, 2)); ?></span>
            </div>
            <div class="flex justify-between pt-3 border-t border-white/10">
                <span class="text-2xl font-bold"><?php echo e(__('Total')); ?></span>
                <span class="text-3xl font-bold text-amber-400">$<?php echo e(number_format($this->activeOrder->total, 2)); ?></span>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->activeOrder->status === OrderStatus::Paid): ?>
                <div class="mt-3 text-center">
                    <span class="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-green-500/20 text-green-400 text-xl font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <?php echo e(__('Paid')); ?>

                    </span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php else: ?>
        
        <div class="flex-1 flex flex-col items-center justify-center text-center px-8">
            <div class="w-24 h-24 rounded-full bg-white/5 flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-white/60"><?php echo e(__('Welcome')); ?></h2>
            <p class="text-lg text-white/30 mt-2 max-w-md"><?php echo e(__('Your order will appear here once the cashier starts ringing it up.')); ?></p>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views\livewire\pos\customer-display.blade.php ENDPATH**/ ?>