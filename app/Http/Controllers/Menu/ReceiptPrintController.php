<?php

namespace App\Http\Controllers\Menu;

use App\Domain\Ordering\Models\Order;
use App\Domain\Shared\Enums\OrderStatus;
use App\Services\ReceiptPrinterService;
use Symfony\Component\HttpFoundation\Response;

class ReceiptPrintController
{
    public function show(Order $order): Response
    {
        if (! in_array($order->status, [OrderStatus::Paid, OrderStatus::Completed, OrderStatus::Refunded])) {
            abort(404, 'Receipt not available for this order.');
        }

        $receiptService = app(ReceiptPrinterService::class);

        return response(
            $receiptService->generateReceiptHtml($order)
        )->header('Content-Type', 'text/html');
    }

    public function printPdf(Order $order): Response
    {
        if (! in_array($order->status, [OrderStatus::Paid, OrderStatus::Completed, OrderStatus::Refunded])) {
            abort(404, 'Receipt not available for this order.');
        }

        $receiptService = app(ReceiptPrinterService::class);
        $html = $receiptService->generateReceiptHtml($order);
        $filename = 'receipt-'.$order->order_number.'-'.now()->format('YmdHis').'.pdf';

        return response()->streamDownload(function () use ($html) {
            echo $html;
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
