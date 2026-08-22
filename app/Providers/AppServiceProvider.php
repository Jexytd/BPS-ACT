<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $base64 = env('FIREBASE_CREDENTIALS_BASE64');

        if (!$base64) {
            return;
        }

        $decoded = base64_decode($base64, true);

        if ($decoded === false) {
            throw new \RuntimeException(
                'FIREBASE_CREDENTIALS_BASE64 tidak valid.'
            );
        }

        $json = json_decode($decoded, true);

        if (!is_array($json)) {
            throw new \RuntimeException(
                'Firebase Service Account JSON tidak valid.'
            );
        }

        if (
            empty($json['project_id']) ||
            empty($json['private_key']) ||
            empty($json['client_email'])
        ) {
            throw new \RuntimeException(
                'Firebase Service Account tidak lengkap.'
            );
        }

        if ($json['project_id'] !== 'bpsact-e8fc3') {
            throw new \RuntimeException(
                'Firebase project ID tidak sesuai. Ditemukan: ' .
                $json['project_id']
            );
        }

        $path = storage_path(
            'app/firebase-service-account.json'
        );

        File::ensureDirectoryExists(dirname($path));

        File::put($path, $decoded);

        config([
            'firebase.projects.app.credentials' => $path,
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production' || isset($_SERVER['VERCEL']) || isset($_ENV['VERCEL'])) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
