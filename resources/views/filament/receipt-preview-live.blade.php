@php
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
@endphp
<div class="rounded-xl bg-gray-50 dark:bg-gray-900/50 ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden sticky top-4" x-data>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
        <x-filament::icon icon="heroicon-o-eye" class="w-4 h-4 text-gray-400" />
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Receipt Preview</span>
        <span class="text-xs px-1.5 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-mono">{{ $width }}w</span>
        @if($template)
            <span class="text-xs px-1.5 py-0.5 rounded font-medium"
                style="background:{{ match($template) { 'classic' => '#e0e7ff', 'minimal' => '#f0fdf4', 'detailed' => '#fef3c7', 'compact' => '#fce7f3', 'branded' => '#ede9fe', default => '#e0e7ff' } }};color:{{ match($template) { 'classic' => '#3730a3', 'minimal' => '#166534', 'detailed' => '#92400e', 'compact' => '#9d174d', 'branded' => '#5b21b6', default => '#3730a3' } }}">
                {{ $t['label'] }}
            </span>
        @endif
        <span class="ml-auto text-xs text-gray-400 animate-pulse">live</span>
    </div>
    <div class="p-5 flex justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow-sm" style="max-width:340px;width:100%">
            <div class="font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 dark:text-stone-300" style="max-height:400px;overflow-y:auto">
@if($outer)
{{ $outer }}
@endif
@if($showLogo && $logoUrl)
<img src="{{ $logoUrl }}" alt="Logo" style="display:block;margin:0 auto 4px;max-height:32px;max-width:{{ min($width * 6, 180) }}px;object-fit:contain">
@endif
<div @style(['text-align:center' => $t['header_align'] === 'center'])>{{ strtoupper($shopName) }}</div>
@if($showAddress && $address)
<div @style(['text-align:center' => $t['header_align'] === 'center'])>{{ $address }}</div>
@endif
@if($showPhone && $phone)
<div @style(['text-align:center' => $t['header_align'] === 'center'])>{{ 'Tel: '.$phone }}</div>
@endif
{{ $outer ?: '' }}
@if($header)
{{ $header }}
{{ $inner }}
@endif
@php
    $metaParts = [];
    if ($showOrderType) $metaParts[] = 'Type: Dine-in';
    if ($showTable) $metaParts[] = 'Table: 5';
    $metaLine = implode('  ', $metaParts);
@endphp
Order #: ORD-20260623-0001{{ $tight ? ' | ' : "\n" }}Date: 23 Jun 2026, 14:30
@if($metaLine)
{{ $metaLine }}
@endif
@if($showCashier)
Cashier: Admin
@endif
{{ $gap }}
{{ $inner }}
QTY  ITEM                    PRICE
{{ $inner }}
1x   Cold Brew Latte         4.50
2x   Blueberry Muffin        7.00
@if($showModifiers)
     + Extra Blueberries
@endif
1x   Espresso                3.00
{{ $inner }}
{{ $tight ? '' : "\n" }}Subtotal                    14.50
@if($showDiscount)
Discount                   -1.45
@endif
Tax                         1.31
{{ $inner }}
TOTAL                      14.36
{{ $inner }}
@if($showPayment)
Paid                        20.00
Change                      5.64
@endif
@if($footer)
{{ $inner }}
{{ $footer }}
@endif
@if($outer)
{{ $outer }}
@else
{{ $inner }}
@endif
            </div>
        </div>
    </div>
</div>
