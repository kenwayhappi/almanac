<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - Almanac</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
  <link rel="icon" type="image/png" href="{{ asset('images/logofinal.png') }}">
  <style>
    body {
      background: linear-gradient(135deg, #4caf50, #2d3748);
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 2rem;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
      max-width: 400px;
      width: 100%;
      animation: fadeIn 1s ease-in-out;
    }
    .login-container h2 {
      color: #2d3748;
      font-weight: 700;
      margin-bottom: 1.5rem;
      text-align: center;
    }
    .form-control {
      border-radius: 10px;
      padding: 0.75rem;
      border: 1px solid #ddd;
      transition: all 0.3s ease;
    }
    .form-control:focus {
      border-color: #4caf50;
      box-shadow: 0 0 10px rgba(76, 175, 80, 0.5);
    }
    .btn-login {
      background-color: #4caf50;
      color: white;
      font-weight: bold;
      border: none;
      padding: 0.75rem;
      border-radius: 10px;
      width: 100%;
      transition: background-color 0.3s ease;
    }
    .btn-login:hover {
      background-color: #45a049;
    }
    .btn-login:active {
      transform: scale(0.98);
    }
    .login-footer {
      text-align: center;
      margin-top: 1rem;
      color: #666;
    }
    .login-footer a {
      color: #4caf50;
      text-decoration: none;
      font-weight: bold;
    }
    .login-footer a:hover {
      text-decoration: underline;
    }
    .alert {
      border-radius: 10px;
    }
    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    /* Style de l'écran de chargement */
    .loader-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.95);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      transition: opacity 0.5s ease;
    }
    .loader-overlay.hidden {
      opacity: 0;
      pointer-events: none;
    }
    .loader-dots {
      display: flex;
      gap: 10px;
    }
    .loader-dot {
      width: 15px;
      height: 15px;
      background: #4caf50;
      border-radius: 50%;
      animation: blink 1.4s infinite both;
    }
    .loader-dot:nth-child(2) {
      animation-delay: 0.2s;
    }
    .loader-dot:nth-child(3) {
      animation-delay: 0.4s;
    }
    @keyframes blink {
      0%, 80%, 100% { transform: scale(0); }
      40% { transform: scale(1); }
    }
  </style>
</head>
<body>
  <!-- Écran de chargement -->
  <div class="loader-overlay" id="loader">
    <div class="loader-dots">
      <div class="loader-dot"></div>
      <div class="loader-dot"></div>
      <div class="loader-dot"></div>
    </div>
  </div>

  <div class="login-container">
    <h2>Connexion</h2>
    @if (session('error'))
      <div class="alert alert-danger">
        {{ session('error') }}
      </div>
    @endif
    <form id="loginForm" action="{{ route('login.submit') }}" method="POST">
      @csrf
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="Entrez votre email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Entrez votre mot de passe" required>
      </div>
      <button type="submit" class="btn btn-login">Se connecter</button>
    </form>
    <div class="login-footer">
      <p>Revenir à l'accueil ? <a href="{{ route('accueil') }}">Accueil</a></p>
    </div>
  </div>

  <script>
    // Gérer le chargement de la page
    window.addEventListener('load', function() {
      const loader = document.getElementById('loader');
      loader.classList.add('hidden');
      setTimeout(() => {
        loader.remove(); // Supprimer le loader du DOM
      }, 500); // Correspond à la durée de la transition
    });
  </script>
</body>
</html>
