<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones — BodyFix</title>
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" type="image">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        .legal-hero {
            background: linear-gradient(135deg, #ff8c00 0%, #e67c00 100%);
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
        .legal-toc a:hover { color: #ff8c00; }

        /* Contenido */
        .legal-content h2 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a1a1a;
            margin-bottom: 32px;
            padding-bottom: 16px;
            border-bottom: 2px solid #ff8c00;
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
            background: #ff8c00;
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
            background: #fff8f0;
            border: 1px solid #ffe0b2;
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
            background: #ff8c00;
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
            color: #ff8c00;
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
            <div class="legal-badge">📄 Legal</div>
            <h1>Términos y Condiciones</h1>
            <p>Estas condiciones regulan el uso de la plataforma BodyFix. Te recomendamos leerlas antes de usar nuestros servicios.</p>
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
                    <a href="#sec-1">1. Aceptación</a>
                    <a href="#sec-2">2. Descripción del servicio</a>
                    <a href="#sec-3">3. Reservas y cancelaciones</a>
                    <a href="#sec-4">4. Pagos</a>
                    <a href="#sec-5">5. Responsabilidades</a>
                    <a href="#sec-6">6. Propiedad intelectual</a>
                    <a href="#sec-7">7. Modificaciones</a>
                    <a href="#sec-8">8. Ley aplicable</a>
                </aside>

                <!-- Contenido -->
                <div class="legal-content">
                    <h2>Términos y Condiciones de uso</h2>

                    <div class="legal-section" id="sec-1">
                        <h3><span class="section-num">1</span> Aceptación de los términos</h3>
                        <p>Al utilizar la aplicación o la plataforma web BodyFix, aceptás estos Términos y Condiciones en su totalidad. Si no estás de acuerdo con alguna de las condiciones aquí establecidas, te pedimos que no utilices el servicio.</p>
                    </div>

                    <div class="legal-section" id="sec-2">
                        <h3><span class="section-num">2</span> Descripción del servicio</h3>
                        <p>BodyFix es una plataforma que conecta a clientes con locales de masajes y masajistas profesionales, permitiendo la reserva de turnos de manera digital. BodyFix actúa como intermediario y no es responsable por la calidad del servicio prestado por los profesionales adheridos.</p>
                    </div>

                    <div class="legal-section" id="sec-3">
                        <h3><span class="section-num">3</span> Reservas y cancelaciones</h3>
                        <p>Las reservas se confirman una vez abonada la seña correspondiente. Las cancelaciones deben realizarse con al menos 24 horas de anticipación para evitar penalidades. La devolución de la seña en caso de cancelación queda sujeta a la política del local seleccionado.</p>
                    </div>

                    <div class="legal-section" id="sec-4">
                        <h3><span class="section-num">4</span> Pagos</h3>
                        <p>Los pagos se procesan a través de pasarelas de pago seguras. BodyFix no almacena datos de tarjetas de crédito ni débito. El precio final del servicio es el indicado al momento de la reserva y puede incluir una seña previa.</p>
                    </div>

                    <div class="legal-section" id="sec-5">
                        <h3><span class="section-num">5</span> Responsabilidades del usuario</h3>
                        <p>El usuario se compromete a proporcionar información veraz y actualizada, a no realizar reservas fraudulentas, y a tratar con respeto al personal de los locales. El incumplimiento de estas condiciones puede resultar en la suspensión de la cuenta.</p>
                    </div>

                    <div class="legal-section" id="sec-6">
                        <h3><span class="section-num">6</span> Propiedad intelectual</h3>
                        <p>Todos los contenidos de la plataforma BodyFix, incluyendo textos, imágenes, logotipos y código, son propiedad de BodyFix o de sus licenciantes. Queda prohibida su reproducción total o parcial sin autorización expresa.</p>
                    </div>

                    <div class="legal-section" id="sec-7">
                        <h3><span class="section-num">7</span> Modificaciones</h3>
                        <p>BodyFix se reserva el derecho de modificar estos Términos en cualquier momento. Los cambios serán notificados a los usuarios a través de la aplicación o la plataforma web. El uso continuado del servicio implica la aceptación de las modificaciones.</p>
                    </div>

                    <div class="legal-section" id="sec-8">
                        <h3><span class="section-num">8</span> Ley aplicable</h3>
                        <p>Estos Términos y Condiciones se rigen por la legislación de la República Argentina. Cualquier disputa será sometida a la jurisdicción de los tribunales ordinarios de la Ciudad Autónoma de Buenos Aires.</p>
                    </div>

                    <div class="legal-contact-box">
                        <div class="contact-icon">✉️</div>
                        <div>
                            <h4>¿Tenés dudas sobre estos términos?</h4>
                            <p>Contactanos en <a href="mailto:legal@bodyfix.com.ar">legal@bodyfix.com.ar</a> y te respondemos a la brevedad.</p>
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
