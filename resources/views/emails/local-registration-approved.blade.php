<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Tu local fue aprobado! — BodyFix</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #e87c2b; padding: 36px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 26px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 15px; }
        .body { padding: 36px 40px; }
        .body p { color: #444; line-height: 1.7; font-size: 15px; margin: 0 0 16px; }
        .highlight { background: #fff8f2; border-left: 4px solid #e87c2b; padding: 16px 20px; border-radius: 0 8px 8px 0; margin: 24px 0; }
        .highlight strong { color: #e87c2b; }
        .credentials { background: #1a1a1a; color: #fff; border-radius: 10px; padding: 22px 28px; margin: 24px 0; }
        .credentials p { color: #ccc; font-size: 13px; margin: 0 0 14px; }
        .credentials .field { display: flex; align-items: center; margin: 8px 0; }
        .credentials .label { color: #aaa; font-size: 13px; width: 110px; flex-shrink: 0; }
        .credentials .value { color: #ffffff; font-size: 15px; font-family: monospace; font-weight: 600; }
        .credentials .warning { color: #f87c2b; font-size: 12px; margin-top: 14px; }
        .btn { display: inline-block; background: #e87c2b; color: #fff; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 15px; margin: 16px 0; }
        .footer { background: #f9f9f9; padding: 24px 40px; text-align: center; border-top: 1px solid #eee; }
        .footer p { color: #999; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 ¡Felicitaciones!</h1>
            <p>Tu local fue aprobado en BodyFix</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $registration->nombre ?? $registration->nombre_local }}</strong>:</p>

            <p>
                Nos complace informarte que la solicitud de alta para tu local ha sido
                <strong>revisada y aprobada</strong> por el equipo de BodyFix.
            </p>

            <div class="highlight">
                <strong>Local:</strong> {{ $registration->nombre_local }}<br>
                <strong>Dirección:</strong> {{ $registration->direccion }}<br>
                @if($registration->localidad)
                <strong>Localidad:</strong> {{ $registration->localidad }}<br>
                @endif
            </div>

            <p>Ya podés acceder al portal administrativo con las siguientes credenciales:</p>

            <div class="credentials">
                <p>Credenciales de acceso al portal</p>
                <div class="field">
                    <span class="label">Usuario</span>
                    <span class="value">{{ $registration->email }}</span>
                </div>
                <div class="field">
                    <span class="label">Contraseña</span>
                    <span class="value">{{ $password }}</span>
                </div>
                <p class="warning">⚠ Por seguridad, cambiá tu contraseña después del primer ingreso.</p>
            </div>

            <a href="{{ url('/app') }}" class="btn">Acceder al portal →</a>

            <p style="margin-top: 24px;">
                Desde el portal podrás administrar tus masajistas, configurar la disponibilidad
                horaria y gestionar los datos de tu local.
            </p>

            <p>¡Bienvenido/a a la familia BodyFix!</p>
        </div>
        <div class="footer">
            <p>BodyFix &mdash; Reservá tu masaje en un solo clic</p>
            <p>Este es un mensaje automático, por favor no respondas este correo.</p>
        </div>
    </div>
</body>
</html>
