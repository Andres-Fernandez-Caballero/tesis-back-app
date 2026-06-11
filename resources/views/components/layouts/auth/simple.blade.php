<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        {{--
            Forzar light mode en la página de auth.
            - @fluxAppearance (en partials.head) puede agregar 'dark' al <html>.
            - flux.min.js captura window.Flux.applyAppearance en su closure al inicializar
              (dentro del listener "alpine:init"), DESPUÉS de que este script corre.
            - Sobreescribiendo applyAppearance aquí, Alpine.effect siempre llama a nuestra
              versión → nunca re-agrega 'dark'. Sin MutationObserver, sin loop infinito.
        --}}
        <script>
            document.documentElement.classList.remove('dark');
            if (window.Flux) {
                window.Flux.applyAppearance = function () {
                    document.documentElement.classList.remove('dark');
                };
            }
        </script>
    </head>
    <body class="min-h-screen antialiased" style="background-color: #18181b;">
        <div style="background-color: #18181b;" class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-sm flex-col gap-6">
                <a href="{{ route('home') }}" class="flex flex-col items-center">
                    <span style="color: #ff8c00; font-size: 2rem; font-weight: 800; letter-spacing: -0.5px; font-family: inherit;">BodyFix</span>
                </a>
                <div class="rounded-2xl bg-white shadow-xl px-8 py-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
