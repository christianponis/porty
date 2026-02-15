<?php

namespace App\Providers;

use App\Services\Payment\MockPaymentProvider;
use App\Services\Payment\PaymentProviderInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentProviderInterface::class, function () {
            return match (config('porty.payment.default_provider')) {
                // 'stripe' => new StripePaymentProvider(),
                // 'paypal' => new PaypalPaymentProvider(),
                default => new MockPaymentProvider(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
