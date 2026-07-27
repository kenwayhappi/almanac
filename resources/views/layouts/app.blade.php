<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Almanac - La Bible des Villages & Groupements')</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">

  <!-- Icons & Bootstrap -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
  <link rel="icon" type="image/png" href="{{ asset('images/logofinal.png') }}">

  <style>
    :root {
      --bg-body: #f8fafc;
      --bg-card: #ffffff;
      --bg-header: #0f172a;
      --bg-nav: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --accent-green: #16a34a;
      --accent-green-hover: #15803d;
      --accent-gold: #d97706;
      --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.03);
      --glass-bg: rgba(255, 255, 255, 0.85);
      --glass-border: rgba(226, 232, 240, 0.8);
      --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    html[data-theme="dark"] {
      --bg-body: #0b0f17;
      --bg-card: #151d2a;
      --bg-header: #070a10;
      --bg-nav: #111827;
      --text-main: #f8fafc;
      --text-muted: #cbd5e1;
      --border-color: #1e293b;
      --accent-green: #22c55e;
      --accent-green-hover: #16a34a;
      --accent-gold: #f59e0b;
      --card-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.5);
      --glass-bg: rgba(17, 24, 39, 0.85);
      --glass-border: rgba(30, 41, 59, 0.8);
    }

    html[data-theme="dark"] .text-dark,
    html[data-theme="dark"] .text-muted,
    html[data-theme="dark"] label,
    html[data-theme="dark"] p,
    html[data-theme="dark"] span {
      color: var(--text-main) !important;
    }

    html[data-theme="dark"] .text-muted {
      color: #cbd5e1 !important;
    }

    html[data-theme="dark"] .bg-light,
    html[data-theme="dark"] .bg-body-tertiary {
      background-color: #111827 !important;
    }

    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select {
      background-color: #1e293b !important;
      color: #f8fafc !important;
      border-color: #334155 !important;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: var(--bg-body);
      color: var(--text-main);
      transition: background-color 0.3s ease, color 0.3s ease;
      overflow-x: hidden;
    }

    h1, h2, h3, h4, .font-serif {
      font-family: 'Playfair Display', serif;
    }

    /* Footer Styles - Legibility Fix */
    .main-footer {
      background-color: #070a10 !important;
      color: #e2e8f0 !important;
      border-top: 1px solid rgba(255,255,255,0.08);
      padding: 60px 0 30px;
    }

    .main-footer p, .main-footer span, .main-footer li {
      color: #e2e8f0 !important;
      font-size: 0.92rem;
      line-height: 1.6;
    }

    .main-footer h5 {
      color: #ffffff !important;
      font-weight: 700;
      font-size: 1.15rem;
      margin-bottom: 20px;
    }

    .main-footer a {
      color: #cbd5e1 !important;
      text-decoration: none;
      transition: var(--transition);
    }

    .main-footer a:hover {
      color: var(--accent-green) !important;
      padding-left: 4px;
    }

    /* Top Bar */
    .top-bar {
      background-color: var(--bg-header);
      color: #94a3b8;
      font-size: 0.825rem;
      padding: 6px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .top-bar a {
      color: #cbd5e1;
      text-decoration: none;
      transition: var(--transition);
    }

    .top-bar a:hover {
      color: var(--accent-green);
    }

    /* Navbar Header */
    .main-navbar {
      background-color: var(--glass-bg);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border-bottom: 1px solid var(--glass-border);
      position: sticky;
      top: 0;
      z-index: 1040;
      transition: var(--transition);
    }

    .brand-logo {
      height: 48px;
      width: auto;
      object-fit: contain;
      transition: transform 0.3s ease;
    }

    .brand-logo:hover {
      transform: scale(1.05);
    }

    .brand-title {
      font-size: 1.35rem;
      font-weight: 800;
      color: var(--text-main);
      letter-spacing: -0.02em;
    }

    .brand-title span {
      color: var(--accent-green);
    }

    .nav-link-custom {
      color: var(--text-main);
      font-weight: 600;
      font-size: 0.95rem;
      padding: 8px 16px !important;
      border-radius: 999px;
      transition: var(--transition);
      text-decoration: none;
    }

    .nav-link-custom:hover, .nav-link-custom.active {
      color: var(--accent-green) !important;
      background-color: rgba(34, 197, 94, 0.08);
    }

    .theme-toggle-btn {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 1px solid var(--border-color);
      background: var(--bg-card);
      color: var(--text-main);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: var(--transition);
    }

    .theme-toggle-btn:hover {
      border-color: var(--accent-green);
      color: var(--accent-green);
      transform: rotate(15deg);
    }

    .btn-accent {
      background: linear-gradient(135deg, var(--accent-green), var(--accent-green-hover));
      color: #ffffff !important;
      font-weight: 700;
      border-radius: 999px;
      padding: 8px 20px;
      border: none;
      box-shadow: 0 4px 14px rgba(22, 163, 74, 0.25);
      transition: var(--transition);
    }

    .btn-accent:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(22, 163, 74, 0.35);
      color: #ffffff;
    }

    /* Cards & Containers */
    .custom-card {
      background-color: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: var(--card-shadow);
      transition: var(--transition);
    }

    .custom-card:hover {
      border-color: rgba(34, 197, 94, 0.4);
      transform: translateY(-3px);
    }

    /* Offcanvas Mobile Drawer */
    .mobile-drawer {
      background-color: var(--bg-card);
      color: var(--text-main);
      border-left: 1px solid var(--border-color);
    }

    /* Footer */
    .main-footer {
      background-color: var(--bg-header);
      color: #94a3b8;
      border-top: 1px solid rgba(255,255,255,0.05);
      padding: 60px 0 30px;
    }

    .main-footer h5 {
      color: #f8fafc;
      font-weight: 700;
      font-size: 1.1rem;
      margin-bottom: 20px;
    }

    .main-footer a {
      color: #94a3b8;
      text-decoration: none;
      transition: var(--transition);
    }

    .main-footer a:hover {
      color: var(--accent-green);
      padding-left: 4px;
    }

    .footer-bottom {
      border-top: 1px solid rgba(255,255,255,0.08);
      margin-top: 40px;
      padding-top: 20px;
    }

    /* Ad Viewer Modal Styling */
    .ad-modal-content {
      background-color: var(--bg-card);
      color: var(--text-main);
      border: 1px solid var(--border-color);
      border-radius: 20px;
      overflow: hidden;
    }

    .ad-modal-header {
      border-bottom: 1px solid var(--border-color);
    }

    .ad-media-container {
      max-height: 70vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #000;
      border-radius: 12px;
      overflow: hidden;
    }

    .ad-media-container img, .ad-media-container video {
      max-height: 65vh;
      width: auto;
      max-width: 100%;
      object-fit: contain;
    }
  </style>

  @yield('styles')
</head>
<body>

  <!-- Top Bar -->
  <div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center">
      <div>
        <i class="fas fa-globe-africa me-2 text-success"></i>
        <span>Almanac - Plateforme Culturelle & Atlas Démographique</span>
      </div>
      <div class="d-none d-md-flex align-items-center gap-3">
        <a href="mailto:contact@almanac.cm"><i class="fas fa-envelope me-1"></i> contact@almanac.cm</a>
        <span>|</span>
        <a href="{{ route('login') }}"><i class="fas fa-lock me-1"></i> Espace Admin</a>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg main-navbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('accueil') }}">
        <img src="{{ asset('images/logofinal.png') }}" alt="Almanac Logo" class="brand-logo">
        <span class="brand-title">Almanac<span>.</span></span>
      </a>

      <!-- Controls Right (Mobile & Desktop) -->
      <div class="d-flex align-items-center gap-2 order-lg-last">
        <!-- Theme Toggle -->
        <button type="button" class="theme-toggle-btn" id="themeToggleBtn" title="Changer de thème (Clair/Sombre)">
          <i class="fas fa-moon" id="themeIcon"></i>
        </button>

        <!-- Search CTA -->
        <a href="{{ route('recherche') }}" class="btn btn-accent d-none d-sm-inline-flex align-items-center gap-2">
          <i class="fas fa-search"></i>
          <span>Explorer</span>
        </a>

        <!-- Mobile Toggler -->
        <button class="navbar-toggler border-0 shadow-none text-main" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
          <i class="fas fa-bars fs-4"></i>
        </button>
      </div>

      <!-- Desktop Nav Links -->
      <div class="collapse navbar-collapse justify-content-center" id="desktopNav">
        <ul class="navbar-nav align-items-center gap-1">
          <li class="nav-item">
            <a class="nav-link-custom {{ request()->routeIs('accueil') ? 'active' : '' }}" href="{{ route('accueil') }}">Accueil</a>
          </li>
          <li class="nav-item">
            <a class="nav-link-custom {{ request()->routeIs('recherche') ? 'active' : '' }}" href="{{ route('recherche') }}">Recherche & Filtres</a>
          </li>
          <li class="nav-item">
            <a class="nav-link-custom {{ request()->routeIs('a-propos') ? 'active' : '' }}" href="{{ route('a-propos') }}">À Propos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link-custom {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Mobile Offcanvas Menu -->
  <div class="offcanvas offcanvas-end mobile-drawer" tabindex="-1" id="mobileMenu">
    <div class="offcanvas-header border-bottom border-secondary border-opacity-10">
      <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/logofinal.png') }}" alt="Logo" class="brand-logo" style="height:36px;">
        <span class="fw-bold fs-5">Almanac</span>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
      <div class="d-flex flex-column gap-3">
        <a class="nav-link-custom py-2 border-bottom" href="{{ route('accueil') }}"><i class="fas fa-home me-2"></i> Accueil</a>
        <a class="nav-link-custom py-2 border-bottom" href="{{ route('recherche') }}"><i class="fas fa-search me-2"></i> Recherche & Filtres</a>
        <a class="nav-link-custom py-2 border-bottom" href="{{ route('a-propos') }}"><i class="fas fa-info-circle me-2"></i> À Propos</a>
        <a class="nav-link-custom py-2 border-bottom" href="{{ route('contact') }}"><i class="fas fa-envelope me-2"></i> Contact</a>
        <a class="btn btn-accent w-100 mt-3 py-2" href="{{ route('login') }}"><i class="fas fa-sign-in-alt me-2"></i> Espace d'Administration</a>
      </div>
    </div>
  </div>

  <!-- Main Content -->
  <main class="py-4">
    @yield('content')
  </main>

  <!-- Publicité Viewer Modal (With Background Carousel Pause) -->
  <div class="modal fade" id="adViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content ad-modal-content">
        <div class="modal-header ad-modal-header py-3 px-4">
          <h5 class="modal-title font-serif fw-bold" id="adModalTitle">Publicité</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>
        <div class="modal-body p-4" id="adModalBody">
          <!-- Dynamic media content injected via JS -->
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10 py-2 px-4 justify-content-between">
          <small class="text-muted"><i class="fas fa-eye me-1"></i> Impression enregistrée de manière unique</small>
          <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Fermer</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="main-footer">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-4 col-md-6">
          <div class="d-flex align-items-center gap-2 mb-3">
            <img src="{{ asset('images/logofinal.png') }}" alt="Almanac Logo" style="height:40px;">
            <span class="font-serif fw-bold fs-4 text-white">Almanac</span>
          </div>
          <p class="small text-muted mb-3">
            La bible numérique d'inventaire, de préservation et de découverte du patrimoine culturel, historique et démographique des villages et groupements.
          </p>
          <div class="d-flex gap-2">
            <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:36px;height:36px;"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:36px;height:36px;"><i class="fab fa-twitter"></i></a>
            <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:36px;height:36px;"><i class="fab fa-instagram"></i></a>
            <a href="#" class="btn btn-sm btn-outline-secondary rounded-circle" style="width:36px;height:36px;"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>

        <div class="col-lg-2 col-md-6">
          <h5>Navigation</h5>
          <ul class="list-unstyled d-flex flex-column gap-2 small">
            <li><a href="{{ route('accueil') }}">Accueil</a></li>
            <li><a href="{{ route('recherche') }}">Moteur de Recherche</a></li>
            <li><a href="{{ route('liste.groupements') }}">Groupements</a></li>
            <li><a href="{{ route('liste.list') }}">Répertoire des Villages</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h5>Informations</h5>
          <ul class="list-unstyled d-flex flex-column gap-2 small">
            <li><a href="{{ route('a-propos') }}">À propos du projet</a></li>
            <li><a href="{{ route('contact') }}">Nous contacter</a></li>
            <li><a href="{{ route('login') }}">Accès Partenaires / Admin</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h5>Contact</h5>
          <ul class="list-unstyled d-flex flex-column gap-2 small">
            <li><i class="fas fa-map-marker-alt text-success me-2"></i> Yaoundé, Cameroun</li>
            <li><i class="fas fa-envelope text-success me-2"></i> contact@almanac.cm</li>
            <li><i class="fas fa-phone-alt text-success me-2"></i> +237 699 99 99 99</li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom text-center py-3">
        <p class="small mb-0">© {{ date('Y') }} Almanac. Tous droits réservés.</p>
      </div>
    </div>
  </footer>

  <!-- Floating Back-to-Top Button (Appears on Scroll) -->
  <button id="btnBackToTop" class="btn btn-success rounded-circle shadow-lg position-fixed bottom-0 end-0 m-4 p-0 d-none align-items-center justify-content-center" style="z-index: 1080; width: 46px; height: 46px; transition: all 0.3s ease;" title="Haut de page">
    <i class="fas fa-arrow-up fs-6"></i>
  </button>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

  <script>
    // Theme Switcher Logic (Dark & Light Mode)
    (function() {
      const savedTheme = localStorage.getItem('almanac_theme') || 'light';
      document.documentElement.setAttribute('data-theme', savedTheme);
      updateThemeIcon(savedTheme);

      document.getElementById('themeToggleBtn')?.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('almanac_theme', newTheme);
        updateThemeIcon(newTheme);
      });

      function updateThemeIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (icon) {
          icon.className = theme === 'dark' ? 'fas fa-sun text-warning' : 'fas fa-moon';
        }
      }
    })();

    // Publicité Viewer & Carousel Pause Logic
    function viewAdvertisement(id, title, type, fileUrl, contentText) {
      // 1. Pause background carousels so they don't rotate while reading ad
      $('.owl-carousel').trigger('stop.owl.carousel');
      $('.carousel').carousel('pause');

      // 2. Track unique machine view via AJAX
      $.ajax({
        url: '/publicite/' + id + '/track-view',
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(res) {
          console.log('Ad view tracked (Unique):', res);
        }
      });

      // 3. Render Content inside Modal
      $('#adModalTitle').text(title || 'Publicité Sponsorisée');
      let bodyHtml = '';

      if (type === 'video') {
        bodyHtml = `
          <div class="ad-media-container">
            <video controls autoplay class="w-100">
              <source src="${fileUrl}" type="video/mp4">
              Votre navigateur ne prend pas en charge la vidéo.
            </video>
          </div>`;
      } else if (type === 'photo') {
        bodyHtml = `
          <div class="ad-media-container">
            <img src="${fileUrl}" alt="${title}" class="img-fluid rounded">
          </div>`;
      } else if (type === 'pdf') {
        bodyHtml = `
          <div class="ratio ratio-16x9">
            <iframe src="${fileUrl}" allowfullscreen></iframe>
          </div>
          <div class="text-center mt-3">
            <a href="${fileUrl}" target="_blank" class="btn btn-sm btn-success rounded-pill">
              <i class="fas fa-download me-1"></i> Télécharger le PDF
            </a>
          </div>`;
      } else if (type === 'text') {
        bodyHtml = `<div class="p-3 bg-body-tertiary rounded fs-5">${contentText || ''}</div>`;
      }

      $('#adModalBody').html(bodyHtml);
      const modal = new bootstrap.Modal(document.getElementById('adViewerModal'));
      modal.show();
    }

    // Floating Back to Top Button on Scroll
    window.addEventListener('scroll', function () {
      const btn = document.getElementById('btnBackToTop');
      if (btn) {
        if (window.scrollY > 60) {
          btn.classList.remove('d-none');
          btn.classList.add('d-flex');
        } else {
          btn.classList.remove('d-flex');
          btn.classList.add('d-none');
        }
      }
    });

    document.getElementById('btnBackToTop')?.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    // Resume carousels when modal closes
    document.getElementById('adViewerModal')?.addEventListener('hidden.bs.modal', function () {
      $('.owl-carousel').trigger('play.owl.carousel', [4000]);
      $('.carousel').carousel('cycle');
    });
  </script>

  @yield('scripts')
</body>
</html>
