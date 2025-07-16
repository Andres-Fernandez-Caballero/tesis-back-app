<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BodyFix - Your Personalized Wellness Journey</title>
    <link rel="shortcut icon" href="{{ asset("icon.png") }}" type="image">
    <link rel="stylesheet" href="{{ asset("css/landing.css") }}">
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="logo">
                <h2>BodyFix</h2>
            </div>
            <nav class="nav">
                <a href="#about">About</a>
                <a href="#testimonials">{{ __('landing.navigation.testimonials') }}</a>
                <a href="#contact">{{  __('landing.navigation.contact') }}</a>
                <a href="{{ route('login') }}" class="btn-primary">{{ __('landing.navigation.bookings_now') }}</a>
            </nav>
            <div class="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Your Personalized Wellness Journey Starts Here</h1>
                    <p>Health and well-being are the key to a happy and fulfilling life.</p>
                    <a href="{{ route('login') }}" class="btn-primary">Bookings Now</a>
                </div>
                <div class="hero-image">
                    <img src="{{ asset("assets/home-banner-image.png") }}?height=400&width=400"
                        alt="Massage therapy session">
                </div>
            </div>
        </div>
        <div class="hero-bg-shape"></div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-image">
                    <img src="{{asset("assets/about-background-image.png")}}?height=400&width=400"
                        alt="Foot massage therapy">
                </div>
                <div class="about-text">
                    <span class="section-label">About</span>
                    <h2>Get to know us</h2>
                    <p>We are a team of wellness professionals passionate about helping you achieve your health goals
                        through therapeutic and relaxing massages.</p>
                    <p>Professional and experienced trainers to help you in your therapeutic journey.</p>
                    <div class="about-actions">
                        <button class="btn-primary">Download App</button>
                        <button class="btn-video">
                            <span class="play-icon">▶</span>
                            Watch video
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="about-bg-shape"></div>
    </section>

    <!-- How it Works Section -->
    <section class="how-it-works">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Steps</span>
                <h2>How it works</h2>
                <p>With BodyFix, you can book a relaxing massage from the comfort of your home. Choose the type of
                    massage, how often you want it, and receive your therapist wherever you are — quickly and easily!
                </p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 100 100" fill="currentColor">
                            <circle cx="50" cy="30" r="15" />
                            <path d="M20 50 Q30 40 50 50 Q70 40 80 50 L75 80 Q50 90 25 80 Z" />
                        </svg>
                    </div>
                    <h3>Choose your massage</h3>
                    <p>Select the type of massage that best suits your needs: relaxing, deep tissue, sports, or
                        therapeutic.</p>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 100 100" fill="currentColor">
                            <path d="M50 10 L60 40 L90 40 L70 60 L80 90 L50 70 L20 90 L30 60 L10 40 L40 40 Z" />
                        </svg>
                    </div>
                    <h3>Choose the frequency</h3>
                    <p>You can book a one-time session or schedule recurring sessions based on your lifestyle and
                        wellness goals.</p>
                </div>
                <div class="step">
                    <div class="step-icon">
                        <svg viewBox="0 0 100 100" fill="currentColor">
                            <rect x="10" y="30" width="80" height="40" rx="5" />
                            <rect x="20" y="20" width="60" height="10" rx="5" />
                            <circle cx="30" cy="50" r="8" />
                            <circle cx="50" cy="50" r="8" />
                            <circle cx="70" cy="50" r="8" />
                        </svg>
                    </div>
                    <h3>Receive your therapist</h3>
                    <p>A certified professional will arrive at your home at the scheduled time, bringing everything
                        needed without leaving your home.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="section-header">
                <span class="section-label">Testimonials</span>
                <h2>What they say</h2>
                <p>Our users trust BodyFix to improve their well-being. Here's what some of them have to say about their
                    experience.</p>
            </div>
            <div class="testimonial-card">
                <div class="testimonial-avatar">
                    <img src="{{asset("assets/john-doe-image.png")}}?height=80&width=80" alt="John Doe">
                </div>
                <div class="testimonial-content">
                    <p>"Booking a massage has never been this easy. Within minutes everything was scheduled and the
                        therapist arrived right on time. It was relaxing, well worth it, and I highly recommend
                        BodyFix!"</p>
                    <div class="stars">
                        <span>★★★★★</span>
                    </div>
                    <h4>John Doe</h4>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <div class="contact-content">
                <h2>Have a question? Let us help you</h2>
                <div class="contact-form">
                    <input type="email" placeholder="youremail@gmail.com">
                    <button class="btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-brand">
                    <h3>BodyFix</h3>
                    <div class="social-links">
                        <a href="#" aria-label="Instagram">📷</a>
                        <a href="#" aria-label="LinkedIn">💼</a>
                        <a href="#" aria-label="Email">✉️</a>
                        <a href="#" aria-label="Facebook">📘</a>
                    </div>
                </div>
                <div class="footer-info">
                    <div class="footer-column">
                        <p>844-328-7793</p>
                        <p>youremail@gmail.com</p>
                        <p>pressbodyfix.com</p>
                        <p>www.bodyfix.com</p>
                    </div>
                    <div class="footer-column">
                        <a href="#">Terms & Conditions</a>
                        <a href="#">Privacy Policy</a>
                        <a href="#">contact@bodyfix.com</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>