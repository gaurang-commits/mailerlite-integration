<?php

namespace App\Providers;

use App\Http\Services\MailerLiteService;
use App\Transformers\SubscriberTransformer;
use Illuminate\Support\ServiceProvider;

class MailerLiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('MailerLite',function(){
            return new MailerLiteService(new SubscriberTransformer);
        });
       
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
