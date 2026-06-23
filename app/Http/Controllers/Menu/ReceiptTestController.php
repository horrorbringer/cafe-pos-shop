<?php

namespace App\Http\Controllers\Menu;

use App\Domain\Shop\Models\Setting;
use Illuminate\View\View;

class ReceiptTestController
{
    public function __invoke(): View
    {
        $shopName = Setting::getValue('shop_name', config('app.name', 'POS Cafe'));
        $address = Setting::getValue('shop_address', '');
        $phone = Setting::getValue('shop_phone', '');
        $header = Setting::getValue('receipt_header', '');
        $footer = Setting::getValue('receipt_footer', 'Thank you!');
        $currency = Setting::getValue('shop_currency', 'USD');

        $template = Setting::getValue('receipt_template', 'classic');

        $sampleItems = [
            ['name' => 'Cold Brew Latte', 'qty' => 1, 'price' => 4.50],
            ['name' => 'Blueberry Muffin', 'qty' => 2, 'price' => 3.50],
            ['name' => 'Espresso', 'qty' => 1, 'price' => 3.00],
        ];

        $subtotal = collect($sampleItems)->sum(fn ($i) => $i['qty'] * $i['price']);
        $discount = 1.45;
        $taxRate = (float) Setting::getValue('shop_tax_rate', 0);
        $tax = round($subtotal * $taxRate / 100, 2);
        $total = $subtotal - $discount + $tax;
        $paid = 20.00;
        $change = $paid - $total;

        $currencySymbol = match ($currency) {
            'KHR' => '៛',
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };

        return view('menu.test-receipt', compact(
            'shopName', 'address', 'phone', 'header', 'footer', 'currencySymbol',
            'sampleItems', 'subtotal', 'discount', 'tax', 'total', 'paid', 'change',
            'template'
        ));
    }
}
