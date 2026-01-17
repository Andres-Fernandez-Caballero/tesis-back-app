<!DOCTYPE html>
<html>
<body>
    <h1>Hola {{ $user->name }}</h1>

    <p>
        Tu cuenta como <strong>Cliente</strong> fue creada correctamente.
    </p>

    <p>
        Ya podés ingresar al sistema.
    </p>

    <a href="{{ url('/login') }}">
        Ir al sistema
    </a>
</body>
</html>
