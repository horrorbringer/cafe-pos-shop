<?php

namespace App\Providers;

use App\Domain\Notifications\Events\DailySalesClosed;
use App\Domain\Notifications\Events\LowStockDetected;
use App\Domain\Notifications\Events\OrderPaid;
use App\Domain\Notifications\Events\PaymentFailed;
use App\Domain\Notifications\Events\RefundVoided;
use App\Domain\Notifications\Listeners\SendDailySalesNotification;
use App\Domain\Notifications\Listeners\SendLowStockNotification;
use App\Domain\Notifications\Listeners\SendOrderPaidNotification;
use App\Domain\Notifications\Listeners\SendPaymentFailedNotification;
use App\Domain\Notifications\Listeners\SendRefundVoidNotification;
use App\Domain\Payment\PaymentManager;
use App\Domain\Payment\Providers\BakongProvider;
use App\Domain\Payment\Providers\CashProvider;
use App\Domain\Payment\Providers\ThirdPartyKhqrProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentManager::class, function ($app) {
            $manager = new PaymentManager;

            // Register available providers
            $manager->register('cash', new CashProvider);
            $manager->register('bakong', new BakongProvider);
            $manager->register('third_party_khqr', new ThirdPartyKhqrProvider);

            return $manager;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(OrderPaid::class, SendOrderPaidNotification::class);
        Event::listen(PaymentFailed::class, SendPaymentFailedNotification::class);
        Event::listen(LowStockDetected::class, SendLowStockNotification::class);
        Event::listen(RefundVoided::class, SendRefundVoidNotification::class);
        Event::listen(DailySalesClosed::class, SendDailySalesNotification::class);
    }
}
