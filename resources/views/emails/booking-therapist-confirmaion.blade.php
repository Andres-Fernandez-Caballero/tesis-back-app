<!DOCTYPE html>

<html lang="es">
<head>
<meta charset="UTF-8">
<title>Nueva solicitud de turno</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background:#f4f6f8; padding:30px; margin:0;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
<td align="center">

<table width="600" cellpadding="0" cellspacing="0" style="background:white;border-radius:8px;padding:30px">

<tr>
<td>

<h2 style="margin-top:0;color:#111;">📅 Nueva solicitud de turno</h2>

<p>
Hola <strong>{{ $therapist->name }}</strong>,
</p>

<p>
Un cliente ha solicitado un turno contigo.  
Revisa los detalles y confirma el turno si estás disponible.
</p>

<hr style="border:none;border-top:1px solid #eee;margin:25px 0">

<h3 style="margin-bottom:15px;">Detalles del turno</h3>

<table width="100%" style="font-size:14px;">
<tr>
<td style="padding:6px 0;"><strong>Cliente:</strong></td>
<td>{{ $client->name }}</td>
</tr>

<tr>
<td style="padding:6px 0;"><strong>Email:</strong></td>
<td>{{ $client->email }}</td>
</tr>

<tr>
<td style="padding:6px 0;"><strong>Servicio:</strong></td>
<td>{{ $appointment->service_name }}</td>
</tr>

<tr>
<td style="padding:6px 0;"><strong>Fecha:</strong></td>
<td>{{ $appointment->date }}</td>
</tr>

<tr>
<td style="padding:6px 0;"><strong>Hora:</strong></td>
<td>{{ $appointment->time }}</td>
</tr>
</table>

<div style="text-align:center;margin:35px 0;">

<a href="{{ $confirmUrl }}"
style="
background:#22c55e;
color:white;
padding:14px 28px;
text-decoration:none;
border-radius:6px;
font-weight:bold;
font-size:15px;
display:inline-block;
">
Confirmar turno </a>

</div>

<p style="font-size:14px;">
Si no puedes aceptar este turno, simplemente ignora este correo o ingresa a tu panel para gestionarlo.
</p>

<hr style="border:none;border-top:1px solid #eee;margin:25px 0">

<p style="font-size:12px;color:#777;text-align:center;">
Este mensaje fue enviado automáticamente por el sistema de gestión de turnos.
</p>

</td>
</tr>

</table>

</td>
</tr>
</table>

</body>
</html>
