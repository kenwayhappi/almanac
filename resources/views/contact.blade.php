@extends('layouts.app')

@section('title', 'Contact & Assistance - Almanac')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center text-center mb-5">
    <div class="col-lg-8">
      <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-3">
        <i class="fas fa-envelope me-1"></i> Restons en Contact
      </span>
      <h1 class="font-serif fw-bold display-5 mb-3">Nous Contacter</h1>
      <p class="text-muted lead fs-6">
        Vous souhaitez ajouter votre village, inscrire des personnalités, ou diffuser une publicité sponsorisée ? Remplissez ce formulaire et notre équipe vous recontactera rapidement.
      </p>
    </div>
  </div>

  <div class="row g-4 justify-content-center">
    <!-- Contact Info Cards -->
    <div class="col-lg-4">
      <div class="custom-card p-4 h-100 d-flex flex-column justify-content-between">
        <div>
          <h4 class="font-serif fw-bold mb-4">Nos Coordonnées</h4>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
              <i class="fas fa-map-marker-alt fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Siège Social</h6>
              <p class="small text-muted mb-0">Yaoundé, Cameroun</p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
              <i class="fas fa-phone-alt fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Téléphone & WhatsApp</h6>
              <p class="small text-muted mb-0">+237 699 99 99 99</p>
            </div>
          </div>

          <div class="d-flex align-items-start gap-3 mb-4">
            <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
              <i class="fas fa-envelope fs-4"></i>
            </div>
            <div>
              <h6 class="fw-bold mb-1">Adresse Email</h6>
              <p class="small text-muted mb-0">contact@almanac.cm</p>
            </div>
          </div>
        </div>

        <div class="border-top pt-3 mt-4">
          <small class="text-muted"><i class="fas fa-clock text-success me-1"></i> Horaires : Lundi - Vendredi (8h00 - 18h00)</small>
        </div>
      </div>
    </div>

    <!-- Contact Form Card -->
    <div class="col-lg-8">
      <div class="custom-card p-4 p-md-5">
        <h4 class="font-serif fw-bold mb-4">Envoyez-nous un Message</h4>

        <div id="formAlert" class="alert alert-success d-none mb-4" role="alert">
          <i class="fas fa-check-circle me-2"></i> Votre message a été transmis avec succès. Merci !
        </div>

        <form id="contactForm" onsubmit="event.preventDefault(); document.getElementById('formAlert').classList.remove('d-none'); this.reset();">
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="name" class="form-label small fw-bold text-uppercase text-muted">Nom complet</label>
              <input type="text" class="form-control py-2" id="name" placeholder="Votre nom" required>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label small fw-bold text-uppercase text-muted">Adresse Email</label>
              <input type="email" class="form-control py-2" id="email" placeholder="nom@domaine.com" required>
            </div>
          </div>

          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label for="phone" class="form-label small fw-bold text-uppercase text-muted">Numéro de Téléphone</label>
              <input type="tel" class="form-control py-2" id="phone" placeholder="+237 ...">
            </div>
            <div class="col-md-6">
              <label for="subject" class="form-label small fw-bold text-uppercase text-muted">Objet de la Demande</label>
              <select class="form-select py-2" id="subject" required>
                <option value="ajouter-village">Ajouter un village / groupement</option>
                <option value="publicite">Services de Publicité & Partenariat</option>
                <option value="personnalite">Inscrire une personnalité</option>
                <option value="autre">Autre demande</option>
              </select>
            </div>
          </div>

          <div class="mb-4">
            <label for="message" class="form-label small fw-bold text-uppercase text-muted">Votre Message</label>
            <textarea class="form-control" id="message" rows="5" placeholder="Décrivez votre demande en détail..." required></textarea>
          </div>

          <button type="submit" class="btn btn-accent px-5 py-3 rounded-pill fw-bold">
            <i class="fas fa-paper-plane me-2"></i> Envoyer le Message
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
