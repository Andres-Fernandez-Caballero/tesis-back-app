<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualización sobre tu solicitud — BodyFix</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.08); }
        .header { background: #6b7280; padding: 36px 40px; text-align: center; }
        .header h1 { color: #ffffff; margin: 0; font-size: 26px; font-weight: 700; }
        .header p { color: rgba(255,255,255,0.85); margin: 8px 0 0; font-size: 15px; }
        .body { padding: 36px 40px; }
        .body p { color: #444; line-height: 1.7; font-size: 15px; margin: 0 0 16px; }
        .highlight { background: #fef2f2; border-left: 4px solid #dc2626; padding: 16px 20px; border-radius: 0 8px 8px 0; margin: 24px 0; }
        .highlight strong { color: #dc2626; }
        .footer { background: #f9f9f9; padding: 24px 40px; text-align: center; border-top: 1px solid #eee; }
        .footer p { color: #999; font-size: 13px; margin: 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Solicitud no aprobada</h1>
            <p>Actualización sobre tu registro en BodyFix</p>
        </div>
        <div class="body">
            <p>Hola, <strong>{{ $registration->nombre ?? $registration->nombre_local }}</strong>:</p>

            <p>
                Luego de revisar tu solicitud de alta, el equipo de BodyFix ha tomado la
                decisión de <strong>no aprobar</strong> el registro de tu local en esta ocasión.
            </p>

            <div class="highlight">
                <strong>Local:</strong> {{ $registration->nombre_local }}<br>
                <strong>Dirección:</strong> {{ $registration->direccion }}
            </div>

            <p>
                Si tenés alguna consulta o creés que hubo un error, podés contactarnos
                respondiendo este correo o escribiéndonos a través de nuestro sitio web.
            </p>

            <p>Gracias por tu interés en BodyFix.</p>
        </div>
        <div class="footer">
            <p>BodyFix &mdash; Reservá tu masaje en un solo clic</p>
            <p>Este es un mensaje automático.</p>
        </div>
    </div>
</body>
</html>
