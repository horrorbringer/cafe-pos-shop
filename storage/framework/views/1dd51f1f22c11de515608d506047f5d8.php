<div class="bg-white rounded-lg shadow-sm">
    
    <div class="p-6 border-b">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold text-gray-800"><?php echo e(__('Order History')); ?></h2>
        </div>

        
        <div class="flex flex-wrap gap-4">
            <input
                type="text"
                wire:model.live="search"
                placeholder="<?php echo e(__('Search order number...')); ?>"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
            <select
                wire:model.live="statusFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
                <option value=""><?php echo e(__('All Status')); ?></option>
                <option value="draft"><?php echo e(__('Draft')); ?></option>
                <option value="pending"><?php echo e(__('Pending')); ?></option>
                <option value="paid"><?php echo e(__('Paid')); ?></option>
                <option value="completed"><?php echo e(__('Completed')); ?></option>
                <option value="cancelled"><?php echo e(__('Cancelled')); ?></option>
                <option value="refunded"><?php echo e(__('Refunded')); ?></option>
            </select>
            <input
                type="date"
                wire:model.live="dateFilter"
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
            >
        </div>
    </div>

    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Order #')); ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Cashier')); ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Items')); ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Total')); ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Status')); ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Date')); ?></th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"><?php echo e(__('Actions')); ?></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->orders['data'] ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            <?php echo e($order['order_number']); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            <?php echo e($order['user']['name'] ?? __('Unknown')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                            <?php echo e(collect($order['items'])->sum('quantity')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                            $<?php echo e(number_format($order['total'], 2)); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php if($order['status'] === 'completed'): ?> bg-green-100 text-green-800
                                <?php elseif($order['status'] === 'paid'): ?> bg-blue-100 text-blue-800
                                <?php elseif($order['status'] === 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($order['status'] === 'cancelled'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>
                            ">
                                <?php echo e(ucfirst($order['status'])); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-500 text-sm">
                            <?php echo e(\Carbon\Carbon::parse($order['created_at'])->format('M d, Y H:i')); ?>

                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button
                                wire:click="viewOrder(<?php echo e($order['id']); ?>)"
                                class="text-amber-600 hover:text-amber-900"
                            >
                                <?php echo e(__('View')); ?>

                            </button>
                        </td>
                    </tr>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <?php echo e(__('No orders found')); ?>

                        </td>
                    </tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($this->orders['last_page']) && $this->orders['last_page'] > 1): ?>
        <div class="px-6 py-4 border-t">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <?php echo e(__('Showing')); ?> <?php echo e($this->orders['from']); ?> <?php echo e(__('to')); ?> <?php echo e($this->orders['to']); ?> <?php echo e(__('of')); ?> <?php echo e($this->orders['total']); ?> <?php echo e(__('orders')); ?>

                </div>
                <div class="flex gap-1">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= $this->orders['last_page']; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <button
                            wire:click="setPage(<?php echo e($i); ?>)"
                            class="px-3 py-1 text-sm rounded <?php echo e($this->orders['current_page'] === $i ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'); ?>"
                        >
                            <?php echo e($i); ?>

                        </button>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingOrder): ?>
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold"><?php echo e(__('Order Details')); ?></h3>
                        <button wire:click="closeOrder" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <p class="text-sm text-gray-500"><?php echo e(__('Order Number')); ?></p>
                            <p class="font-medium"><?php echo e($viewingOrder['order_number']); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500"><?php echo e(__('Cashier')); ?></p>
                            <p class="font-medium"><?php echo e($viewingOrder['user']['name'] ?? __('Unknown')); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500"><?php echo e(__('Status')); ?></p>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php if($viewingOrder['status'] === 'completed'): ?> bg-green-100 text-green-800
                                <?php elseif($viewingOrder['status'] === 'paid'): ?> bg-blue-100 text-blue-800
                                <?php elseif($viewingOrder['status'] === 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php elseif($viewingOrder['status'] === 'cancelled'): ?> bg-red-100 text-red-800
                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>
                            ">
                                <?php echo e(ucfirst($viewingOrder['status'])); ?>

                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500"><?php echo e(__('Date')); ?></p>
                            <p class="font-medium"><?php echo e(\Carbon\Carbon::parse($viewingOrder['created_at'])->format('M d, Y H:i')); ?></p>
                        </div>
                    </div>

                    
                    <div class="mb-6">
                        <h4 class="font-semibold mb-2"><?php echo e(__('Items')); ?></h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $viewingOrder['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="flex justify-between py-2 <?php echo e(!$loop->last ? 'border-b' : ''); ?>">
                                    <span><?php echo e($item['product']['name'] ?? 'Unknown'); ?> x<?php echo e($item['quantity']); ?></span>
                                    <span>$<?php echo e(number_format($item['total_price'], 2)); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </div>

                    
                    <div class="mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500"><?php echo e(__('Subtotal')); ?></span>
                            <span>$<?php echo e(number_format($viewingOrder['subtotal'], 2)); ?></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500"><?php echo e(__('Tax')); ?></span>
                            <span>$<?php echo e(number_format($viewingOrder['tax'], 2)); ?></span>
                        </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($viewingOrder['discount'] > 0): ?>
                            <div class="flex justify-between text-sm text-green-600">
                                <span><?php echo e(__('Discount')); ?></span>
                                <span>-$<?php echo e(number_format($viewingOrder['discount'], 2)); ?></span>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div class="flex justify-between font-bold mt-2 pt-2 border-t">
                            <span><?php echo e(__('Total')); ?></span>
                            <span>$<?php echo e(number_format($viewingOrder['total'], 2)); ?></span>
                        </div>
                    </div>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($viewingOrder['payments'])): ?>
                        <div class="mb-6">
                            <h4 class="font-semibold mb-2"><?php echo e(__('Payments')); ?></h4>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $viewingOrder['payments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-500"><?php echo e(ucfirst($payment['method'])); ?></span>
                                    <span>$<?php echo e(number_format($payment['amount'], 2)); ?></span>
                                </div>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($viewingOrder['statusLogs'])): ?>
                        <div>
                            <h4 class="font-semibold mb-2"><?php echo e(__('Status History')); ?></h4>
                            <div class="space-y-2">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $viewingOrder['statusLogs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="text-sm flex justify-between">
                                        <span class="text-gray-500">
                                            <?php echo e(ucfirst($log['from_status'] ?? __('Created'))); ?> → <?php echo e(ucfirst($log['to_status'])); ?>

                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($log['notes']): ?>
                                                <span class="text-gray-400">(<?php echo e($log['notes']); ?>)</span>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        </span>
                                        <span class="text-gray-400"><?php echo e(\Carbon\Carbon::parse($log['created_at'])->format('M d, H:i')); ?></span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views\livewire\pos\order-history.blade.php ENDPATH**/ ?>