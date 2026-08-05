<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SlackAccountNotifier
{
    public function notifyNewAccount(string $type, string $email, string $password): void
    {
        $webhookUrl = config('services.slack.accounts_webhook_url');

        if (! $webhookUrl) {
            return;
        }

        try {
            Http::post($webhookUrl, [
                'text' => "🔑 *Nueva cuenta creada — {$type}*\n*Email:* {$email}\n*Contraseña:* {$password}",
            ]);
        } catch (\Throwable $e) {
            // Nunca debe romper el flujo de creación de la cuenta por un fallo al notificar a Slack.
            Log::warning('No se pudo notificar la nueva cuenta a Slack', [
                'error' => $e->getMessage(),
                'type' => $type,
                'email' => $email,
            ]);
        }
    }
}
