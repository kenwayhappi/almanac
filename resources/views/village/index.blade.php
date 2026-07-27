@extends('layouts.app')

@section('title', 'Village '.$village->name.' - Almanac')

@section('content')
<div class="container py-4">
  <!-- Village Hero Banner -->
  @php
    $vBanner = $village->village_image ? (Str::startsWith($village->village_image, ['http://', 'https://']) ? $village->village_image : Storage::url($village->village_image)) : asset('images/logofinal.png');
    
    if ($village->is_village) {
        $cPhoto = $village->chief_image ? (Str::startsWith($village->chief_image, ['http://', 'https://']) ? $village->chief_image : Storage::url($village->chief_image)) : null;
        $cName = $village->chef_village ?? $village->current_chief ?? 'Chef de Village';
    } else {
        $groupement = $village->villageGroup;
        $cPhoto = ($groupement && $groupement->chef_image) ? (Str::startsWith($groupement->chef_image, ['http://', 'https://']) ? $groupement->chef_image : Storage::url($groupement->chef_image)) : null;
        $cName = ($groupement && $groupement->chef_groupement) ? $groupement->chef_groupement : 'Chef de Groupement';
    }
  @endphp
  <div class="custom-card p-4 p-md-5 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(22, 163, 74, 0.88)), url('{{ $vBanner }}') center/cover; color:#fff; border-radius:24px;">
    <div class="row align-items-center g-4 position-relative" style="z-index:2;">
      <div class="col-lg-8">
        @if($village->villageGroup)
          <a href="{{ route('groupement.show', $village->villageGroup->id . '-' . Str::slug($village->villageGroup->name)) }}" class="badge bg-warning text-dark font-bold px-3 py-2 rounded-pill mb-3 text-decoration-none" title="Visiter la fiche du Canton {{ $village->villageGroup->name }}">
            <i class="fas fa-layer-group me-1"></i> Canton {{ $village->villageGroup->name }}
          </a>
        @else
          <span class="badge bg-warning text-dark font-bold px-3 py-2 rounded-pill mb-3">
            <i class="fas fa-layer-group me-1"></i> Groupement
          </span>
        @endif
        <h1 class="font-serif fw-bold display-4 text-white mb-2">{{ $village->name }}</h1>
        <p class="lead opacity-90 fs-6 mb-4">
          {{ Str::limit($village->description ?? $village->histoire ?? 'Village traditionnel riche en histoire, coutumes et chefferie.', 180) }}
        </p>

        <div class="d-flex flex-wrap gap-3 align-items-center">
          <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-bold fs-6">
            <i class="fas fa-users me-1 text-success"></i> {{ number_format($village->population ?? 0) }} Habitants
          </span>
          @if($cName)
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-bold fs-6">
              <i class="fas fa-user-shield me-1 text-warning"></i> Chef: {{ $cName }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Interactive Unified Sub-Navigation Tabs -->
  <div class="custom-card p-2 mb-4">
    <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2" id="villageTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold active py-3 w-100" id="tab-overview" data-bs-toggle="pill" data-bs-target="#content-overview" type="button" role="tab">
          <i class="fas fa-home me-2 text-success"></i> Vue d'ensemble
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold py-3 w-100" id="tab-decouvrir" data-bs-toggle="pill" data-bs-target="#content-decouvrir" type="button" role="tab">
          <i class="fas fa-compass me-2 text-success"></i> Découvrir ({{ $village->activities->count() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold py-3 w-100" id="tab-personalities" data-bs-toggle="pill" data-bs-target="#content-personalities" type="button" role="tab">
          <i class="fas fa-user-tie me-2 text-warning"></i> Personnalités ({{ $village->personalities->count() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold py-3 w-100" id="tab-pros" data-bs-toggle="pill" data-bs-target="#content-pros" type="button" role="tab">
          <i class="fas fa-briefcase me-2 text-info"></i> Artisans & Pros ({{ $village->professionals->count() }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold py-3 w-100" id="tab-history" data-bs-toggle="pill" data-bs-target="#content-history" type="button" role="tab">
          <i class="fas fa-book-open me-2 text-primary"></i> Histoire & Événements
        </button>
      </li>
    </ul>
  </div>

  <!-- Tab Content Sections -->
  <div class="tab-content" id="villageTabContent">

    <!-- 1. VUE D'ENSEMBLE -->
    <div class="tab-pane fade show active" id="content-overview" role="tabpanel">
      <div class="row g-4">
        <!-- Chief Profile -->
        <div class="col-lg-5 col-xl-4">
          <div class="custom-card p-4 h-100">
            <h4 class="font-serif fw-bold mb-3"><i class="fas fa-crown text-warning me-2"></i> Chefferie Traditionnelle</h4>
            <hr class="opacity-10 mb-4">

            <div class="text-center mb-4">
              <div class="rounded-circle overflow-hidden mx-auto mb-3 shadow" style="width: 150px; height: 150px; border: 4px solid var(--accent-green);">
                @if($cPhoto)
                  <img src="{{ $cPhoto }}" alt="Chef" class="w-100 h-100" style="object-fit: cover;">
                @else
                  <div class="w-100 h-100 bg-secondary bg-opacity-20 d-flex align-items-center justify-content-center text-muted">
                    <i class="fas fa-user-shield fs-1"></i>
                  </div>
                @endif
              </div>
              <h5 class="fw-bold font-serif mb-1">{{ $cName }}</h5>
              <span class="badge bg-warning bg-opacity-10 text-dark fw-bold px-3 py-1">Autorité Traditionnelle</span>
            </div>

            @if($village->chief_description)
              <div class="mb-3">
                <h6 class="fw-bold small text-uppercase text-muted">Présentation</h6>
                <p class="small text-muted mb-0">{{ $village->chief_description }}</p>
              </div>
            @endif

            @if($village->chief_achievements)
              <div class="mb-3">
                <h6 class="fw-bold small text-uppercase text-muted">Réalisations Majeures</h6>
                <p class="small text-muted mb-0">{{ $village->chief_achievements }}</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Presentation -->
        <div class="col-lg-7 col-xl-8">
          <div class="custom-card p-4 p-md-5 h-100">
            <h3 class="font-serif fw-bold mb-3"><i class="fas fa-scroll text-success me-2"></i> Présentation du Village</h3>
            <hr class="opacity-10 mb-4">

            <div class="fs-6 text-main mb-4">
              {!! nl2br(e($village->description ?? 'Ce village est une localité majeure riche en traditions, coutumes et savoir-faire.')) !!}
            </div>

            @if($village->villageGroup)
              <div class="p-4 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-20 mt-4">
                <h5 class="fw-bold font-serif text-success mb-2"><i class="fas fa-layer-group me-2"></i> Rattachement Cantonal</h5>
                <p class="small text-main mb-3">Rattaché au <a href="{{ route('groupement.show', $village->villageGroup->id . '-' . Str::slug($village->villageGroup->name)) }}" class="fw-bold text-success text-decoration-underline">{{ $village->villageGroup->name }}</a> ({{ $village->villageGroup->chef_groupement ?? 'Chef de Canton' }}).</p>
                <a href="{{ route('groupement.show', $village->villageGroup->id . '-' . Str::slug($village->villageGroup->name)) }}" class="btn btn-sm btn-outline-success rounded-pill px-4 py-2 fw-bold">
                  Voir le Canton {{ $village->villageGroup->name }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- 2. DÉCOUVRIR (Activités & Initiatives) -->
    <div class="tab-pane fade" id="content-decouvrir" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <h3 class="font-serif fw-bold mb-3"><i class="fas fa-compass text-success me-2"></i> Activités, Artisanats & Attraits</h3>
        <p class="text-muted small mb-4">Découvrez les activités économiques, culturelles et touristiques du village {{ $village->name }}.</p>

        @if($village->activities->isEmpty())
          <div class="text-center py-5">
            <i class="fas fa-hiking fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucune activité enregistrée</h5>
            <p class="text-muted small">Les activités culturelles et économiques de ce village seront bientôt répertoriées.</p>
          </div>
        @else
          <div class="row g-4">
            @foreach($village->activities as $act)
              @php $aImg = $act->image ? (Str::startsWith($act->image, ['http://', 'https://']) ? $act->image : Storage::url($act->image)) : null; @endphp
              <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm custom-card overflow-hidden">
                  @if($aImg)
                    <img src="{{ $aImg }}" alt="{{ $act->name }}" style="height: 180px; object-fit: cover;" class="w-100">
                  @else
                    <div class="bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="height:180px;">
                      <i class="fas fa-hiking fs-1"></i>
                    </div>
                  @endif
                  <div class="card-body p-4">
                    <span class="badge bg-info bg-opacity-10 text-info fw-bold mb-2">{{ $act->type }}</span>
                    <h5 class="fw-bold font-serif mb-2">{{ $act->name }}</h5>
                    <p class="text-muted small mb-0">{{ Str::limit($act->description ?? '-', 100) }}</p>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- 3. PERSONNALITÉS & ÉLITES -->
    <div class="tab-pane fade" id="content-personalities" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <h3 class="font-serif fw-bold mb-3"><i class="fas fa-user-tie text-warning me-2"></i> Notables & Figures Marquantes</h3>
        <p class="text-muted small mb-4">Portraits des personnalités, élites et grands noms issus du village {{ $village->name }}.</p>

        @if($village->personalities->isEmpty())
          <div class="text-center py-5">
            <i class="fas fa-user-tie fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucune personnalité répertoriée</h5>
            <p class="text-muted small">Le répertoire des notables et élites de ce village est en cours de constitution.</p>
          </div>
        @else
          <div class="row g-4">
            @foreach($village->personalities as $p)
              @php $pImg = $p->image ? (Str::startsWith($p->image, ['http://', 'https://']) ? $p->image : Storage::url($p->image)) : null; @endphp
              <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm custom-card text-center p-4">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3 shadow" style="width: 100px; height: 100px;">
                    @if($pImg)
                      <img src="{{ $pImg }}" alt="{{ $p->name }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                      <div class="w-100 h-100 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fs-2"></i>
                      </div>
                    @endif
                  </div>
                  <h5 class="fw-bold font-serif mb-1">{{ $p->name }}</h5>
                  <span class="badge bg-warning bg-opacity-10 text-dark fw-bold mb-3 mx-auto px-3 py-1">{{ $p->statut ?? 'Notable' }}</span>
                  <p class="text-muted small mb-2">{{ Str::limit($p->description ?? 'Figure marquante du village.', 110) }}</p>
                  @if($p->contact)
                    <small class="text-success fw-semibold"><i class="fas fa-phone me-1"></i> {{ $p->contact }}</small>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- 4. ARTISANS & PROS -->
    <div class="tab-pane fade" id="content-pros" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <h3 class="font-serif fw-bold mb-3"><i class="fas fa-briefcase text-info me-2"></i> Artisans & Professionnels Locaux</h3>
        <p class="text-muted small mb-4">Répertoire des métiers, ateliers et services disponibles dans le village.</p>

        @if($village->professionals->isEmpty())
          <div class="text-center py-5">
            <i class="fas fa-briefcase fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucun artisan enregistré</h5>
            <p class="text-muted small">Les artisans et professionnels du village seront prochainement ajoutés.</p>
          </div>
        @else
          <div class="row g-4">
            @foreach($village->professionals as $pro)
              @php $proImg = $pro->image ? (Str::startsWith($pro->image, ['http://', 'https://']) ? $pro->image : Storage::url($pro->image)) : null; @endphp
              <div class="col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm custom-card p-4">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 overflow-hidden bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink:0;">
                      @if($proImg)
                        <img src="{{ $proImg }}" alt="{{ $pro->name }}" class="w-100 h-100" style="object-fit: cover;">
                      @else
                        <i class="fas fa-tools fs-4"></i>
                      @endif
                    </div>
                    <div>
                      <h5 class="fw-bold font-serif mb-0">{{ $pro->name }}</h5>
                      <span class="badge bg-info bg-opacity-10 text-info fw-bold">{{ $pro->profession ?? 'Artisan' }}</span>
                    </div>
                  </div>
                  <p class="text-muted small mb-3">{{ Str::limit($pro->description ?? 'Service professionnel local.', 100) }}</p>
                  @if($pro->contact)
                    <div class="pt-2 border-top border-secondary border-opacity-10 small text-success font-monospace fw-bold">
                      <i class="fas fa-phone me-1"></i> {{ $pro->contact }}
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    <!-- 5. HISTOIRE & ÉVÉNEMENTS -->
    <div class="tab-pane fade" id="content-history" role="tabpanel">
      <div class="custom-card p-4 p-md-5 mb-4">
        <h3 class="font-serif fw-bold mb-3"><i class="fas fa-history text-primary me-2"></i> Histoire, Origines & Dynasties</h3>
        <hr class="opacity-10 mb-4">

        <div class="fs-6 text-main mb-4">
          {!! nl2br(e($village->histoire ?? $village->village_history ?? $village->description ?? 'L\'histoire séculaire de ce village s\'inscrit au cœur des traditions de son canton.')) !!}
        </div>

        @if($village->historical_dynasty)
          <div class="p-4 bg-primary bg-opacity-10 rounded-3 border border-primary border-opacity-20 mt-4">
            <h5 class="fw-bold font-serif text-primary mb-2"><i class="fas fa-sitemap me-2"></i> Dynastie & Généalogie Royale</h5>
            <p class="small text-main mb-0">{{ $village->historical_dynasty }}</p>
          </div>
        @endif
      </div>

      <!-- Événements du village -->
      <div class="custom-card p-4 p-md-5">
        <h4 class="font-serif fw-bold mb-3"><i class="fas fa-calendar-alt text-success me-2"></i> Événements & Festivals du Village</h4>

        @if($village->events->isEmpty())
          <p class="text-muted small mb-0">Aucun festival ou événement programmé pour le moment.</p>
        @else
          <div class="row g-4">
            @foreach($village->events as $ev)
              @php $evImg = $ev->image ? (Str::startsWith($ev->image, ['http://', 'https://']) ? $ev->image : Storage::url($ev->image)) : null; @endphp
              <div class="col-md-6">
                <div class="card border-0 custom-card p-3 d-flex flex-row align-items-center gap-3">
                  <div class="rounded-3 overflow-hidden bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; flex-shrink: 0;">
                    @if($evImg)
                      <img src="{{ $evImg }}" alt="{{ $ev->name }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                      <i class="fas fa-calendar-day fs-3"></i>
                    @endif
                  </div>
                  <div>
                    <span class="badge bg-warning bg-opacity-10 text-dark fw-bold mb-1">{{ $ev->type }}</span>
                    <h6 class="fw-bold font-serif mb-1">{{ $ev->name }}</h6>
                    <small class="text-muted d-block">{{ $ev->start_date ? \Carbon\Carbon::parse($ev->start_date)->format('d/m/Y') : '-' }}</small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

  </div>
</div>
@endsection
