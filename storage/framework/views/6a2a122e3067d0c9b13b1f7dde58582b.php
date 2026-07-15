<div class="flex h-screen bg-stone-50 text-stone-800 select-none"
    x-data="{ addingFlash: null, processing: $wire.entangle('processing') }"
    x-on:item-added.window="addingFlash = $event.detail.productId; setTimeout(() => addingFlash = null, 400)"
    @keydown.escape.window="if ($wire.showModifierModal) $wire.cancelModifierModal(); else if ($wire.showPaymentModal) $wire.set('showPaymentModal', false); else if ($wire.showKhqrModal) $wire.cancelKhqr(); else if ($wire.showReceiptModal) false"
    @keydown.enter.window="if ($wire.showPaymentModal && $wire.paymentMethod === 'cash' && $wire.amountTendered >= $wire.total) $wire.processPayment()">

    
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        
        <div class="bg-white border-b border-stone-200 px-5 py-3 flex items-center gap-3 shrink-0">
            <div class="flex items-center gap-3 shrink-0">
                <h1 class="text-lg font-bold text-stone-800 tracking-tight">POS</h1>
            </div>

            
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="<?php echo e(__('Search...')); ?>"
                    class="w-full pl-9 pr-4 py-1.5 border border-stone-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50"
                    x-ref="searchInput" x-init="$el.focus()">
            </div>

            <div class="flex items-center gap-2 ml-auto">
                <button onclick="window.open('<?php echo e(route('pos.customer-display')); ?>', 'customer-display', 'width=800,height=600')"
                    class="text-xs text-stone-400 hover:text-stone-600 hover:bg-stone-100 px-2 py-1 rounded transition-colors flex items-center gap-1"
                    title="<?php echo e(__('Open customer display')); ?>">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <?php echo e(__('Display')); ?>

                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->itemCount > 0): ?>
                    <button wire:click="holdOrder"
                        class="text-xs text-stone-500 hover:text-amber-600 hover:bg-amber-50 px-2 py-1 rounded transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <?php echo e(__('Hold')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php $heldCount = count($this->suspendedOrders); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heldCount > 0): ?>
                    <button wire:click="$toggle('showSuspendedOrders')"
                        class="relative text-xs text-stone-500 hover:text-blue-600 hover:bg-blue-50 px-2 py-1 rounded transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                        <?php echo e(__('Held')); ?>

                        <span class="bg-blue-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full -mr-1"><?php echo e($heldCount); ?></span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->itemCount > 0): ?>
                    <button wire:click="cancelOrder"
                        class="text-xs text-stone-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition-colors">
                        <?php echo e(__('Clear')); ?>

                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal8d3bff7d7383a45350f7495fc470d934 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8d3bff7d7383a45350f7495fc470d934 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.language-switcher','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('language-switcher'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $attributes = $__attributesOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__attributesOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8d3bff7d7383a45350f7495fc470d934)): ?>
<?php $component = $__componentOriginal8d3bff7d7383a45350f7495fc470d934; ?>
<?php unset($__componentOriginal8d3bff7d7383a45350f7495fc470d934); ?>
<?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white border-b border-stone-200 px-5 py-3 space-y-3 shrink-0">

            
            <div class="flex gap-1.5 overflow-x-auto pb-1 -mx-1 px-1 scrollbar-hide">
                <?php
                    $productsList = $this->products;
                    $categoryCounts = collect($productsList)->groupBy('category_id')->map->count();
                    $totalCount = count($productsList);
                ?>
                <button wire:click="$set('selectedCategoryId', 0)"
                    class="px-3.5 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 <?php echo e($selectedCategoryId === 0 ? 'bg-stone-800 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'); ?>">
                    <?php echo e(__('All')); ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalCount > 0): ?>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded <?php echo e($selectedCategoryId === 0 ? 'bg-white/20' : 'bg-stone-200 text-stone-500'); ?>"><?php echo e($totalCount); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php $catCount = $categoryCounts->get($category['id'], 0); ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($catCount > 0 || $selectedCategoryId === $category['id']): ?>
                    <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cat-'.e($category['id']).''; ?>wire:key="cat-<?php echo e($category['id']); ?>" wire:click="$set('selectedCategoryId', <?php echo e($category['id']); ?>)"
                        class="px-3.5 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-all flex items-center gap-1.5 <?php echo e($selectedCategoryId === $category['id'] ? 'bg-stone-800 text-white' : 'bg-stone-100 text-stone-600 hover:bg-stone-200'); ?>">
                        <?php echo e($category['name']); ?>

                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded <?php echo e($selectedCategoryId === $category['id'] ? 'bg-white/20' : 'bg-stone-200 text-stone-500'); ?>"><?php echo e($catCount); ?></span>
                    </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            
            <div class="flex items-center gap-1 bg-stone-100 rounded-lg p-1 w-fit">
                <button wire:click="$set('orderType', 'dine_in')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-all <?php echo e($orderType === 'dine_in' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800'); ?>">
                    <?php echo e(__('Dine-in')); ?>

                </button>
                <button wire:click="$set('orderType', 'takeaway')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-all <?php echo e($orderType === 'takeaway' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800'); ?>">
                    <?php echo e(__('Takeaway')); ?>

                </button>
                <button wire:click="$set('orderType', 'delivery')"
                    class="px-4 py-1.5 rounded-md text-sm font-medium transition-all <?php echo e($orderType === 'delivery' ? 'bg-amber-500 text-white shadow-sm' : 'text-stone-600 hover:text-stone-800'); ?>">
                    <?php echo e(__('Delivery')); ?>

                </button>
            </div>

            <div class="flex items-center gap-3">
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($orderType === 'dine_in'): ?>
                    <div class="relative">
                        <input type="text" wire:model.live="tableNumber" placeholder="<?php echo e(__('Table #')); ?>"
                            class="w-20 px-3 py-2 border border-stone-200 rounded-lg text-sm text-center font-medium focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50">
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="flex-1 overflow-y-auto p-5 space-y-6">
            <?php
                $popularProducts = collect($this->products)->filter(fn($p) => !empty($p['tags']) && in_array('popular', (array) $p['tags']));
                $regularProducts = collect($this->products)->filter(fn($p) => empty($p['tags']) || !in_array('popular', (array) $p['tags']));
            ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($popularProducts->isNotEmpty() && $selectedCategoryId === 0 && empty($search)): ?>
                <div>
                    <div class="flex items-center gap-1.5 mb-3">
                        <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        <h3 class="text-xs font-semibold text-stone-500 uppercase tracking-wider"><?php echo e(__('Popular Items')); ?></h3>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $popularProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $isOutOfStock = $product['stock_quantity'] <= 0;
                            ?>
                            <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'popular-'.e($product['id']).''; ?>wire:key="popular-<?php echo e($product['id']); ?>"
                                wire:click="addItemQuick(<?php echo e($product['id']); ?>)"
                                class="group relative bg-white rounded-xl border-2 border-amber-200 p-3 text-left transition-all duration-150 cursor-pointer flex flex-col
                                    <?php echo e($isOutOfStock ? 'opacity-40 cursor-not-allowed' : 'hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.97]'); ?>"
                            >
                                <div class="relative mb-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product['image']): ?>
                                        <img src="<?php echo e(asset('storage/' . $product['image'])); ?>" alt="<?php echo e($product['name']); ?>" class="w-full h-20 object-cover rounded-xl bg-stone-100">
                                    <?php else: ?>
                                        <div class="w-full h-20 bg-stone-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-6 h-6 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-500 text-white rounded-full flex items-center justify-center shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    </div>
                                </div>
                                <h4 class="font-semibold text-stone-800 text-sm leading-tight"><?php echo e($product['name']); ?></h4>
                                <p class="text-amber-600 font-bold text-sm mt-1">$<?php echo e(number_format($product['price'], 2)); ?></p>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->products) === 0): ?>
                <div class="flex flex-col items-center justify-center h-full text-stone-400">
                    <svg class="w-16 h-16 mb-3 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <p class="font-medium"><?php echo e(__('No products found')); ?></p>
                    <p class="text-sm mt-1"><?php echo e(__('Try a different category or search term')); ?></p>
                </div>
            <?php else: ?>
                <div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($popularProducts->isNotEmpty() && $selectedCategoryId === 0 && empty($search)): ?>
                        <div class="flex items-center gap-1.5 mb-3">
                            <h3 class="text-xs font-semibold text-stone-500 uppercase tracking-wider"><?php echo e(__('All Items')); ?></h3>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $regularProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <?php
                                $isOutOfStock = $product['stock_quantity'] <= 0;
                                $isLowStock = $product['stock_quantity'] > 0 && $product['stock_quantity'] <= 5;
                                $hasOptions = !empty($product['variants']) || !empty($product['modifier_groups']);
                            ?>
                            <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'product-'.e($product['id']).''; ?>wire:key="product-<?php echo e($product['id']); ?>"
                                wire:click="addItemQuick(<?php echo e($product['id']); ?>)"
                                class="group relative bg-white rounded-xl border-2 p-4 text-left transition-all duration-150 flex flex-col
                                    <?php echo e($isOutOfStock
                                        ? 'opacity-40 cursor-not-allowed border-stone-100'
                                        : 'hover:shadow-lg hover:-translate-y-0.5 active:scale-[0.97] cursor-pointer ' . ($hasOptions ? 'border-stone-200 hover:border-amber-300' : 'border-stone-200 hover:border-green-300')); ?>"
                            >
                                <div class="relative mb-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product['image']): ?>
                                        <img src="<?php echo e(asset('storage/' . $product['image'])); ?>" alt="<?php echo e($product['name']); ?>"
                                            class="w-full h-32 object-cover rounded-xl bg-stone-100">
                                    <?php else: ?>
                                        <div class="w-full h-32 bg-stone-100 rounded-xl flex items-center justify-center">
                                            <svg class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($product['tags'])): ?>
                                        <div class="absolute top-2 left-2 flex flex-wrap gap-1">
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = (array) $product['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tag === 'new'): ?>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-500 text-white shadow-sm"><?php echo e(__('NEW')); ?></span>
                                                <?php elseif($tag === 'signature'): ?>
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-purple-500 text-white shadow-sm"><?php echo e(__('SIGNATURE')); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isOutOfStock): ?>
                                        <div class="absolute inset-0 bg-black/40 rounded-xl flex items-center justify-center">
                                            <span class="text-white text-sm font-bold uppercase tracking-wider shadow-sm"><?php echo e(__('Sold Out')); ?></span>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasOptions && !$isOutOfStock): ?>
                                        <div class="absolute top-2 right-2 w-7 h-7 bg-green-500 text-white rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="flex-1 flex flex-col justify-between gap-1">
                                    <h3 class="font-semibold text-stone-800 text-sm leading-snug line-clamp-2"><?php echo e($product['name']); ?></h3>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($product['calories'])): ?>
                                        <p class="text-[10px] text-stone-400"><?php echo e($product['calories']); ?> kcal</p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($product['variants'])): ?>
                                        <?php
                                            $activeVariants = collect($product['variants'])->where('is_active', true);
                                            $minPrice = $product['price'] + ($activeVariants->min('price_adjustment') ?? 0);
                                            $maxPrice = $product['price'] + ($activeVariants->max('price_adjustment') ?? 0);
                                        ?>
                                        <p class="text-amber-600 font-bold text-sm mt-auto">
                                            $<?php echo e(number_format($minPrice, 2)); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($minPrice != $maxPrice): ?> <span class="text-amber-400 font-normal">-<?php echo e(number_format($maxPrice, 2)); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-amber-600 font-bold text-base mt-auto">$<?php echo e(number_format($product['price'], 2)); ?></p>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasOptions): ?>
                                            <span class="text-[10px] font-medium bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full"><?php echo e(__('Options')); ?></span>
                                        <?php elseif(!$isOutOfStock): ?>
                                            <span class="text-[10px] font-medium bg-green-50 text-green-600 px-2 py-0.5 rounded-full"><?php echo e(__('Quick add')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isLowStock && !$isOutOfStock): ?>
                                            <span class="text-[10px] font-medium bg-orange-50 text-orange-600 px-2 py-0.5 rounded-full"><?php echo e($product['stock_quantity']); ?> <?php echo e(__('left')); ?></span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </div>
                                </div>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="w-[380px] bg-white border-l border-stone-200 flex flex-col shrink-0">

        
        <div class="px-5 py-4 border-b border-stone-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="font-bold text-stone-800"><?php echo e(__('Cart')); ?></h2>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium bg-stone-100 text-stone-500 px-2 py-0.5 rounded-full">
                        <?php echo e($orderType === 'dine_in' ? __('Dine-in') : ($orderType === 'takeaway' ? __('Takeaway') : __('Delivery'))); ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tableNumber): ?> &middot; T<?php echo e($tableNumber); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </span>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->itemCount > 0): ?>
                        <span class="text-xs font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">
                            <?php echo e($this->itemCount); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="flex-1 overflow-y-auto">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($this->cartItems)): ?>
                <div class="flex flex-col items-center justify-center h-full text-stone-400 px-6">
                    <div class="w-20 h-20 bg-stone-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="font-medium text-stone-500"><?php echo e(__('Cart is empty')); ?></p>
                    <p class="text-sm text-stone-400 mt-1"><?php echo e(__('Tap a product to add it')); ?></p>
                </div>
            <?php else: ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cart-'.e($item['id']).''; ?>wire:key="cart-<?php echo e($item['id']); ?>" class="border-l-[3px] <?php echo e($loop->first ? 'border-l-transparent' : ''); ?> px-5 py-3 hover:bg-stone-50 transition-colors border-b border-stone-50 last:border-b-0">
                        <div class="flex items-start gap-3">
                            
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="font-semibold text-stone-800 text-sm leading-tight"><?php echo e($item['product_name'] ?? ($item['product']['name'] ?? __('Unknown'))); ?></h4>
                                    <p class="font-bold text-sm text-stone-800 shrink-0">$<?php echo e(number_format($item['total_price'], 2)); ?></p>
                                </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['variant_name'])): ?>
                                    <p class="text-xs text-stone-400 mt-0.5"><?php echo e($item['variant_name']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['modifiers'])): ?>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $item['modifiers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $modifier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <span class="text-[10px] bg-amber-50 text-amber-600 px-1.5 py-0.5 rounded">
                                                <?php echo e($modifier['modifier_option_name']); ?>

                                            </span>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($item['notes'])): ?>
                                    <p class="text-[10px] text-stone-400 italic mt-0.5"><?php echo e($item['notes']); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="flex items-center gap-2 mt-1.5">
                                    <div class="flex items-center gap-0 bg-stone-100 rounded-md">
                                        <button wire:click="updateQuantity(<?php echo e($item['id']); ?>, <?php echo e($item['quantity'] - 1); ?>)"
                                            class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-200 rounded-l-md transition-colors text-base font-medium">
                                            &minus;
                                        </button>
                                        <span class="min-w-[1.5rem] h-7 flex items-center justify-center text-xs font-bold text-stone-800"><?php echo e($item['quantity']); ?></span>
                                        <button wire:click="updateQuantity(<?php echo e($item['id']); ?>, <?php echo e($item['quantity'] + 1); ?>)"
                                            class="w-7 h-7 flex items-center justify-center text-stone-500 hover:text-stone-800 hover:bg-stone-200 rounded-r-md transition-colors text-base font-medium">
                                            +
                                        </button>
                                    </div>
                                    <span class="text-[10px] text-stone-400">$<?php echo e(number_format($item['unit_price'], 2)); ?> <?php echo e(__('each')); ?></span>
                                    <button wire:click="removeItem(<?php echo e($item['id']); ?>)"
                                        class="ml-auto text-stone-300 hover:text-red-500 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->itemCount > 0): ?>
            <div class="border-t border-stone-200 bg-white">
                
                <div class="px-5 pt-3">
                    <input type="text" wire:model.live="orderNotes" placeholder="<?php echo e(__('Order notes...')); ?>"
                        class="w-full px-3 py-2 border border-stone-200 rounded-lg text-xs focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50">
                </div>

                
                <div class="px-5 pt-2">
                    <div class="flex items-center gap-1.5">
                        <button wire:click="applyDiscountPercent(10)"
                            class="text-[10px] font-medium px-2 py-1 rounded border transition-colors
                                <?php echo e($this->isActiveDiscountPercent(10) ? 'bg-green-50 border-green-300 text-green-700' : 'border-stone-200 text-stone-500 hover:border-stone-300'); ?>">
                            10%
                        </button>
                        <button wire:click="applyDiscountPercent(20)"
                            class="text-[10px] font-medium px-2 py-1 rounded border transition-colors
                                <?php echo e($this->isActiveDiscountPercent(20) ? 'bg-green-50 border-green-300 text-green-700' : 'border-stone-200 text-stone-500 hover:border-stone-300'); ?>">
                            20%
                        </button>
                        <button wire:click="applyDiscountPercent(50)"
                            class="text-[10px] font-medium px-2 py-1 rounded border transition-colors
                                <?php echo e($this->isActiveDiscountPercent(50) ? 'bg-green-50 border-green-300 text-green-700' : 'border-stone-200 text-stone-500 hover:border-stone-300'); ?>">
                            50%
                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->order && $this->order->discount > 0): ?>
                            <button wire:click="applyDiscount(0)"
                                class="text-[10px] font-medium px-2 py-1 rounded border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                <?php echo e(__('Remove')); ?>

                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <div class="px-5 py-3 space-y-1.5">
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500"><?php echo e(__('Subtotal')); ?></span>
                        <span class="font-medium">$<?php echo e(number_format($this->subtotal, 2)); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-stone-500"><?php echo e(__('Tax')); ?> (<?php echo e(config('pos.tax_rate', 0.10) * 100); ?>%)</span>
                        <span class="font-medium">$<?php echo e(number_format($this->tax, 2)); ?></span>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->order && $this->order->discount > 0): ?>
                        <div class="flex justify-between text-sm text-green-600">
                            <span><?php echo e(__('Discount')); ?></span>
                            <span class="font-medium">-$<?php echo e(number_format($this->order->discount, 2)); ?></span>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="flex justify-between pt-2 border-t border-stone-100">
                        <span class="font-bold text-stone-800"><?php echo e(__('Total')); ?></span>
                        <span class="font-bold text-xl text-amber-600">$<?php echo e(number_format($this->total, 2)); ?></span>
                    </div>
                </div>

                
                <div class="px-5 pb-4">
                    <button wire:click="openPaymentModal"
                        class="w-full bg-amber-500 hover:bg-amber-600 active:bg-amber-700 text-white font-bold py-3.5 rounded-xl transition-colors text-base shadow-sm shadow-amber-200">
                        <?php echo e(__('Pay')); ?> $<?php echo e(number_format($this->total, 2)); ?>

                    </button>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showSuspendedOrders && count($this->suspendedOrders) > 0): ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'suspended-orders-panel'; ?>wire:key="suspended-orders-panel">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[80vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-stone-800"><?php echo e(__('Held Orders')); ?></h3>
                        <button wire:click="$set('showSuspendedOrders', false)"
                            class="w-7 h-7 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-2">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->suspendedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $held): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <button wire:click="resumeOrder(<?php echo e($held['id']); ?>)"
                                <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'held-'.e($held['id']).''; ?>wire:key="held-<?php echo e($held['id']); ?>"
                                class="w-full text-left p-3.5 rounded-xl border border-stone-200 hover:border-blue-300 hover:bg-blue-50 transition-all flex items-center justify-between group">
                                <div>
                                    <p class="font-semibold text-stone-800 text-sm"><?php echo e($held['order_number'] ?? '#' . $held['id']); ?></p>
                                    <p class="text-xs text-stone-400 mt-0.5">
                                        <?php echo e($held['items_count'] ?? 0); ?> <?php echo e(__('items')); ?>

                                        &middot; <?php echo e(\Carbon\Carbon::parse($held['updated_at'])->diffForHumans()); ?>

                                    </p>
                                </div>
                                <div class="text-blue-500 group-hover:translate-x-0.5 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </div>
                            </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($this->suspendedOrders) === 0): ?>
                        <p class="text-center text-stone-400 py-6 text-sm"><?php echo e(__('No held orders')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showModifierModal && $selectedProduct): ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    
                    <div class="flex items-center justify-between mb-5">
                        <div>
                            <h3 class="text-lg font-bold text-stone-800"><?php echo e($selectedProduct['name']); ?></h3>
                            <p class="text-sm text-stone-400"><?php echo e(__('Customize your order')); ?></p>
                        </div>
                        <button wire:click="cancelModifierModal" class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($selectedProduct['variants'])): ?>
                        <?php $activeVariants = collect($selectedProduct['variants'])->where('is_active', true); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($activeVariants->isNotEmpty()): ?>
                            <div class="mb-5">
                                <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2"><?php echo e(__('Size')); ?></label>
                                <div class="grid grid-cols-3 gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $activeVariants; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $variant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'variant-'.e($variant['id']).''; ?>wire:key="variant-<?php echo e($variant['id']); ?>"
                                            wire:click="selectVariant(<?php echo e($variant['id']); ?>)"
                                            class="py-2.5 px-3 rounded-xl border-2 text-sm font-medium transition-all
                                                <?php echo e($selectedVariant && $selectedVariant['id'] == $variant['id']
                                                    ? 'border-amber-500 bg-amber-50 text-amber-700'
                                                    : 'border-stone-200 hover:border-stone-300 text-stone-600'); ?>">
                                            <?php echo e($variant['name']); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($variant['price_adjustment'] > 0): ?>
                                                <span class="block text-xs font-normal mt-0.5">+$<?php echo e(number_format($variant['price_adjustment'], 2)); ?></span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </button>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($selectedProduct['modifier_groups'])): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $selectedProduct['modifier_groups']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="mb-5">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">
                                        <?php echo e($group['name']); ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['is_required']): ?>
                                            <span class="text-red-500">*</span>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($group['max_selections'] > 1): ?>
                                        <span class="text-[10px] text-stone-400 font-medium"><?php echo e(__('Max')); ?> <?php echo e($group['max_selections']); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $group['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($option['is_active']): ?>
                                            <?php
                                                $isSelected = collect($selectedModifiers)->contains('modifier_option_id', $option['id']);
                                            ?>
                                            <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'mod-'.e($option['id']).''; ?>wire:key="mod-<?php echo e($option['id']); ?>"
                                                wire:click="toggleModifier(<?php echo e($option['id']); ?>)"
                                                class="py-2 px-3.5 rounded-xl border-2 text-sm font-medium transition-all
                                                    <?php echo e($isSelected
                                                        ? 'border-amber-500 bg-amber-50 text-amber-700'
                                                        : 'border-stone-200 hover:border-stone-300 text-stone-600'); ?>">
                                                <?php echo e($option['name']); ?>

                                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($option['price'] > 0): ?>
                                                    <span class="text-xs font-normal">+$<?php echo e(number_format($option['price'], 2)); ?></span>
                                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                            </button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2"><?php echo e(__('Quantity')); ?></label>
                        <div class="flex items-center gap-3">
                            <div class="inline-flex items-center bg-stone-100 rounded-xl">
                                <button wire:click="$set('itemQuantity', max(1, $this->itemQuantity - 1))"
                                    class="w-12 h-12 flex items-center justify-center text-stone-600 hover:text-stone-800 rounded-l-xl hover:bg-stone-200 transition-colors text-xl font-medium">
                                    &minus;
                                </button>
                                <span class="w-14 h-12 flex items-center justify-center text-lg font-bold text-stone-800"><?php echo e($this->itemQuantity); ?></span>
                                <button wire:click="$set('itemQuantity', $this->itemQuantity + 1)"
                                    class="w-12 h-12 flex items-center justify-center text-stone-600 hover:text-stone-800 rounded-r-xl hover:bg-stone-200 transition-colors text-xl font-medium">
                                    +
                                </button>
                            </div>
                            <div class="flex gap-1.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [1, 2, 3, 4, 5]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'qty-'.e($qty).''; ?>wire:key="qty-<?php echo e($qty); ?>" wire:click="$set('itemQuantity', <?php echo e($qty); ?>)"
                                        class="w-10 h-12 rounded-xl text-sm font-bold transition-all
                                            <?php echo e($this->itemQuantity === $qty
                                                ? 'bg-amber-500 text-white shadow-sm'
                                                : 'bg-stone-100 text-stone-600 hover:bg-stone-200'); ?>">
                                        <?php echo e($qty); ?>

                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    </div>

                    
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2"><?php echo e(__('Special Instructions')); ?></label>
                        <input type="text" wire:model.live="itemNotes" placeholder="<?php echo e(__('e.g. Extra hot, no sugar...')); ?>"
                            class="w-full px-4 py-2.5 border border-stone-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-stone-50">
                    </div>

                    
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 space-y-1.5">
                        <div class="flex justify-between text-sm">
                            <span class="text-stone-500"><?php echo e(__('Base price')); ?></span>
                            <span class="font-medium">$<?php echo e(number_format($selectedProduct['price'], 2)); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedVariant && $selectedVariant['price_adjustment'] > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500">Size (<?php echo e($selectedVariant['name']); ?>)</span>
                                <span class="font-medium text-amber-600">+$<?php echo e(number_format($selectedVariant['price_adjustment'], 2)); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(collect($selectedModifiers)->sum('price') > 0): ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-stone-500"><?php echo e(__('Modifiers')); ?></span>
                                <span class="font-medium text-amber-600">+$<?php echo e(number_format(collect($selectedModifiers)->sum('price'), 2)); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php
                            $unitPrice = $selectedProduct['price']
                                + ($selectedVariant['price_adjustment'] ?? 0)
                                + collect($selectedModifiers)->sum('price');
                        ?>
                        <div class="flex justify-between pt-2 border-t border-stone-200">
                            <span class="font-bold text-stone-800"><?php echo e(__('Unit Price')); ?></span>
                            <span class="font-bold text-amber-600 text-lg">$<?php echo e(number_format($unitPrice, 2)); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($itemQuantity > 1): ?>
                            <div class="flex justify-between text-sm text-stone-500">
                                <span>&times;<?php echo e($itemQuantity); ?></span>
                                <span class="font-bold text-stone-800">$<?php echo e(number_format($unitPrice * $itemQuantity, 2)); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="flex gap-3">
                        <button wire:click="cancelModifierModal"
                            class="flex-1 px-4 py-3 border border-stone-200 rounded-xl hover:bg-stone-50 text-stone-600 font-medium transition-colors">
                            <?php echo e(__('Cancel')); ?>

                        </button>
                        <button wire:click="confirmAddItem"
                            class="flex-1 px-4 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold transition-colors shadow-sm shadow-amber-200">
                            <?php echo e(__('Add')); ?> &minus; $<?php echo e(number_format($unitPrice * $itemQuantity, 2)); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showPaymentModal): ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-stone-800"><?php echo e(__('Payment')); ?></h3>
                        <span class="text-2xl font-bold text-amber-600">$<?php echo e(number_format($this->total, 2)); ?></span>
                    </div>

                    
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2"><?php echo e(__('Method')); ?></label>
                        <div class="grid grid-cols-<?php echo e($this->isKhqrAvailable ? 2 : 1); ?> gap-3">
                            <button wire:click="selectPaymentMethod('cash')"
                                class="py-3.5 px-4 rounded-xl border-2 text-sm font-semibold transition-all flex flex-row items-center justify-center gap-2
                                    <?php echo e($paymentMethod === 'cash' ? 'border-amber-500 bg-amber-50 text-amber-700 ring-1 ring-amber-500' : 'border-stone-200 hover:border-stone-300 text-stone-600'); ?>">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <?php echo e(__('Cash')); ?>

                            </button>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->isKhqrAvailable): ?>
                                <button wire:click="selectPaymentMethod('khqr')"
                                    class="py-3.5 px-4 rounded-xl border-2 text-sm font-semibold transition-all flex flex-row items-center justify-center gap-2
                                        <?php echo e($paymentMethod === 'khqr' ? 'border-amber-500 bg-amber-50 text-amber-700 ring-1 ring-amber-500' : 'border-stone-200 hover:border-stone-300 text-stone-600'); ?>">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                    <?php echo e(__('KHQR')); ?>

                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === 'cash'): ?>
                        
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2"><?php echo e(__('Quick Amount')); ?></label>
                            <div class="grid grid-cols-4 gap-2">
                                <?php $denominations = config('pos.cash_denominations', [1, 2, 5, 10, 20, 50, 100]); ?>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $denominations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'cash-'.e($amount).''; ?>wire:key="cash-<?php echo e($amount); ?>" wire:click="$set('amountTendered', <?php echo e($amount); ?>)"
                                        class="py-2.5 rounded-lg text-sm font-semibold transition-all
                                            <?php echo e($amountTendered == $amount
                                                ? 'ring-2 ring-amber-500 bg-amber-50 text-amber-700'
                                                : ($amount >= $this->total ? 'bg-stone-100 hover:bg-stone-200 text-stone-700' : 'bg-stone-50 text-stone-400')); ?>">
                                        $<?php echo e($amount); ?>

                                    </button>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <button wire:click="$set('amountTendered', <?php echo e($this->total); ?>)"
                                    class="py-2.5 rounded-lg text-sm font-bold transition-all
                                        <?php echo e($amountTendered == $this->total
                                            ? 'ring-2 ring-green-500 bg-green-50 text-green-700'
                                            : 'bg-green-100 hover:bg-green-200 text-green-700'); ?>">
                                    <?php echo e(__('Exact')); ?>

                                </button>
                            </div>
                        </div>

                        
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2"><?php echo e(__('Amount Tendered')); ?></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-stone-300">$</span>
                                <input type="number" wire:model.live="amountTendered" step="0.01" min="0"
                                    class="w-full pl-10 pr-4 py-3.5 border-2 border-stone-200 rounded-xl text-2xl font-bold text-center focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white"
                                    placeholder="0.00" x-ref="cashInput" x-init="$nextTick(() => $refs.cashInput.focus()); $refs.cashInput.select()">
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 space-y-2">
                        <div class="flex justify-between">
                            <span class="text-stone-500 text-sm"><?php echo e(__('Subtotal')); ?></span>
                            <span class="font-medium text-sm">$<?php echo e(number_format($this->subtotal, 2)); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->order && $this->order->discount > 0): ?>
                            <div class="flex justify-between text-green-600 text-sm">
                                <span><?php echo e(__('Discount')); ?></span>
                                <span class="font-medium">-$<?php echo e(number_format($this->order->discount, 2)); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="flex justify-between border-t border-stone-200 pt-2">
                            <span class="font-bold text-stone-800"><?php echo e(__('Total')); ?></span>
                            <span class="font-bold text-xl text-amber-600">$<?php echo e(number_format($this->total, 2)); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === 'cash' && $amountTendered > 0): ?>
                            <div class="flex justify-between pt-1 border-t border-dashed border-stone-300">
                                <span class="font-semibold text-sm"><?php echo e($amountTendered >= $this->total ? __('Change') : __('Remaining')); ?></span>
                                <span class="font-bold text-lg <?php echo e($amountTendered >= $this->total ? 'text-green-600' : 'text-red-500'); ?>">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($amountTendered >= $this->total): ?>
                                        $<?php echo e(number_format($amountTendered - $this->total, 2)); ?>

                                    <?php else: ?>
                                        -$<?php echo e(number_format($this->total - $amountTendered, 2)); ?>

                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    
                    <div class="flex gap-3">
                        <button wire:click="$set('showPaymentModal', false)"
                            class="flex-1 px-4 py-3 border border-stone-200 rounded-xl hover:bg-stone-50 text-stone-600 font-medium transition-colors">
                            <?php echo e(__('Cancel')); ?>

                        </button>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paymentMethod === 'cash'): ?>
                            <button wire:click="processPayment"
                                class="flex-[2] px-4 py-3 bg-green-500 hover:bg-green-600 active:bg-green-700 text-white rounded-xl font-bold transition-all shadow-sm shadow-green-200 flex items-center justify-center gap-2 text-base
                                    <?php echo e($amountTendered < $this->total ? 'opacity-50 cursor-not-allowed' : ''); ?>"
                                <?php if($amountTendered < $this->total): ?> disabled <?php endif; ?>
                                wire:loading.attr="disabled">
                                <svg wire:loading wire:target="processPayment" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <span wire:loading.remove wire:target="processPayment">
                                    <?php echo e($amountTendered >= $this->total ? __('Complete Sale') : __('Pay')); ?> $<?php echo e(number_format(min($this->total, $amountTendered), 2)); ?>

                                </span>
                                <span wire:loading wire:target="processPayment"><?php echo e(__('Processing...')); ?></span>
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showKhqrModal && $khqrData): ?>
        <?php
            $qrSrc = $khqrData['qr_image_url'] ?? '';
            if (empty($qrSrc) && !empty($khqrData['qr_data'])) {
                $isBase64 = base64_decode($khqrData['qr_data'], true) !== false;
                if ($isBase64) {
                    $qrSrc = 'data:image/png;base64,' . $khqrData['qr_data'];
                }
            }
        ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-data="{ pollInterval: null, timerInterval: null, pollCount: 0, maxPolls: 120 }"
            x-init="clearInterval(pollInterval); pollInterval = setInterval(() => { if (pollCount < maxPolls) { pollCount++; $wire.checkKhqrStatus(); } else { clearInterval(pollInterval); } }, 5000)"
            x-on:keydown.escape.window="clearInterval(pollInterval); clearInterval(timerInterval)"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-bold text-stone-800"><?php echo e(__('KHQR Payment')); ?></h3>
                        <button wire:click="cancelKhqr"
                            class="w-8 h-8 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition-colors"
                            x-on:click="clearInterval(pollInterval); clearInterval(timerInterval)">
                            <svg class="w-4 h-4 text-stone-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="text-center mb-5">
                        <p class="text-sm text-stone-500 mb-3"><?php echo e(__('Scan with your banking app')); ?></p>
                        <div class="bg-white border-2 border-stone-100 rounded-xl p-4 inline-block">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($qrSrc)): ?>
                                <img src="<?php echo e($qrSrc); ?>" alt="KHQR Code" class="w-48 h-48">
                            <?php else: ?>
                                <div class="w-48 h-48 bg-stone-50 flex items-center justify-center">
                                    <svg class="w-12 h-12 text-stone-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                        <p class="mt-3 text-2xl font-bold text-amber-600">$<?php echo e(number_format($khqrData['amount'], 2)); ?></p>
                    </div>

                    
                    <div class="bg-stone-50 rounded-xl p-4 mb-5 text-center">
                        <p class="text-xs text-stone-400 mb-1"><?php echo e(__('Expires in')); ?></p>
                        <p class="text-3xl font-bold text-stone-800 font-mono"
                            x-data="{ seconds: <?php echo e($qrExpirySeconds); ?> }"
                            x-init="timerInterval = setInterval(() => { if(seconds > 0) seconds--; }, 1000)"
                            x-text="Math.floor(seconds/60).toString().padStart(2,'0') + ':' + (seconds%60).toString().padStart(2,'0')">
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button wire:click="checkKhqrStatus"
                            class="flex-1 px-4 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-xl font-bold transition-colors flex items-center justify-center gap-2"
                            wire:loading.attr="disabled">
                            <svg wire:loading wire:target="checkKhqrStatus" class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span wire:loading.remove wire:target="checkKhqrStatus"><?php echo e(__('Check Payment')); ?></span>
                            <span wire:loading wire:target="checkKhqrStatus"><?php echo e(__('Checking...')); ?></span>
                        </button>
                        <button wire:click="generateKhqr"
                            class="flex-1 px-4 py-3 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-bold transition-colors"
                            wire:loading.attr="disabled"
                            x-on:click="clearInterval(pollInterval); clearInterval(timerInterval)">
                            <?php echo e(__('Refresh QR')); ?>

                        </button>
                    </div>

                    <p class="text-xs text-stone-400 text-center mt-4">
                        <?php echo e(__('Auto-confirms after payment')); ?>

                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($showReceiptModal): ?>
        <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-auto"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div class="p-5">
                    <div class="text-center mb-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-stone-800"><?php echo e(__('Payment Complete')); ?></h3>
                        <p class="text-xs text-stone-400 mt-0.5"><?php echo e(__('Order has been processed successfully')); ?></p>
                    </div>

                    <div class="bg-white rounded-xl p-3 font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 border border-stone-200 shadow-inner"
                        x-data="{ printing: false }">
                        <?php echo e($receiptContent); ?>

                    </div>

                    <div class="flex gap-2.5 mt-4">
                        <button wire:click="printReceipt"
                            class="flex-1 px-3 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-semibold transition-colors flex items-center justify-center gap-1.5 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9V2h12v7M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 18v4h12v-4M6 18v-2h12v2"/></svg>
                            <?php echo e(__('Print')); ?>

                        </button>
                        <button wire:click="newOrder"
                            class="flex-1 px-3 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold transition-colors shadow-sm shadow-amber-200 text-sm">
                            <?php echo e(__('New Order')); ?>

                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div x-show="processing" class="fixed inset-0 bg-black/20 z-[60] flex items-center justify-center"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-6 flex items-center gap-4">
            <svg class="animate-spin h-6 w-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-stone-700 font-medium">Processing...</span>
        </div>
    </div>

    
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:show-toast.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 3000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50"
    >
        <div class="px-5 py-3 rounded-xl shadow-lg text-sm font-medium flex items-center gap-2"
            :class="type === 'success' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'">
            <template x-if="type === 'success'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </template>
            <span x-text="message"></span>
        </div>
    </div>
</div>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views/livewire/pos/pos-terminal.blade.php ENDPATH**/ ?>