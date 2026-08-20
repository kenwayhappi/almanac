@extends('layouts.app')

@section('title', 'Almanach - L\'Encyclopédie Numérique des Villages & Groupements')

@section('content')
<!-- Hero Section -->
<section class="pt-2 pb-4 py-md-5 position-relative overflow-hidden">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-3">
          <i class="fas fa-landmark me-1"></i> Patrimoine & Culture Africaine
        </span>
        <h1 class="font-serif fw-extrabold mb-4" style="line-height: 1.25; font-size: calc(1.35rem + 1.2vw);">
          Découvrez la Richesse Historique et Démographique de nos <span class="text-success">Villages</span>.
        </h1>
        <p class="lead text-muted mb-4 fs-5">
          Almanach rassemble l’histoire, les cantons, les chefferies traditionnelles, les personnalités inspirantes et les opportunités des localités d'Afrique.
        </p>

        <!-- Quick Search Bar -->
        <div class="custom-card p-2 mb-4 shadow-lg border-0">
          <form action="{{ route('recherche') }}" method="GET" class="d-flex flex-column flex-sm-row gap-2">
            <div class="input-group">
              <span class="input-group-text bg-transparent border-0"><i class="fas fa-search text-success fs-5"></i></span>
              <input type="text" name="name" class="form-control border-0 shadow-none bg-transparent" placeholder="Rechercher un village, canton, chefferie..." required>
            </div>
            <button type="submit" class="btn btn-accent px-4 py-3 text-nowrap rounded-pill">
              <i class="fas fa-search me-2"></i> Explorer
            </button>
          </form>
        </div>

        <!-- Quick Stats (Order: Groupement/Canton first, then Village, then Personnalité) -->
        <div class="row g-3 pt-2 text-center text-sm-start">
          <div class="col-4">
            <h3 class="fw-bold font-serif mb-0 text-success">{{ number_format($totalGroupements ?? 0) }}+</h3>
            <small class="text-muted fw-semibold">Groupements / Cantons</small>
          </div>
          <div class="col-4">
            <h3 class="fw-bold font-serif mb-0 text-success">{{ number_format($totalVillages ?? 0) }}+</h3>
            <small class="text-muted fw-semibold">Villages Répertoriés</small>
          </div>
          <div class="col-4">
            <h3 class="fw-bold font-serif mb-0 text-warning">{{ number_format($totalPersonalities ?? 0) }}+</h3>
            <small class="text-muted fw-semibold">Personnalités</small>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="position-relative">
          <div class="custom-card p-3 shadow-lg overflow-hidden position-relative" style="border-radius: 24px;">
            <img src="{{ asset('images/logofinal.png') }}" alt="Almanac Banner" class="img-fluid rounded-4 w-100" style="max-height: 420px; object-fit: contain; background: linear-gradient(135deg, #0f172a, #16a34a);">
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Advertisements Section -->
@if(isset($initialAds) && $initialAds->count() > 0)
<section class="py-5 bg-body-tertiary border-top border-bottom border-secondary border-opacity-10">
  <div class="container">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
      <div>
        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill fw-bold mb-2"><i class="fas fa-star me-1"></i> Espace Sponsorisé</span>
        <h3 class="font-serif fw-bold mb-0">Publicités & Annonces à la Une</h3>
      </div>
      <small class="text-muted mt-2 mt-md-0"><i class="fas fa-info-circle me-1"></i> Cliquez sur une annonce pour consulter la version grand format</small>
    </div>

    <div class="owl-carousel owl-theme home-ad-carousel">
      @foreach($initialAds as $ad)
        <div class="item">
          <div class="custom-card h-100 p-3 overflow-hidden cursor-pointer" onclick="viewAdvertisement({{ $ad->id }}, '{{ addslashes($ad->title ?? 'Publicité') }}', '{{ $ad->type }}', '{{ $ad->file_url }}', '{{ addslashes($ad->content ?? '') }}')">
            <div class="position-relative rounded-3 overflow-hidden mb-3" style="height: 200px; background:#000;">
              @if($ad->type === 'photo')
                <img src="{{ $ad->file_url }}" alt="{{ $ad->title }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'d-flex flex-column align-items-center justify-content-center h-100 bg-secondary bg-opacity-20 text-muted\'><i class=\'fas fa-image fs-1 mb-1 text-warning\'></i><span class=\'small fw-bold\'>Annonce Photo</span></div>';">
              @elseif($ad->type === 'video')
                <div class="d-flex align-items-center justify-content-center h-100 bg-dark text-white">
                  <i class="fas fa-play-circle fs-1 text-success"></i>
                </div>
              @else
                <div class="p-4 bg-success bg-opacity-10 text-start h-100">
                  <i class="fas fa-bullhorn fs-2 text-success mb-2"></i>
                  <p class="small text-truncate mb-0">{{ $ad->content }}</p>
                </div>
              @endif
              <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 text-uppercase fs-xs">
                {{ $ad->type }}
              </span>
            </div>
            <h6 class="fw-bold font-serif text-truncate mb-0 text-center">{{ $ad->title ?? 'Offre Sponsorisée' }}</h6>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- Groupements by Country Section -->
@if(isset($groupementsByCountry) && count($groupementsByCountry) > 0)
<section class="py-5">
  <div class="container">
    <div class="text-center max-w-2xl mx-auto mb-5">
      <h2 class="font-serif fw-bold mb-3">Groupements ou Cantons par Territoire</h2>
      <p class="text-muted">Parcourez les structures traditionnelles et les cantons organisés par pays.</p>
    </div>

    @foreach($groupementsByCountry as $countryName => $groupements)
      <div class="mb-5">
        <div class="d-flex align-items-center gap-3 mb-4">
          <h4 class="font-serif fw-bold mb-0"><i class="fas fa-flag text-success me-2"></i> {{ $countryName }}</h4>
          <hr class="flex-grow-1 opacity-10">
        </div>

        <div class="row g-4">
          @foreach($groupements as $g)
            <div class="col-md-6 col-lg-4">
              <div class="custom-card h-100 p-4 d-flex flex-column">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
                    <i class="fas fa-layer-group fs-4"></i>
                  </div>
                  <div>
                    <h5 class="fw-bold font-serif mb-0">{{ $g->name }}</h5>
                    <small class="text-muted">Canton Traditionnel</small>
                  </div>
                </div>
                <p class="small text-muted mb-4 flex-grow-1">
                  {{ Str::limit($g->description ?? 'Groupement culturel abritant plusieurs villages traditionnels.', 100) }}
                </p>
                <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                  <a href="{{ route('groupement.show', $g->id . '-' . Str::slug($g->name)) }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
                    Explorer le groupement <i class="fas fa-arrow-right ms-1"></i>
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</section>
@endif

<!-- Call to Action Banner -->
<section class="py-5">
  <div class="container">
    <div class="custom-card p-5 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #16a34a 100%); color:#fff; border:none; border-radius: 28px;">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <h2 class="font-serif fw-bold display-6 mb-3 text-white">Contribuez à l'Almanach de votre Village</h2>
          <p class="lead mb-4 opacity-90 fs-6">
            Vous souhaitez documenter l'histoire de votre localité, inscrire vos notables ou faire la promotion d'artisans locaux ?
          </p>
          <a href="{{ route('contact') }}" class="btn btn-light text-dark font-bold rounded-pill px-5 py-3 fs-6 shadow">
            <i class="fas fa-paper-plane me-2"></i> Nous Contacter
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  $(document).ready(function(){
    $(".home-ad-carousel").owlCarousel({
      items: 1,
      responsive: {
        576: { items: 2 },
        992: { items: 3 },
        1200: { items: 4 }
      },
      loop: {{ isset($initialAds) && $initialAds->count() > 1 ? 'true' : 'false' }},
      margin: 20,
      autoplay: true,
      autoplayTimeout: 4500,
      autoplayHoverPause: true,
      dots: true
    });
  });
</script>
@endsection
