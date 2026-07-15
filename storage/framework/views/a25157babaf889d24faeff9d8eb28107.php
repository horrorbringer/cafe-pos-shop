<div class="flex gap-4 h-[calc(100vh-10rem)]" x-data="{ copied: false }" x-on:copy-to-clipboard.window="navigator.clipboard.writeText($event.detail.url); copied = true; setTimeout(() => copied = false, 2000)">

    
    <div class="w-80 shrink-0 flex flex-col gap-3 overflow-y-auto">

        
        <div class="filament-card rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white"><?php echo e(__('QR Code')); ?></h3>
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-qr-code'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-gray-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->qrCodeDataUri): ?>
                <div class="flex justify-center mb-3">
                    <img src="<?php echo e($this->qrCodeDataUri); ?>" alt="QR Code" class="w-32 h-32 rounded-lg">
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->menuUrl): ?>
                <div class="flex items-center gap-1.5">
                    <input type="text" value="<?php echo e($this->menuUrl); ?>" readonly
                        class="flex-1 px-2 py-1.5 bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-lg text-xs text-gray-600 dark:text-gray-400 font-mono truncate">
                    <button wire:click="copyUrl"
                        class="shrink-0 px-2.5 py-1.5 bg-primary-600 hover:bg-primary-500 text-white text-xs font-medium rounded-lg transition-colors">
                        <span x-show="!copied"><?php echo e(__('Copy')); ?></span>
                        <span x-show="copied" x-cloak><?php echo e(__('Copied!')); ?></span>
                    </button>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        
        <div class="filament-card rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white"><?php echo e(__('Appearance')); ?></h3>
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-paint-brush'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-gray-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            </div>
            <div class="space-y-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->getFormSchema(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->getName() !== 'isEnabled'): ?>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1"><?php echo e($field->getLabel()); ?></label>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->getName() === 'menuTitle'): ?>
                                <input type="text" wire:model.live="menuTitle" wire:blur="saveTitle"
                                    placeholder="<?php echo e(config('app.name', 'POS Cafe')); ?>"
                                    class="w-full px-2.5 py-1.5 border border-gray-200 dark:border-white/10 rounded-lg text-sm bg-white dark:bg-white/5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <?php elseif($field->getName() === 'menuSubtitle'): ?>
                                <textarea wire:model.live="menuSubtitle" wire:blur="saveSubtitle" rows="2"
                                    placeholder="Welcome! Scan to order."
                                    class="w-full px-2.5 py-1.5 border border-gray-200 dark:border-white/10 rounded-lg text-sm bg-white dark:bg-white/5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none"></textarea>
                            <?php elseif($field->getName() === 'primaryColor'): ?>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model.live="primaryColor" wire:blur="saveColor"
                                        class="w-8 h-8 rounded-lg border border-gray-200 dark:border-white/10 cursor-pointer">
                                    <input type="text" wire:model.live="primaryColor" wire:blur="saveColor"
                                        class="flex-1 px-2.5 py-1.5 border border-gray-200 dark:border-white/10 rounded-lg text-sm font-mono bg-white dark:bg-white/5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            <?php elseif($field->getName() === 'logoPath'): ?>
                                <div wire:ignore>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logoPath): ?>
                                        <div class="relative mb-2">
                                            <img src="<?php echo e(asset('storage/' . $logoPath)); ?>" class="h-10 object-contain rounded">
                                            <button wire:click="removeLogo"
                                                class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600">
                                                &times;
                                            </button>
                                        </div>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <label class="flex items-center justify-center w-full h-16 border-2 border-dashed border-gray-200 dark:border-white/10 rounded-lg cursor-pointer hover:border-gray-400 dark:hover:border-white/20 transition-colors">
                                        <div class="text-center">
                                            <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-photo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-5 h-5 mx-auto text-gray-400 mb-0.5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                                            <span class="text-[10px] text-gray-400"><?php echo e(__('Upload logo')); ?></span>
                                        </div>
                                        <input type="file" accept="image/*" class="hidden"
                                            x-ref="logoInput"
                                            x-on:change="
                                                const file = $refs.logoInput.files[0];
                                                if (file) {
                                                    const fd = new FormData();
                                                    fd.append('file', file);
                                                    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
                                                    fetch('/admin/upload/menu-logo', { method: 'POST', body: fd })
                                                        .then(r => r.json())
                                                        .then(d => {
                                                            $wire.set('logoPath', d.path || d.full_path);
                                                            $wire.call('saveLogo');
                                                        });
                                                }
                                            ">
                                    </label>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                
                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-white/5">
                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300"><?php echo e(__('Enable Menu')); ?></span>
                    <button wire:click="$set('isEnabled', <?php echo e($isEnabled ? 'false' : 'true'); ?>); saveEnabled"
                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors <?php echo e($isEnabled ? 'bg-primary-600' : 'bg-gray-300 dark:bg-gray-600'); ?>">
                        <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform <?php echo e($isEnabled ? 'translate-x-4' : 'translate-x-0.5'); ?>"></span>
                    </button>
                </div>
            </div>
        </div>

        
        <div class="filament-card rounded-xl bg-white p-4 shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white"><?php echo e(__('Social Media')); ?></h3>
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-share'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-gray-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            </div>
            <div class="space-y-4">
                <?php
                    $socialPlatforms = [
                        ['handle' => 'socialFacebook', 'prefix' => 'facebook.com/'],
                        ['handle' => 'socialInstagram', 'prefix' => 'instagram.com/'],
                        ['handle' => 'socialTiktok', 'prefix' => 'tiktok.com/@'],
                        ['handle' => 'socialYoutube', 'prefix' => 'youtube.com/@'],
                        ['handle' => 'socialTelegram', 'prefix' => 't.me/'],
                        ['handle' => 'socialTwitter', 'prefix' => 'x.com/'],
                    ];
                ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $socialPlatforms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $platform): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <div class="flex items-center gap-0 rounded-lg border border-gray-200 dark:border-white/10 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500 focus-within:border-primary-500">
                        <span class="px-2.5 py-1.5 text-xs text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-white/5 truncate shrink-0"><?php echo e($platform['prefix']); ?></span>
                        <input type="text" wire:model.live="<?php echo e($platform['handle']); ?>" wire:blur="saveSocialLinks"
                            placeholder="yourhandle"
                            class="flex-1 min-w-0 px-2.5 py-1.5 text-sm bg-white dark:bg-white/5 border-0 focus:ring-0">
                    </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="flex-1 filament-card rounded-xl bg-white shadow-sm dark:bg-gray-900 ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden flex flex-col">
        <div class="flex items-center justify-between px-4 py-2 border-b border-gray-100 dark:border-white/5">
            <div class="flex items-center gap-2">
                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-eye'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 text-gray-400']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
                <h3 class="text-xs font-semibold text-gray-950 dark:text-white"><?php echo e(__('Live Preview')); ?></h3>
            </div>
            <a href="<?php echo e(url('/menu')); ?>" target="_blank" class="text-xs text-primary-600 hover:text-primary-500 font-medium flex items-center gap-1">
                <?php echo e(__('Open full page')); ?>

                <?php if (isset($component)) { $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c = $attributes; } ?>
<?php $component = BladeUI\Icons\Components\Svg::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('heroicon-o-arrow-top-right-on-square'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\BladeUI\Icons\Components\Svg::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-3 h-3']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $attributes = $__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__attributesOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c)): ?>
<?php $component = $__componentOriginal643fe1b47aec0b76658e1a0200b34b2c; ?>
<?php unset($__componentOriginal643fe1b47aec0b76658e1a0200b34b2c); ?>
<?php endif; ?>
            </a>
        </div>
        <div class="flex-1 bg-gray-100 dark:bg-black/20 p-3">
            <iframe src="<?php echo e(url('/menu')); ?>" class="w-full h-full border-0 rounded-lg bg-white" loading="lazy"></iframe>
        </div>
    </div>
</div>
<?php /**PATH D:\Herd\cafe-pos-shop\resources\views\filament\pages\digital-menu.blade.php ENDPATH**/ ?>