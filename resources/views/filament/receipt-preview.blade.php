@php
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
@endphp

<div class="mt-8">
    <div class="flex items-center gap-2 mb-3">
        <x-filament::icon icon="heroicon-o-eye" class="w-5 h-5 text-gray-400" />
        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Receipt Preview</span>
        <span class="text-xs text-gray-400 dark:text-gray-500">({{ $width }} chars width)</span>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
        <div class="p-6 flex justify-center">
            <div class="bg-stone-50 dark:bg-gray-800 rounded-lg p-5 shadow-sm" style="max-width:360px;width:100%">
                <div class="font-mono text-xs leading-relaxed whitespace-pre-wrap text-stone-700 dark:text-stone-300" style="max-height:400px;overflow-y:auto">
                    {{ str_repeat('=', $width) }}
                    {{ strtoupper($shopName) }}
                    @if($showAddress && $address)
                        {{ $address }}
                    @endif
                    @if($showPhone && $phone)
                        Tel: {{ $phone }}
                    @endif
                    {{ str_repeat('=', $width) }}

                    @if($header)
                        {{ $header }}
                        {{ str_repeat('-', $width) }}
                    @endif

                    Order #: ORD-20260623-0001
                    Date: 23 Jun 2026, 14:30
                    Type: Dine-in
                    Table: 5
                    Cashier: Admin

                    {{ str_repeat('-', $width) }}
                    QTY  ITEM                    PRICE
                    {{ str_repeat('-', $width) }}
                    1x   Cold Brew Latte         4.50
                    2x   Blueberry Muffin        7.00
                         + Extra Blueberries
                    1x   Espresso                3.00
                    {{ str_repeat('-', $width) }}
                    Subtotal                    14.50
                    Discount                   -1.45
                    Tax                         1.31
                    {{ str_repeat('-', $width) }}
                    TOTAL                      14.36
                    {{ str_repeat('-', $width) }}

                    Paid                        20.00
                    Change                      5.64

                    @if($footer)
                        {{ str_repeat('-', $width) }}
                        {{ $footer }}
                    @endif
                    {{ str_repeat('=', $width) }}
                </div>
            </div>
        </div>
        <div class="px-6 pb-4 text-center text-xs text-gray-400 dark:text-gray-500">
            Preview updates after saving settings.
        </div>
    </div>
</div>
