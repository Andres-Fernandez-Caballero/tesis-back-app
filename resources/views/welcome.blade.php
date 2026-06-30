<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BodyFix - Encontrá tu local de masajes</title>
    <link rel="shortcut icon" href="{{ asset('icon.png') }}" type="image">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>
    <!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <div class="logo">
                <a href="#" id="logo-link" aria-label="Ir al inicio"><h2>BodyFix</h2></a>
            </div>
            <nav class="nav" id="nav">
                <a href="#nosotros">Nosotros</a>
                <a href="#como-funciona">Cómo funciona</a>
                <a href="#testimonios">Testimonios</a>
                <a href="#contacto">Contacto</a>
                <button class="btn-outline" onclick="return false;">Descargar App</button>
                <button class="btn-outline" id="btn-registrar-local">Registrar Local</button>
                @guest
                    <a href="{{ route('login') }}" class="btn-primary">Portal Administrativo</a>
                @else
                    <a href="{{ Auth::User()->canAccessAdminPanel()
                        ? route('filament.admin.pages.dashboard')
                        : route('filament.app.pages.dashboard') }}" class="btn-primary">Dashboard</a>
                @endguest
            </nav>
            <button class="mobile-menu-btn" id="mobile-menu" aria-label="Menú">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <span class="hero-badge">Locales de masajes en tu barrio</span>
                    <h1>Tu camino al bienestar comienza aquí</h1>
                    <p>Encontrá los mejores locales de masajes en Capital Federal. Elegí el masajista, reservá tu turno y asistí al local que más te convenga.</p>
                    <div class="hero-actions">
                        <a href="#" class="btn-primary btn-lg" onclick="return false;">Descargar App</a>
                        <a href="#como-funciona" class="btn-outline btn-lg">Cómo funciona</a>
                    </div>
                </div>
                <div class="hero-image">
                    <div class="hero-image-wrapper">
                        <img src="{{ asset('assets/home-banner-image.png') }}" alt="Sesión de masajes terapéuticos">
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-bg-shape"></div>
        <div class="hero-bg-shape-2"></div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-number">200+</span>
                    <span class="stat-label">Locales adheridos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">10k+</span>
                    <span class="stat-label">Clientes satisfechos</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50k+</span>
                    <span class="stat-label">Sesiones realizadas</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">4.9★</span>
                    <span class="stat-label">Calificación promedio</span>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="nosotros">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="{{ asset('assets/about-background-image.png') }}" alt="Terapia de masajes">
                    <div class="about-badge">
                        <span class="about-badge-icon">✓</span>
                        <span>Locales verificados</span>
                    </div>
                </div>
                <div class="about-text">
                    <span class="section-label">Nosotros</span>
                    <h2>Conocenos</h2>
                    <p>Somos una plataforma que conecta a clientes con los mejores locales de masajes de Capital Federal. Nuestro objetivo es que encontrés el lugar ideal para tu bienestar, cerca de donde estás.</p>
                    <p>Locales seleccionados con masajistas profesionales, para que tu visita sea siempre una experiencia de calidad.</p>
                    <div class="about-features">
                        <div class="feature-item">
                            <span class="feature-icon">🎯</span>
                            <span>Sesiones personalizadas</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🏆</span>
                            <span>Locales verificados</span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">📅</span>
                            <span>Reservas flexibles</span>
                        </div>
                    </div>
                    <div class="about-actions">
                        <a href="#como-funciona" class="btn-primary">Ver cómo funciona</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="about-bg-shape"></div>
    </section>

    <!-- How it Works Section -->
    <section class="how-it-works" id="como-funciona">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Pasos</span>
                <h2>Cómo funciona</h2>
                <p>Con BodyFix encontrás el local de masajes ideal en tu barrio de Capital Federal. Elegí el lugar, el masajista disponible y reservá tu turno en minutos.</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">01</div>
                    <div class="step-icon">
                        <svg viewBox="0 0 100 100" fill="currentColor">
                            <circle cx="50" cy="30" r="15" />
                            <path d="M20 50 Q30 40 50 50 Q70 40 80 50 L75 80 Q50 90 25 80 Z" />
                        </svg>
                    </div>
                    <h3>Explorá locales en tu barrio</h3>
                    <p>Buscá locales de masajes en tu barrio de CABA. Filtrá por ubicación y tipo de servicio para encontrar el que más te convenga.</p>
                </div>
                <div class="step">
                    <div class="step-number">02</div>
                    <div class="step-icon">
                        <svg viewBox="0 0 100 100" fill="currentColor">
                            <rect x="15" y="15" width="70" height="70" rx="8" fill="none" stroke="currentColor" stroke-width="6"/>
                            <line x1="15" y1="35" x2="85" y2="35" stroke="currentColor" stroke-width="6"/>
                            <line x1="35" y1="15" x2="35" y2="30" stroke="currentColor" stroke-width="6"/>
                            <line x1="65" y1="15" x2="65" y2="30" stroke="currentColor" stroke-width="6"/>
                            <rect x="28" y="48" width="12" height="12" rx="2"/>
                            <rect x="46" y="48" width="12" height="12" rx="2"/>
                            <rect x="28" y="65" width="12" height="12" rx="2"/>
                        </svg>
                    </div>
                    <h3>Reservá tu turno</h3>
                    <p>Elegí el masajista disponible en el local y seleccioná el día y horario para asistir. Podés agendar una sesión única o recurrente.</p>
                </div>
                <div class="step">
                    <div class="step-number">03</div>
                    <div class="step-icon">
                        <svg viewBox="0 0 100 100" fill="currentColor">
                            <path d="M50 10 C30 10 15 25 15 45 C15 65 35 80 50 90 C65 80 85 65 85 45 C85 25 70 10 50 10 Z"/>
                            <path d="M35 45 L45 55 L65 35" fill="none" stroke="white" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <h3>Asistí al local</h3>
                    <p>Presentate en el local en el horario reservado y disfrutá de tu sesión con un masajista profesional en un espacio dedicado a tu bienestar.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Para dueños de locales -->
    <section class="para-locales" id="para-locales">
        <div class="container">
            <div class="para-locales-header">
                <span class="section-label section-label-light">Para dueños de locales</span>
                <h2>Hacé crecer tu negocio con BodyFix</h2>
                <p>Miles de clientes buscan locales de masajes en CABA todos los días. Registrá el tuyo y empezá a recibir reservas online.</p>
            </div>
            <div class="beneficios-grid">
                <div class="beneficio-card">
                    <div class="beneficio-icon">📍</div>
                    <h3>Mayor alcance de clientes</h3>
                    <p>Tu local aparece en los resultados de búsqueda de miles de usuarios que buscan servicios de masajes en Capital Federal, filtrando por barrio y tipo de servicio.</p>
                </div>
                <div class="beneficio-card">
                    <div class="beneficio-icon">📈</div>
                    <h3>Mejorá tu rentabilidad</h3>
                    <p>Optimizá la ocupación de tu agenda con reservas online. Reducí los tiempos muertos y maximizá el aprovechamiento de cada turno disponible.</p>
                </div>
                <div class="beneficio-card">
                    <div class="beneficio-icon">💼</div>
                    <h3>Incrementá tus ventas</h3>
                    <p>Más visibilidad significa más clientes nuevos. Mostrá tus servicios, precios y disponibilidad antes de la reserva y aumentá tu tasa de conversión.</p>
                </div>
            </div>
            <div class="para-locales-cta">
                <button class="btn-primary btn-lg" onclick="openModal()">Registrar mi local</button>
                <p>Sin costo inicial · Alta en minutos</p>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonios">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Testimonios</span>
                <h2>Lo que dicen nuestros usuarios</h2>
                <p>Miles de personas confían en BodyFix para mejorar su bienestar. Conocé sus experiencias.</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <img src="{{ asset('assets/john-doe-image.png') }}" alt="Usuario BodyFix">
                        </div>
                        <div>
                            <h4>María González</h4>
                            <div class="stars">★★★★★</div>
                        </div>
                    </div>
                    <p>"Encontrar el local ideal fue muy fácil. Reservé mi turno en minutos y la atención fue excelente. ¡Totalmente recomendable!"</p>
                </div>
                <div class="testimonial-card testimonial-featured">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <img src="{{ asset('assets/john-doe-image.png') }}" alt="Usuario BodyFix">
                        </div>
                        <div>
                            <h4>Carlos Ramírez</h4>
                            <div class="stars">★★★★★</div>
                        </div>
                    </div>
                    <p>"Excelente plataforma. Encontré un local cerca de mi casa con masajistas muy profesionales. La aplicación es súper intuitiva y la reserva muy sencilla."</p>
                </div>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="testimonial-avatar">
                            <img src="{{ asset('assets/john-doe-image.png') }}" alt="Usuario BodyFix">
                        </div>
                        <div>
                            <h4>Laura Fernández</h4>
                            <div class="stars">★★★★★</div>
                        </div>
                    </div>
                    <p>"Increíble experiencia. El local estaba impecable y el masajista fue muy profesional. La sesión fue perfecta. Definitivamente vuelvo a usar BodyFix."</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contacto">
        <div class="container">
            <div class="contact-content">
                <span class="section-label">Contacto</span>
                <h2>¿Tenés alguna pregunta?<br>Estamos para ayudarte</h2>
                <p>Dejanos tu email y nos ponemos en contacto a la brevedad.</p>
                <div class="contact-form">
                    <input type="email" placeholder="tuemail@gmail.com" id="contact-email">
                    <button class="btn-primary" onclick="handleContactSubmit()">Enviar</button>
                </div>
                <p class="contact-success hidden" id="contact-success">¡Gracias! Nos pondremos en contacto pronto.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
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
                        <button class="footer-btn-link" onclick="openModal()">Registrar Local</button>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} BodyFix. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Modal: Registrar Local -->
    <div class="modal-overlay" id="modal-registrar" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal">
            <div class="modal-header">
                <div>
                    <h3 id="modal-title">Registrá tu local</h3>
                    <p>Completá el formulario y nos ponemos en contacto a la brevedad.</p>
                </div>
                <button class="modal-close" id="modal-close" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-body">
                <form id="form-registrar" novalidate>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="f-nombre">Nombre <span class="required">*</span></label>
                            <input type="text" id="f-nombre" name="nombre" placeholder="Tu nombre" maxlength="100">
                            <span class="field-error" id="error-nombre"></span>
                        </div>
                        <div class="form-group">
                            <label for="f-apellido">Apellido <span class="required">*</span></label>
                            <input type="text" id="f-apellido" name="apellido" placeholder="Tu apellido" maxlength="100">
                            <span class="field-error" id="error-apellido"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="f-nombre-local">Nombre del local <span class="required">*</span></label>
                        <input type="text" id="f-nombre-local" name="nombre_local" placeholder="Nombre de tu local o negocio" maxlength="200">
                        <span class="field-error" id="error-nombre_local"></span>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="f-direccion">Dirección <span class="required">*</span></label>
                            <input type="text" id="f-direccion" name="direccion" placeholder="Ej: Av. Corrientes 1234, CABA" maxlength="300">
                            <span class="field-error" id="error-direccion"></span>
                        </div>
                        <div class="form-group">
                            <label for="f-cuit">CUIT <span class="required">*</span></label>
                            <input type="text" id="f-cuit" name="cuit" placeholder="XX-XXXXXXXX-X" maxlength="20">
                            <span class="field-error" id="error-cuit"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="f-email">Correo electrónico <span class="required">*</span></label>
                            <input type="email" id="f-email" name="email" placeholder="tuemail@ejemplo.com" maxlength="200">
                            <span class="field-error" id="error-email"></span>
                        </div>
                        <div class="form-group">
                            <label for="f-telefono">Teléfono <span class="required">*</span></label>
                            <input type="tel" id="f-telefono" name="telefono" placeholder="11 1234-5678" maxlength="20">
                            <span class="field-error" id="error-telefono"></span>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="f-instagram">Instagram</label>
                            <input type="text" id="f-instagram" name="instagram" placeholder="@tulocal" maxlength="100">
                            <span class="field-error" id="error-instagram"></span>
                        </div>
                        <div class="form-group">
                            <label for="f-localidad">Localidad (CABA) <span class="required">*</span></label>
                            <select id="f-localidad" name="localidad">
                                <option value="">Seleccioná tu barrio</option>
                                <option>Agronomía</option>
                                <option>Almagro</option>
                                <option>Balvanera</option>
                                <option>Barracas</option>
                                <option>Belgrano</option>
                                <option>Boedo</option>
                                <option>Caballito</option>
                                <option>Chacarita</option>
                                <option>Coghlan</option>
                                <option>Colegiales</option>
                                <option>Constitución</option>
                                <option>Flores</option>
                                <option>Floresta</option>
                                <option>La Boca</option>
                                <option>La Paternal</option>
                                <option>Liniers</option>
                                <option>Mataderos</option>
                                <option>Monte Castro</option>
                                <option>Monserrat</option>
                                <option>Nueva Pompeya</option>
                                <option>Núñez</option>
                                <option>Palermo</option>
                                <option>Parque Avellaneda</option>
                                <option>Parque Chacabuco</option>
                                <option>Parque Chas</option>
                                <option>Parque Patricios</option>
                                <option>Puerto Madero</option>
                                <option>Recoleta</option>
                                <option>Retiro</option>
                                <option>Saavedra</option>
                                <option>San Cristóbal</option>
                                <option>San Nicolás</option>
                                <option>San Telmo</option>
                                <option>Vélez Sársfield</option>
                                <option>Versalles</option>
                                <option>Villa Crespo</option>
                                <option>Villa del Parque</option>
                                <option>Villa Devoto</option>
                                <option>Villa General Mitre</option>
                                <option>Villa Lugano</option>
                                <option>Villa Luro</option>
                                <option>Villa Ortúzar</option>
                                <option>Villa Pueyrredón</option>
                                <option>Villa Real</option>
                                <option>Villa Riachuelo</option>
                                <option>Villa Santa Rita</option>
                                <option>Villa Soldati</option>
                                <option>Villa Urquiza</option>
                            </select>
                            <span class="field-error" id="error-localidad"></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="f-descripcion">Descripción</label>
                        <textarea id="f-descripcion" name="descripcion" placeholder="Contanos sobre tu local, los servicios que ofrecés o cualquier consulta..." rows="4" maxlength="1000"></textarea>
                        <span class="field-error" id="error-descripcion"></span>
                    </div>

                    <div class="form-info-box">
                        <strong>📍 Coordenadas GPS</strong> — opcionales, pero recomendadas. Permiten que los clientes encuentren tu local fácilmente en el mapa. Podés obtenerlas haciendo clic derecho sobre tu local en <a href="https://maps.google.com" target="_blank" rel="noopener">Google Maps</a>.
                    </div>
                    <div class="form-row coord-fields">
                        <div class="form-group">
                            <label for="f-latitude">Latitud</label>
                            <input type="number" id="f-latitude" name="latitude" placeholder="-34.6037" step="0.0000001" min="-90" max="90">
                            <span class="field-error" id="error-latitude"></span>
                        </div>
                        <div class="form-group">
                            <label for="f-longitude">Longitud</label>
                            <input type="number" id="f-longitude" name="longitude" placeholder="-58.3816" step="0.0000001" min="-180" max="180">
                            <span class="field-error" id="error-longitude"></span>
                        </div>
                    </div>

                    <div class="form-footer">
                        <p class="form-note"><span class="required">*</span> Campos obligatorios</p>
                        <button type="submit" class="btn-primary btn-block" id="btn-submit-registro">Enviar solicitud</button>
                    </div>
                </form>
                <div class="registro-success hidden" id="registro-success">
                    <div class="success-icon">✓</div>
                    <h4>¡Solicitud enviada con éxito!</h4>
                    <p>Recibimos tu registro. Nos pondremos en contacto a la brevedad.</p>
                    <button class="btn-primary" onclick="closeModal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // === Mobile menu ===
        const mobileMenuBtn = document.getElementById('mobile-menu');
        const nav = document.getElementById('nav');
        mobileMenuBtn.addEventListener('click', () => {
            nav.classList.toggle('open');
            mobileMenuBtn.classList.toggle('open');
        });

        // === Logo → scroll al inicio ===
        document.getElementById('logo-link').addEventListener('click', function (e) {
            e.preventDefault();
            nav.classList.remove('open');
            mobileMenuBtn.classList.remove('open');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // === Smooth scroll ===
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    nav.classList.remove('open');
                    mobileMenuBtn.classList.remove('open');
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // === Header scroll effect ===
        window.addEventListener('scroll', () => {
            document.getElementById('header').classList.toggle('scrolled', window.scrollY > 50);
        });

        // === Modal ===
        const modal = document.getElementById('modal-registrar');

        document.getElementById('btn-registrar-local').addEventListener('click', openModal);
        document.getElementById('modal-close').addEventListener('click', closeModal);
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        function openModal() {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            resetModal();
        }

        function resetModal() {
            document.getElementById('form-registrar').reset();
            document.getElementById('form-registrar').classList.remove('hidden');
            document.getElementById('registro-success').classList.add('hidden');
            clearErrors();
        }

        // === Form submit ===
        document.getElementById('form-registrar').addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!validateForm()) return;

            const btn = document.getElementById('btn-submit-registro');
            btn.disabled = true;
            btn.textContent = 'Enviando...';

            const latVal = document.getElementById('f-latitude').value.trim();
            const lngVal = document.getElementById('f-longitude').value.trim();
            const data = {
                nombre:       document.getElementById('f-nombre').value.trim(),
                apellido:     document.getElementById('f-apellido').value.trim(),
                nombre_local: document.getElementById('f-nombre-local').value.trim(),
                direccion:    document.getElementById('f-direccion').value.trim(),
                cuit:         document.getElementById('f-cuit').value.trim(),
                instagram:    document.getElementById('f-instagram').value.trim(),
                email:        document.getElementById('f-email').value.trim(),
                telefono:     document.getElementById('f-telefono').value.trim(),
                descripcion:  document.getElementById('f-descripcion').value.trim(),
                localidad:    document.getElementById('f-localidad').value,
                latitude:     latVal !== '' ? parseFloat(latVal) : null,
                longitude:    lngVal !== '' ? parseFloat(lngVal) : null,
            };

            try {
                const res = await fetch('/registrar-local', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data),
                });

                if (res.ok) {
                    document.getElementById('form-registrar').classList.add('hidden');
                    document.getElementById('registro-success').classList.remove('hidden');
                } else if (res.status === 422) {
                    const { errors } = await res.json();
                    Object.entries(errors).forEach(([field, messages]) => {
                        const el = document.getElementById('error-' + field);
                        if (el) el.textContent = messages[0];
                        const input = document.getElementById('f-' + field.replace('_', '-'));
                        if (input) input.classList.add('input-error');
                    });
                } else {
                    alert('Ocurrió un error inesperado. Por favor intentá de nuevo.');
                }
            } catch {
                alert('Error de conexión. Verificá tu internet e intentá de nuevo.');
            } finally {
                btn.disabled = false;
                btn.textContent = 'Enviar solicitud';
            }
        });

        function validateForm() {
            clearErrors();
            let valid = true;

            const required = [
                { id: 'f-nombre',       errorId: 'error-nombre',       msg: 'El nombre es requerido.' },
                { id: 'f-apellido',     errorId: 'error-apellido',     msg: 'El apellido es requerido.' },
                { id: 'f-nombre-local', errorId: 'error-nombre_local', msg: 'El nombre del local es requerido.' },
                { id: 'f-direccion',    errorId: 'error-direccion',    msg: 'La dirección es requerida.' },
                { id: 'f-cuit',         errorId: 'error-cuit',         msg: 'El CUIT es requerido.' },
                { id: 'f-email',        errorId: 'error-email',        msg: 'El correo electrónico es requerido.' },
                { id: 'f-telefono',     errorId: 'error-telefono',     msg: 'El teléfono es requerido.' },
                { id: 'f-localidad',    errorId: 'error-localidad',    msg: 'Seleccioná una localidad.' },
            ];

            required.forEach(({ id, errorId, msg }) => {
                const el = document.getElementById(id);
                if (!el.value.trim()) {
                    document.getElementById(errorId).textContent = msg;
                    el.classList.add('input-error');
                    valid = false;
                }
            });

            const emailEl = document.getElementById('f-email');
            if (emailEl.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailEl.value)) {
                document.getElementById('error-email').textContent = 'Ingresá un email válido.';
                emailEl.classList.add('input-error');
                valid = false;
            }

            const telEl = document.getElementById('f-telefono');
            if (telEl.value && !/^[\d\s\-\+\(\)]{6,20}$/.test(telEl.value)) {
                document.getElementById('error-telefono').textContent = 'Ingresá un teléfono válido.';
                telEl.classList.add('input-error');
                valid = false;
            }

            const latEl = document.getElementById('f-latitude');
            if (latEl.value !== '') {
                const lat = parseFloat(latEl.value);
                if (isNaN(lat) || lat < -90 || lat > 90) {
                    document.getElementById('error-latitude').textContent = 'Ingresá una latitud válida (entre -90 y 90).';
                    latEl.classList.add('input-error');
                    valid = false;
                }
            }

            const lngEl = document.getElementById('f-longitude');
            if (lngEl.value !== '') {
                const lng = parseFloat(lngEl.value);
                if (isNaN(lng) || lng < -180 || lng > 180) {
                    document.getElementById('error-longitude').textContent = 'Ingresá una longitud válida (entre -180 y 180).';
                    lngEl.classList.add('input-error');
                    valid = false;
                }
            }

            return valid;
        }

        function clearErrors() {
            document.querySelectorAll('.field-error').forEach(el => el.textContent = '');
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        }

        function handleContactSubmit() {
            const emailEl = document.getElementById('contact-email');
            if (!emailEl.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailEl.value)) {
                emailEl.style.borderColor = '#e53e3e';
                return;
            }
            emailEl.style.borderColor = '';
            document.getElementById('contact-success').classList.remove('hidden');
            emailEl.value = '';
        }

        // === Scroll animations ===
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.step, .testimonial-card, .stat-item, .feature-item').forEach(el => {
            el.classList.add('fade-in');
            observer.observe(el);
        });
    </script>
</body>

</html>
