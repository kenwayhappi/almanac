@extends('layouts.dashboard')

@section('title', 'Mon Profil Administrateur - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-user-circle text-success me-2"></i> Mon Profil Administrateur</h2>
    <p class="text-muted small mb-0">Gestion de vos identifiants de connexion et de votre mot de passe d'accès.</p>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-4">
    <div class="admin-card text-center h-100">
      <div class="rounded-circle overflow-hidden mx-auto mb-3 shadow bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 120px; height: 120px; border: 4px solid var(--accent-green);">
        <i class="fas fa-user-shield fs-1"></i>
      </div>
      <h5 class="fw-bold font-serif mb-1">{{ $user->name }}</h5>
      <p class="text-muted small mb-3">{{ $user->email }}</p>
      <span class="badge bg-success bg-opacity-20 text-success fw-bold px-3 py-2 rounded-pill">Administrateur Principal</span>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="admin-card">
      <h5 class="fw-bold font-serif mb-3"><i class="fas fa-cog text-primary me-2"></i> Modifier mes Informations</h5>
      <hr class="opacity-10 mb-4">

      <form action="{{ route('dashboard.profile.update') }}" method="POST">
        @csrf

        <div class="row g-4">
          <div class="col-md-6">
            <label for="name" class="form-label small fw-bold text-uppercase text-muted">Nom Complet <span class="text-danger">*</span></label>
            <input type="text" class="form-control py-2" id="name" name="name" value="{{ old('name', $user->name) }}" required>
          </div>

          <div class="col-md-6">
            <label for="email" class="form-label small fw-bold text-uppercase text-muted">Adresse Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control py-2" id="email" name="email" value="{{ old('email', $user->email) }}" required>
          </div>

          <div class="col-12"><hr class="opacity-10 my-2"></div>

          <div class="col-md-6">
            <label for="password" class="form-label small fw-bold text-uppercase text-muted">Nouveau Mot de Passe</label>
            <input type="password" class="form-control py-2" id="password" name="password" placeholder="Laisser vide pour ne pas changer">
          </div>

          <div class="col-md-6">
            <label for="password_confirmation" class="form-label small fw-bold text-uppercase text-muted">Confirmer le Nouveau Mot de Passe</label>
            <input type="password" class="form-control py-2" id="password_confirmation" name="password_confirmation" placeholder="Confirmer le mot de passe">
          </div>
        </div>

        <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top border-secondary border-opacity-10">
          <button type="submit" class="btn btn-success rounded-pill px-5">
            <i class="fas fa-save me-1"></i> Enregistrer les Modifications
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
