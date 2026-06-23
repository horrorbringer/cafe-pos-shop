<?php

namespace App\Services;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shop\Models\Setting;

class ReceiptPrinterService
{
    private function getTemplateConfig(string $template): array
    {
        $templates = [
            'classic' => ['outer' => '=', 'inner' => '-', 'tight' => false, 'show_outer' => true],
            'minimal' => ['outer' => '', 'inner' => '-', 'tight' => true, 'show_outer' => false],
            'detailed' => ['outer' => '=', 'inner' => '=', 'tight' => false, 'show_outer' => true],
            'compact' => ['outer' => '-', 'inner' => '-', 'tight' => true, 'show_outer' => true],
            'branded' => ['outer' => '*', 'inner' => '·', 'tight' => false, 'show_outer' => true],
        ];

        return $templates[$template] ?? $templates['classic'];
    }

    public function getCurrencySymbol(): string
    {
        return match (Setting::getValue('shop_currency', 'USD')) {
            'KHR' => '៛',
            'EUR' => '€',
            'GBP' => '£',
            default => '$',
        };
    }

    public function generateReceiptContent(Order $order): string
    {
        $header = Setting::getValue('receipt_header', '');
        $footer = Setting::getValue('receipt_footer', '');
        $width = $this->getPaperWidth();
        $template = Setting::getValue('receipt_template', 'classic');
        $t = $this->getTemplateConfig($template);
        $currencySymbol = $this->getCurrencySymbol();

        $showAddress = Setting::getValue('receipt_show_address', true);
        $showPhone = Setting::getValue('receipt_show_phone', true);
        $showOrderType = Setting::getValue('receipt_show_order_type', true);
        $showTable = Setting::getValue('receipt_show_table', true);
        $showCashier = Setting::getValue('receipt_show_cashier', true);
        $showModifiers = Setting::getValue('receipt_show_modifiers', true);
        $showDiscount = Setting::getValue('receipt_show_discount', true);
        $showPayment = Setting::getValue('receipt_show_payment', true);
        $showNotes = Setting::getValue('receipt_show_notes', true);

        $outer = $t['show_outer'] ? str_repeat($t['outer'], $width) : '';
        $inner = str_repeat($t['inner'], $width);
        $gap = $t['tight'] ? '' : '';

        $lines = [];

        if ($outer) {
            $lines[] = $outer;
        }

        $lines[] = strtoupper(Setting::getValue('shop_name', config('app.name', 'POS Cafe')));

        $address = Setting::getValue('shop_address', '');
        if ($showAddress && $address) {
            $lines[] = $address;
        }

        $phone = Setting::getValue('shop_phone', '');
        if ($showPhone && $phone) {
            $lines[] = __('Tel:').' '.$phone;
        }

        if ($outer) {
            $lines[] = $outer;
        }

        if ($header) {
            $lines[] = $this->wrapText($header, $width);
            $lines[] = $inner;
        }

        $lines[] = __('Order #').': '.$order->order_number;
        $lines[] = __('Date').': '.$order->created_at->format('d M Y, H:i');

        $metaParts = [];
        if ($showOrderType) {
            $metaParts[] = __('Type').': '.$order->order_type->label();
        }
        if ($showTable && $order->table_number) {
            $metaParts[] = __('Table').': '.$order->table_number;
        }
        if ($metaParts) {
            $lines[] = implode('  ', $metaParts);
        }

        if ($showCashier) {
            $lines[] = __('Cashier').': '.$order->user->name;
        }

        if ($gap) {
            $lines[] = '';
        }
        $lines[] = $inner;
        $lines[] = sprintf('%-3s  %-'.($width - 12).'s  %5s', __('QTY'), __('ITEM'), __('PRICE'));
        $lines[] = $inner;

        $itemNameWidth = $width - 12;
        foreach ($order->items as $item) {
            $name = $item->product_name ?? $item->product->name;
            $formattedPrice = $currencySymbol.number_format($item->total_price, 2);
            $wrapped = $this->wordWrapItem($name, $itemNameWidth);
            foreach ($wrapped as $i => $line) {
                if ($i === 0) {
                    $lines[] = sprintf(
                        '%-3s  %-'.$itemNameWidth.'s  %5s',
                        $item->quantity.'x',
                        $line,
                        $formattedPrice
                    );
                } else {
                    $lines[] = sprintf(
                        '     %-'.($itemNameWidth + 7).'s',
                        $line
                    );
                }
            }

            if ($showModifiers) {
                $modifiers = $item->getModifierSummary();
                if ($modifiers) {
                    $modWrapped = $this->wordWrapItem($modifiers, $width - 5);
                    foreach ($modWrapped as $modLine) {
                        $lines[] = '     '.$modLine;
                    }
                }
            }
        }

        $lines[] = $inner;

        if ($gap) {
            $lines[] = '';
        }
        $lines[] = $this->alignPair(__('Subtotal'), $currencySymbol.number_format($order->subtotal, 2), $width);

        if ($showDiscount && $order->discount > 0) {
            $lines[] = $this->alignPair(__('Discount'), '-'.$currencySymbol.number_format($order->discount, 2), $width);
        }

        $lines[] = $this->alignPair(__('Tax'), $currencySymbol.number_format($order->tax, 2), $width);
        $lines[] = $inner;
        $lines[] = $this->alignPair(__('Total'), $currencySymbol.number_format($order->total, 2), $width, true);
        $lines[] = $inner;

        if ($showPayment) {
            $lines[] = '';
            $lines[] = $this->alignPair(__('Paid'), $currencySymbol.number_format($order->amount_paid, 2), $width);
            $lines[] = $this->alignPair(__('Change'), $currencySymbol.number_format($order->change_amount, 2), $width);
        }

        if ($showNotes && $order->notes) {
            $lines[] = '';
            $lines[] = __('Notes').': '.$order->notes;
        }

        if ($footer) {
            $lines[] = $inner;
            $lines[] = $this->wrapText($footer, $width);
        }

        $lines[] = '';
        $lines[] = str_pad(__('Thank you for your visit!'), $width, ' ', STR_PAD_BOTH);

        if ($outer) {
            $lines[] = $outer;
        } else {
            $lines[] = $inner;
        }

        return implode("\n", $lines);
    }

    public function generateReceiptHtml(Order $order): string
    {
        $shopName = Setting::getValue('shop_name', config('app.name', 'POS Cafe'));
        $address = Setting::getValue('shop_address', '');
        $phone = Setting::getValue('shop_phone', '');
        $header = Setting::getValue('receipt_header', '');
        $footer = Setting::getValue('receipt_footer', '');
        $template = Setting::getValue('receipt_template', 'classic');
        $currencySymbol = $this->getCurrencySymbol();

        $borderColor = match ($template) {
            'branded' => '#8b5cf6',
            'minimal' => '#d6d3d1',
            'compact' => '#d6d3d1',
            'detailed' => '#44403c',
            default => '#d6d3d1',
        };
        $headerBg = match ($template) {
            'branded' => '#f5f3ff',
            'minimal' => 'transparent',
            default => 'transparent',
        };

        $itemsHtml = '';
        foreach ($order->items as $item) {
            $name = $item->product_name ?? $item->product->name;
            $modifiers = $item->getModifierSummary();
            $modifierHtml = $modifiers ? '<div style="font-size:11px;color:#78716c;padding-left:8px">'.e($modifiers).'</div>' : '';
            $itemsHtml .= '<tr>
                <td style="padding:4px 0;font-size:13px">'.e($name).' <span style="color:#a8a29e">x'.$item->quantity.'</span></td>
                <td style="padding:4px 0;text-align:right;font-size:13px;white-space:nowrap">'.$currencySymbol.number_format($item->total_price, 2).'</td>
            </tr>'.$modifierHtml;
        }

        $discountLabel = e(__('Discount'));
        $subtotalLabel = e(__('Subtotal'));
        $taxLabel = e(__('Tax'));
        $totalLabel = e(__('Total'));
        $paidLabel = e(__('Paid'));
        $changeLabel = e(__('Change'));
        $notesLabel = e(__('Notes'));
        $orderLabel = e(__('Order #'));
        $dateLabel = e(__('Date'));
        $typeLabel = e(__('Type'));
        $tableLabel = e(__('Table'));
        $cashierLabel = e(__('Cashier'));
        $itemsLabel = e(__('Items'));
        $priceLabel = e(__('PRICE'));
        $telLabel = e(__('Tel:'));
        $thankYou = e(__('Thank you for your visit!'));

        $discountRow = $order->discount > 0 ? '
            <tr>
                <td style="padding:2px 0;font-size:12px;color:#78716c">'.$discountLabel.'</td>
                <td style="padding:2px 0;text-align:right;font-size:12px;color:#78716c">-'.$currencySymbol.number_format($order->discount, 2).'</td>
            </tr>' : '';

        $notesHtml = $order->notes ? '
            <div style="margin-top:8px;padding:8px;background:#f5f5f4;border-radius:6px;font-size:12px;color:#78716c">
                <strong>'.$notesLabel.':</strong> '.e($order->notes).'
            </div>' : '';

        $decorativeBorder = $template === 'branded' ? '·  ·  ·  ·  ·  ·  ·  ·  ·  ·' : '';
        $doubleBorder = ($template === 'detailed' || $template === 'classic') ? '2px dashed' : '1px dashed';

        return '
        <div style="font-family:ui-monospace,monospace;max-width:320px;margin:0 auto;color:#44403c;padding:4px">
            '.($template === 'branded' ? '<div style="text-align:center;color:#8b5cf6;font-size:14px;margin-bottom:4px">'.$decorativeBorder.'</div>' : '').'
            <div style="text-align:center;margin-bottom:12px;'.($headerBg !== 'transparent' ? 'background:'.$headerBg.';padding:12px;border-radius:8px' : '').'">
                <div style="font-size:'.($template === 'branded' ? '20' : '18').'px;font-weight:700;letter-spacing:1px">'.e($shopName).'</div>
                '.($address ? '<div style="font-size:11px;color:#78716c;margin-top:2px">'.e($address).'</div>' : '').'
                '.($phone ? '<div style="font-size:11px;color:#78716c">'.$telLabel.' '.e($phone).'</div>' : '').'
            </div>
            <hr style="border:none;border-top:2px '.$doubleBorder.' '.$borderColor.';margin:8px 0">
            '.($header ? '<div style="text-align:center;font-size:12px;color:#78716c;margin-bottom:8px">'.e($header).'</div><hr style="border:none;border-top:1px dashed '.$borderColor.';margin:8px 0">' : '').'
            <div style="font-size:12px;color:#78716c;margin-bottom:8px">
                <div>'.$orderLabel.': '.e($order->order_number).'</div>
                <div>'.$dateLabel.': '.e($order->created_at->format('d M Y, H:i')).'</div>
                <div>'.$typeLabel.': '.e($order->order_type->label()).($order->table_number ? ' | '.$tableLabel.': '.e($order->table_number) : '').'</div>
                <div>'.$cashierLabel.': '.e($order->user->name).'</div>
            </div>
            <hr style="border:none;border-top:1px dashed '.$borderColor.';margin:8px 0">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                    <tr style="font-size:11px;color:#a8a29e;text-transform:uppercase">
                        <th style="text-align:left;padding:4px 0">'.e(__('Item')).'</th>
                        <th style="text-align:right;padding:4px 0;white-space:nowrap">'.$priceLabel.' ('.$currencySymbol.')</th>
                    </tr>
                </thead>
                <tbody>
                    '.$itemsHtml.'
                </tbody>
            </table>
            <hr style="border:none;border-top:1px dashed '.$borderColor.';margin:8px 0">
            <table style="width:100%;border-collapse:collapse;font-size:13px">
                <tr><td style="padding:2px 0;color:#78716c">'.$subtotalLabel.'</td><td style="padding:2px 0;text-align:right;color:#78716c">'.$currencySymbol.number_format($order->subtotal, 2).'</td></tr>
                '.$discountRow.'
                <tr><td style="padding:2px 0;color:#78716c">'.$taxLabel.'</td><td style="padding:2px 0;text-align:right;color:#78716c">'.$currencySymbol.number_format($order->tax, 2).'</td></tr>
            </table>
            <hr style="border:none;border-top:2px solid '.$borderColor.';margin:8px 0">
            <table style="width:100%;border-collapse:collapse;font-size:15px;font-weight:700">
                <tr><td style="padding:2px 0">'.$totalLabel.'</td><td style="padding:2px 0;text-align:right">'.$currencySymbol.number_format($order->total, 2).'</td></tr>
            </table>
            <hr style="border:none;border-top:2px solid '.$borderColor.';margin:8px 0">
            <div style="font-size:12px;color:#78716c;margin-top:8px">
                <div style="display:flex;justify-content:space-between">
                    <span>'.$paidLabel.'</span><span>'.$currencySymbol.number_format($order->amount_paid, 2).'</span>
                </div>
                <div style="display:flex;justify-content:space-between">
                    <span>'.$changeLabel.'</span><span>'.$currencySymbol.number_format($order->change_amount, 2).'</span>
                </div>
            </div>
            '.$notesHtml.'
            <hr style="border:none;border-top:1px dashed '.$borderColor.';margin:8px 0">
            '.($footer ? '<div style="text-align:center;font-size:12px;color:#78716c;margin-top:8px">'.e($footer).'</div>' : '').'
            <hr style="border:none;border-top:2px '.$doubleBorder.' '.$borderColor.';margin:8px 0">
            '.($template === 'branded' ? '<div style="text-align:center;color:#8b5cf6;font-size:14px;margin-top:4px">'.$decorativeBorder.'</div>' : '').'
            <div style="text-align:center;font-size:10px;color:#a8a29e;margin-top:4px">'.$thankYou.'</div>
        </div>';
    }

    public function print(Order $order): bool
    {
        $content = $this->generateReceiptContent($order);
        $printerType = Setting::getValue('receipt_printer', 'default');

        if ($printerType === 'pdf') {
            $html = $this->generateReceiptHtml($order);
            $this->printPdf($html, $order);

            return true;
        }

        if ($printerType === 'thermal') {
            $ip = Setting::getValue('receipt_printer_ip', '');
            $port = (int) Setting::getValue('receipt_printer_port', 9100);

            if ($ip) {
                $socket = @fsockopen($ip, $port, $errno, $errstr, 5);
                if ($socket) {
                    $encoding = Setting::getValue('receipt_printer_encoding', 'CP437');
                    $formatted = iconv('UTF-8', $encoding.'//TRANSLIT', $content);
                    fwrite($socket, $formatted);
                    fwrite($socket, "\x1B\x6D"); // ESC/POS partial cut
                    fclose($socket);
                }
            }
        }

        return true;
    }

    public function printPdf(string $html, Order $order): void
    {
        $filename = 'receipt-'.$order->order_number.'-'.now()->format('YmdHis').'.pdf';
        $path = storage_path('app/receipts/'.$filename);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $html);
    }

    public function getPaperWidth(): int
    {
        $printerType = Setting::getValue('receipt_printer', 'default');

        return match ($printerType) {
            'thermal' => 32,
            'pdf' => 48,
            default => 40,
        };
    }

    private function alignPair(string $label, string $value, int $width, bool $bold = false): string
    {
        $label = $bold ? strtoupper($label) : $label;
        $padding = $width - strlen($label) - strlen($value) - 1;

        return $label.' '.str_repeat(' ', max(0, $padding)).$value;
    }

    private function wordWrapItem(string $text, int $maxLength): array
    {
        if (mb_strlen($text) <= $maxLength) {
            return [$text];
        }

        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? $currentLine.' '.$word : $word;
            if (mb_strlen($testLine) <= $maxLength) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                } else {
                    $lines[] = mb_substr($word, 0, $maxLength);
                    $word = mb_substr($word, $maxLength);
                }
                $currentLine = $word;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return $lines;
    }

    private function wrapText(string $text, int $width): string
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';

        foreach ($words as $word) {
            $testLine = $currentLine ? $currentLine.' '.$word : $word;
            if (mb_strlen($testLine) <= $width) {
                $currentLine = $testLine;
            } else {
                if ($currentLine) {
                    $lines[] = $currentLine;
                }
                $currentLine = $word;
            }
        }

        if ($currentLine) {
            $lines[] = $currentLine;
        }

        return implode("\n", $lines);
    }
}
