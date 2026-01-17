<!DOCTYPE html>
<html>
<body>
    <h1>Hola {{ $user->name }}</h1>

    <p>
        Tu cuenta como <strong>terapeuta</strong> fue creada correctamente.
    </p>

    <p>
        Ya podés ingresar al sistema y comenzar a trabajar.
    </p>

    <a href="{{ url('/login') }}">
        Ir al sistema
    </a>
</body>
</html>
