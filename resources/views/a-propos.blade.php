@extends('layouts.app')

@section('title', 'À Propos - Almanac')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center text-center mb-5">
    <div class="col-lg-8">
      <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-3">
        <i class="fas fa-info-circle me-1"></i> Notre Mission
      </span>
      <h1 class="font-serif fw-bold display-5 mb-3">À Propos d'Almanac</h1>
      <p class="text-muted lead fs-6">
        Almanac est l'encyclopédie numérique et l'atlas vivant dédié au recensement, à la préservation et à la valorisation du patrimoine des villages, cantons et chefferies d'Afrique.
      </p>
    </div>
  </div>

  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="custom-card p-4 text-center h-100">
        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 mx-auto mb-3" style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-landmark fs-3"></i>
        </div>
        <h5 class="font-serif fw-bold mb-2">Preservation Culturelle</h5>
        <p class="small text-muted mb-0">
          Conserver l'histoire orale, les dynasties royales, les coutumes et la géographie des chefferies traditionnelles.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="custom-card p-4 text-center h-100">
        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3 mx-auto mb-3" style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-bullhorn fs-3"></i>
        </div>
        <h5 class="font-serif fw-bold mb-2">Visibilité & Partenariats</h5>
        <p class="small text-muted mb-0">
          Offrir des services de publicité ciblée et valoriser les artisans, professionnels et entreprises locales.
        </p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="custom-card p-4 text-center h-100">
        <div class="rounded-circle bg-info bg-opacity-10 text-info p-3 mx-auto mb-3" style="width:64px;height:64px;display:flex;align-items:center;justify-content:center;">
          <i class="fas fa-users fs-3"></i>
        </div>
        <h5 class="font-serif fw-bold mb-2">Réseau Communautaire</h5>
        <p class="small text-muted mb-0">
          Connecter les ressortissants de la diaspora avec leurs villages d'origine et suivre les projets de développement.
        </p>
      </div>
    </div>
  </div>

  <div class="custom-card p-5 text-center my-4" style="background: linear-gradient(135deg, rgba(22, 163, 74, 0.1), rgba(15, 23, 42, 0.05)); border-color: rgba(22, 163, 74, 0.3);">
    <h3 class="font-serif fw-bold mb-3">Rejoignez l'Aventure Almanac</h3>
    <p class="text-muted max-w-xl mx-auto mb-4">
      Vous souhaitez enregistrer votre village ou devenir partenaire commercial ? Contactez-nous dès aujourd'hui.
    </p>
    <a href="{{ route('contact') }}" class="btn btn-accent px-5 py-3 rounded-pill fw-bold">
      <i class="fas fa-envelope me-2"></i> Nous Contacter
    </a>
  </div>
</div>
@endsection
