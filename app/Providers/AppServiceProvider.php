<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || env('APP_ENV') === 'production') {
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
        return (new MailMessage)
            ->subject('Verifikasi Email - Sistem Inventaris Palawi')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Terima kasih sudah mendaftar. Klik tombol di bawah untuk memverifikasi email kamu.')
            ->action('Verifikasi Email', $url)
            ->line('Jika kamu tidak merasa mendaftar, abaikan email ini.')
            ->salutation('Salam, Tim Palawi');
        });
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
}