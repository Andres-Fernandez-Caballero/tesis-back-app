<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad — BodyFix</title>
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" type="image">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .legal-hero {
            background: linear-gradient(135deg, #2d6a4f 0%, #1b4332 100%);
            padding: 120px 0 60px;
            text-align: center;
            color: #fff;
        }
        .legal-hero .legal-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.35);
            border-radius: 50px;
            padding: 6px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 0.3px;
            margin-bottom: 20px;
        }
        .legal-hero h1 {
            font-size: clamp(2rem, 4vw, 2.8rem);
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 14px;
        }
        .legal-hero p {
            font-size: 1rem;
            color: rgba(255,255,255,0.85);
            max-width: 520px;
            margin: 0 auto 12px;
        }
        .legal-hero .legal-meta {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.65);
            font-style: italic;
        }

        .legal-body {
            padding: 64px 0 80px;
            background: #fff;
        }
        .legal-layout {
            display: grid;
            grid-template-columns: 220px 1fr;
            gap: 48px;
            align-items: start;
        }
        @media (max-width: 768px) {
            .legal-layout { grid-template-columns: 1fr; }
            .legal-toc { display: none; }
        }

        /* Índice lateral */
        .legal-toc {
            position: sticky;
            top: 88px;
            background: #fafafa;
            border: 1px solid #e8e0d8;
            border-radius: 16px;
            padding: 20px;
        }
        .legal-toc h3 {
            font-size: 0.78rem;
            font-weight: 700;
            color: #999;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }
        .legal-toc a {
            display: block;
            font-size: 0.88rem;
            color: #555;
            text-decoration: none;
            padding: 6px 0;
            border-bottom: 1px solid #eee;
            transition: color 0.2s;
            line-height: 1.4;
        }
        .legal-toc a:last-child { border-bottom: none; }
        .legal-toc a:hover { color: #2d6a4f; }

        /* Contenido */
        .legal-content h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 2px solid #2d6a4f;
            display: inline-block;
        }
        .legal-section {
            margin-bottom: 36px;
            scroll-margin-top: 100px;
        }
        .legal-section h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .legal-section h3 .section-num {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #2d6a4f;
            color: #fff;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .legal-section p {
            color: #555;
            line-height: 1.75;
            font-size: 0.97rem;
        }

        /* Contacto */
        .legal-contact-box {
            margin-top: 48px;
            background: #f0faf5;
            border: 1px solid #b7dfc9;
            border-radius: 16px;
            padding: 28px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .legal-contact-box .contact-icon {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #2d6a4f;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .legal-contact-box h4 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 4px;
        }
        .legal-contact-box p {
            font-size: 0.9rem;
            color: #666;
        }
        .legal-contact-box a {
            color: #2d6a4f;
            font-weight: 600;
            text-decoration: none;
        }
        .legal-contact-box a:hover { text-decoration: underline; }
    </style>
</head>

<body>

    <!-- Header (mismo que la landing) -->
    <header class="header" id="header">
        <div class="container">
            <div class="logo">
                <a href="{{ route('home') }}" aria-label="Ir al inicio"><h2>BodyFix</h2></a>
            </div>
            <nav class="nav" id="nav">
                <a href="{{ route('home') }}#nosotros">Nosotros</a>
                <a href="{{ route('home') }}#como-funciona">Cómo funciona</a>
                <a href="{{ route('home') }}#testimonios">Testimonios</a>
                <a href="{{ route('home') }}#contacto">Contacto</a>
                @guest
                    <a href="{{ route('login') }}" class="btn-primary">Portal Administrativo</a>
                @else
                    <a href="{{ Auth::User()->canAccessAdminPanel()
                        ? route('filament.admin.pages.dashboard')
                        : route('filament.app.pages.dashboard') }}" class="btn-primary">Dashboard</a>
                @endguest
            </nav>
            <button class="mobile-menu-btn" id="mobile-menu" aria-label="Menú">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <!-- Hero legal -->
    <div class="legal-hero">
        <div class="container">
            <div class="legal-badge">🔒 Privacidad</div>
            <h1>Política de Privacidad</h1>
            <p>Describimos qué datos recopilamos, cómo los usamos y cómo protegemos tu información personal.</p>
            <p class="legal-meta">Última actualización: enero 2025</p>
        </div>
    </div>

    <!-- Cuerpo -->
    <section class="legal-body">
        <div class="container">
            <div class="legal-layout">

                <!-- Índice -->
                <aside class="legal-toc">
                    <h3>Contenido</h3>
                    <a href="#sec-1">1. Qué recopilamos</a>
                    <a href="#sec-2">2. Cómo usamos los datos</a>
                    <a href="#sec-3">3. Compartición</a>
                    <a href="#sec-4">4. Almacenamiento y seguridad</a>
                    <a href="#sec-5">5. Retención de datos</a>
                    <a href="#sec-6">6. Tus derechos</a>
                    <a href="#sec-7">7. Cookies</a>
                    <a href="#sec-8">8. Cambios en la política</a>
                </aside>

                <!-- Contenido -->
                <div class="legal-content">
                    <h2>Política de Privacidad</h2>

                    <div class="legal-section" id="sec-1">
                        <h3><span class="section-num">1</span> ¿Qué información recopilamos?</h3>
                        <p>Recopilamos la información que nos proporcionás al registrarte: nombre, dirección de correo electrónico y número de teléfono. También recopilamos datos de uso de la plataforma, como las reservas realizadas, preferencias y calificaciones.</p>
                    </div>

                    <div class="legal-section" id="sec-2">
                        <h3><span class="section-num">2</span> ¿Cómo usamos tu información?</h3>
                        <p>Utilizamos tus datos para gestionar tu cuenta, procesar reservas, enviarte notificaciones sobre tus turnos y mejorar la experiencia de la plataforma. No utilizamos tus datos para publicidad de terceros.</p>
                    </div>

                    <div class="legal-section" id="sec-3">
                        <h3><span class="section-num">3</span> Compartición de datos</h3>
                        <p>Compartimos únicamente la información necesaria con los locales y masajistas que hayas reservado (nombre y datos de contacto). No vendemos ni cedemos tu información personal a terceros con fines comerciales.</p>
                    </div>

                    <div class="legal-section" id="sec-4">
                        <h3><span class="section-num">4</span> Almacenamiento y seguridad</h3>
                        <p>Tus datos se almacenan en servidores seguros. Utilizamos cifrado SSL/TLS para todas las comunicaciones. Las contraseñas se almacenan de forma hasheada y nunca en texto plano. Revisamos periódicamente nuestras medidas de seguridad.</p>
                    </div>

                    <div class="legal-section" id="sec-5">
                        <h3><span class="section-num">5</span> Retención de datos</h3>
                        <p>Conservamos tus datos mientras tu cuenta esté activa. Si eliminás tu cuenta, tus datos personales serán eliminados en un plazo de 30 días, salvo aquellos que debamos conservar por obligaciones legales.</p>
                    </div>

                    <div class="legal-section" id="sec-6">
                        <h3><span class="section-num">6</span> Tus derechos</h3>
                        <p>Tenés derecho a acceder, rectificar o eliminar tus datos personales en cualquier momento. Para ejercer estos derechos, podés hacerlo desde la sección "Editar perfil" de la app o enviando un correo a <a href="mailto:privacidad@bodyfix.com.ar" style="color:#2d6a4f;font-weight:600;">privacidad@bodyfix.com.ar</a>.</p>
                    </div>

                    <div class="legal-section" id="sec-7">
                        <h3><span class="section-num">7</span> Cookies y tecnologías similares</h3>
                        <p>La plataforma web utiliza cookies de sesión necesarias para el funcionamiento del portal administrativo. La aplicación móvil utiliza almacenamiento local seguro (SecureStore) para mantener tu sesión. No utilizamos cookies de seguimiento de terceros.</p>
                    </div>

                    <div class="legal-section" id="sec-8">
                        <h3><span class="section-num">8</span> Cambios en la Política de Privacidad</h3>
                        <p>Podemos actualizar esta política periódicamente. Te notificaremos sobre cambios significativos a través de la aplicación. El uso continuado del servicio implica la aceptación de la política actualizada.</p>
                    </div>

                    <div class="legal-contact-box">
                        <div class="contact-icon">🔒</div>
                        <div>
                            <h4>¿Querés ejercer tus derechos o tenés consultas sobre privacidad?</h4>
                            <p>Contactanos en <a href="mailto:privacidad@bodyfix.com.ar">privacidad@bodyfix.com.ar</a> y te respondemos a la brevedad.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer (mismo que la landing) -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>BodyFix</h3>
                    <p>Tu lugar de bienestar, siempre cerca.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Instagram">📷</a>
                        <a href="#" aria-label="LinkedIn">💼</a>
                        <a href="#" aria-label="Email">✉️</a>
                        <a href="#" aria-label="Facebook">📘</a>
                    </div>
                </div>
                <div class="footer-info">
                    <div class="footer-column">
                        <h4>Contacto</h4>
                        <p>844-328-7793</p>
                        <p>contacto@bodyfix.com</p>
                        <p>www.bodyfix.com</p>
                    </div>
                    <div class="footer-column">
                        <h4>Legal</h4>
                        <a href="{{ route('terms') }}">Términos y Condiciones</a>
                        <a href="{{ route('privacy') }}">Política de Privacidad</a>
                    </div>
                    <div class="footer-column">
                        <h4>Plataforma</h4>
                        <a href="{{ route('login') }}">Iniciar sesión</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} BodyFix. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        const mobileMenuBtn = document.getElementById('mobile-menu');
        const nav = document.getElementById('nav');
        mobileMenuBtn.addEventListener('click', () => {
            nav.classList.toggle('open');
            mobileMenuBtn.classList.toggle('open');
        });
        window.addEventListener('scroll', () => {
            document.getElementById('header').classList.toggle('scrolled', window.scrollY > 50);
        });
    </script>

</body>
</html>
