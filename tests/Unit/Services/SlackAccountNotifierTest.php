<?php

namespace Tests\Unit\Services;

use App\Services\SlackAccountNotifier;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SlackAccountNotifierTest extends TestCase
{
    public function test_notifica_la_nueva_cuenta_a_slack()
    {
        config(['services.slack.accounts_webhook_url' => 'https://hooks.slack.com/services/test']);

        Http::fake();

        (new SlackAccountNotifier())->notifyNewAccount('cliente', 'nuevo@example.com', 'password-plano-123');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://hooks.slack.com/services/test'
                && str_contains($request['text'], 'cliente')
                && str_contains($request['text'], 'nuevo@example.com')
                && str_contains($request['text'], 'password-plano-123');
        });
    }

    public function test_sin_webhook_configurado_no_envia_nada()
    {
        config(['services.slack.accounts_webhook_url' => null]);

        Http::fake();

        (new SlackAccountNotifier())->notifyNewAccount('cliente', 'nuevo@example.com', 'password-plano-123');

        Http::assertNothingSent();
    }
}
