<!DOCTYPE html>
<html lang="fr" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Almanac Admin - Administration')</title>

  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Bootstrap 5 CSS & Chart.js -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    :root {
      --bg-body: #f8fafc;
      --bg-sidebar: #0f172a;
      --bg-card: #ffffff;
      --text-main: #0f172a;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
      --accent-green: #16a34a;
      --accent-green-hover: #15803d;
      --sidebar-width: 290px;
      --header-height: 64px;
    }

    html[data-theme="dark"] {
      --bg-body: #0b0f17;
      --bg-sidebar: #070a10;
      --bg-card: #151d2a;
      --text-main: #f8fafc;
      --text-muted: #94a3b8;
      --border-color: #1e293b;
      --accent-green: #22c55e;
      --accent-green-hover: #16a34a;
    }

    /* Force Uniform Dark Theme on Form Elements & Cards */
    html[data-theme="dark"] .form-control,
    html[data-theme="dark"] .form-select,
    html[data-theme="dark"] .input-group-text,
    html[data-theme="dark"] .dropdown-menu,
    html[data-theme="dark"] .modal-content,
    html[data-theme="dark"] .bg-white,
    html[data-theme="dark"] .card {
      background-color: var(--bg-card) !important;
      color: var(--text-main) !important;
      border-color: var(--border-color) !important;
    }

    html[data-theme="dark"] .form-control::placeholder,
    html[data-theme="dark"] .form-select::placeholder {
      color: var(--text-muted) !important;
    }

    html[data-theme="dark"] .table,
    html[data-theme="dark"] .table > :not(caption) > * > * {
      background-color: var(--bg-card) !important;
      color: var(--text-main) !important;
      border-color: var(--border-color) !important;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif;
      background-color: var(--bg-body);
      color: var(--text-main);
      min-height: 100vh;
      margin: 0;
    }

    /* Top Navbar */
    .admin-navbar {
      height: var(--header-height);
      background-color: var(--bg-card);
      border-bottom: 1px solid var(--border-color);
      position: fixed;
      top: 0;
      right: 0;
      left: 0;
      z-index: 1030;
      padding: 0 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    /* Sidebar */
    .admin-sidebar {
      width: var(--sidebar-width);
      background-color: var(--bg-sidebar);
      position: fixed;
      top: var(--header-height);
      bottom: 0;
      left: 0;
      z-index: 1020;
      overflow-y: auto;
      border-right: 1px solid var(--border-color);
      padding: 1.5rem 1rem;
      transition: all 0.3s ease;
    }

    .sidebar-section-title {
      font-size: 0.7rem;
      font-weight: 800;
      text-uppercase;
      letter-spacing: 0.08em;
      color: #64748b;
      margin: 1.25rem 0 0.5rem 0.75rem;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      padding: 0.65rem 0.85rem;
      color: #94a3b8;
      font-size: 0.9rem;
      font-weight: 600;
      border-radius: 10px;
      text-decoration: none;
      transition: all 0.2s ease;
    }

    .sidebar-link:hover, .sidebar-link.active {
      color: #ffffff;
      background-color: rgba(34, 197, 94, 0.15);
    }

    .sidebar-link.active {
      color: var(--accent-green);
      font-weight: 700;
    }

    /* Main Content Wrapper */
    .admin-main {
      margin-top: var(--header-height);
      margin-left: var(--sidebar-width);
      padding: 2rem;
      transition: margin-left 0.3s ease;
    }

    /* Custom Cards */
    .admin-card {
      background-color: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.1);
      padding: 1.5rem;
    }

    .kpi-card {
      background-color: var(--bg-card);
      border: 1px solid var(--border-color);
      border-radius: 16px;
      padding: 1.25rem;
      display: flex;
      align-items: center;
      gap: 1.25rem;
      transition: transform 0.2s ease;
    }

    .kpi-card:hover {
      transform: translateY(-2px);
      border-color: var(--accent-green);
    }

    .kpi-icon {
      width: 54px;
      height: 54px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .theme-toggle-btn {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      border: 1px solid var(--border-color);
      background: var(--bg-card);
      color: var(--text-main);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    @media (max-width: 991.98px) {
      .admin-sidebar {
        transform: translateX(-100%);
      }
      .admin-sidebar.show {
        transform: translateX(0);
      }
      .admin-main {
        margin-left: 0;
      }
    }
  </style>

  @yield('styles')
</head>
<body>

  <!-- Top Navbar -->
  <header class="admin-navbar">
    <div class="d-flex align-items-center gap-3">
      <button class="btn p-0 d-lg-none text-main fs-4" id="sidebarToggleBtn">
        <i class="fas fa-bars"></i>
      </button>
      <a href="{{ route('dashboard.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
        <img src="{{ asset('images/logofinal.png') }}" alt="Logo" style="height:36px;">
        <span class="font-serif fw-bold fs-4 text-main">Almanac <span class="badge bg-success bg-opacity-10 text-success fs-xs">Admin</span></span>
      </a>
    </div>

    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('accueil') }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill d-none d-sm-inline-flex align-items-center gap-1">
        <i class="fas fa-external-link-alt"></i> Voir le site
      </a>

      <!-- Theme Switcher -->
      <button type="button" class="theme-toggle-btn" id="adminThemeToggleBtn" title="Changer de thème">
        <i class="fas fa-sun text-warning" id="adminThemeIcon"></i>
      </button>

      <!-- User Menu -->
      <div class="dropdown">
        <button class="btn btn-outline-secondary rounded-pill dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
          <i class="fas fa-user-circle fs-5 text-success"></i>
          <span class="fw-semibold small d-none d-sm-inline">{{ Auth::user()->name ?? 'Administrateur' }}</span>
        </button>
        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
          <li><span class="dropdown-header">Compte Admin</span></li>
          <li>
            <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('dashboard.profile.show') }}">
              <i class="fas fa-user-cog text-success"></i> Mon Profil
            </a>
          </li>
          <li><hr class="dropdown-divider"></li>
          <li>
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </header>

  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-brand-badge">
      <span>🇨🇲</span> Almanac Cameroun (+237)
    </div>

    <div class="sidebar-section-title"><i class="fas fa-compass me-1"></i> Général</div>
    <a href="{{ route('dashboard.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
      <i class="fas fa-chart-pie"></i> Vue d'ensemble
    </a>
    <a href="{{ route('dashboard.profile.show') }}" class="sidebar-link {{ request()->routeIs('dashboard.profile.*') ? 'active' : '' }}">
      <i class="fas fa-user-cog"></i> Mon Profil
    </a>

    <div class="sidebar-section-title"><i class="fas fa-map-marked-alt me-1"></i> Territoires du Cameroun</div>
    <a href="{{ route('dashboard.pays.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.pays.*') ? 'active' : '' }}">
      <i class="fas fa-globe"></i> Cameroun & Divisions
    </a>
    <a href="{{ route('dashboard.groupements.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.groupements.*') ? 'active' : '' }}">
      <i class="fas fa-layer-group"></i> Groupements / Cantons
    </a>
    <a href="{{ route('dashboard.villages.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.villages.*') ? 'active' : '' }}">
      <i class="fas fa-tree"></i> Villages du Cameroun
    </a>

    <div class="sidebar-section-title"><i class="fas fa-users me-1"></i> Acteurs & Patrimoine</div>
    <a href="{{ route('dashboard.activites.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.activites.*') ? 'active' : '' }}">
      <i class="fas fa-hiking"></i> Activités
    </a>
    <a href="{{ route('dashboard.events.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.events.*') ? 'active' : '' }}">
      <i class="fas fa-calendar-alt"></i> Événements
    </a>
    <a href="{{ route('dashboard.personnalite.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.personnalite.*') ? 'active' : '' }}">
      <i class="fas fa-user-tie"></i> Notables & Élites
    </a>
    <a href="{{ route('dashboard.personnalites_administratives.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.personnalites_administratives.*') ? 'active' : '' }}">
      <i class="fas fa-user-shield"></i> Autorités Admin
    </a>
    <a href="{{ route('dashboard.professional.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.professional.*') ? 'active' : '' }}">
      <i class="fas fa-briefcase"></i> Artisans & Pros
    </a>

    <div class="sidebar-section-title"><i class="fas fa-bullhorn me-1"></i> Marketing</div>
    <a href="{{ route('dashboard.advertisements.index') }}" class="sidebar-link {{ request()->routeIs('dashboard.advertisements.*') ? 'active' : '' }}">
      <i class="fas fa-ad"></i> Régie Publicitaire
    </a>

    <div class="sidebar-section-title"><i class="fas fa-lock me-1"></i> Session</div>
    <form action="{{ route('logout') }}" method="POST" class="mt-2">
      @csrf
      <button type="submit" class="sidebar-link text-danger border-0 bg-transparent w-100 text-start">
        <i class="fas fa-power-off"></i> Déconnexion
      </button>
    </form>
  </aside>

  <!-- Main Content -->
  <main class="admin-main">
    <!-- Success & Error Alert Messages -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @yield('content')
  </main>

  <!-- Floating Back-to-Top Button for Admin -->
  <button id="adminBtnBackToTop" class="btn btn-success rounded-circle shadow-lg position-fixed bottom-0 end-0 m-4 p-0 d-none align-items-center justify-content-center" style="z-index: 1080; width: 46px; height: 46px; transition: all 0.3s ease;" title="Haut de page">
    <i class="fas fa-arrow-up fs-6"></i>
  </button>

  <!-- Scripts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    // Dark/Light Theme for Admin
    (function() {
      const savedTheme = localStorage.getItem('almanac_theme') || 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
      updateAdminThemeIcon(savedTheme);

      document.getElementById('adminThemeToggleBtn')?.addEventListener('click', function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('almanac_theme', newTheme);
        updateAdminThemeIcon(newTheme);
      });

      function updateAdminThemeIcon(theme) {
        const icon = document.getElementById('adminThemeIcon');
        if (icon) {
          icon.className = theme === 'dark' ? 'fas fa-sun text-warning' : 'fas fa-moon text-dark';
        }
      }
    })();

    // Mobile Sidebar Toggle
    document.getElementById('sidebarToggleBtn')?.addEventListener('click', function() {
      document.getElementById('adminSidebar')?.classList.toggle('show');
    });

    // Floating Back to Top Button on Scroll
    window.addEventListener('scroll', function () {
      const btn = document.getElementById('adminBtnBackToTop');
      if (btn) {
        if (window.scrollY > 300) {
          btn.classList.remove('d-none');
          btn.classList.add('d-flex');
        } else {
          btn.classList.remove('d-flex');
          btn.classList.add('d-none');
        }
      }
    });

    document.getElementById('adminBtnBackToTop')?.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  </script>

  @yield('scripts')
</body>
</html>
