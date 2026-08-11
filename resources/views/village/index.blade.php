@extends('layouts.app')

@section('title', 'Village '.$village->name.' - Almanac')

@section('content')
<div class="container py-4">
  <!-- Village Hero Banner -->
  @php
    use App\Helpers\CloudinaryHelper;
    $vBanner = CloudinaryHelper::url($village->village_image) ?? asset('images/logofinal.png');

    if ($village->is_village) {
        $cPhoto = CloudinaryHelper::url($village->chief_image);
        $cName = $village->chef_village ?? $village->current_chief ?? 'Chef de Village';
    } else {
        $groupement = $village->villageGroup;
        $cPhoto = ($groupement && $groupement->chef_image) ? CloudinaryHelper::url($groupement->chef_image) : null;
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
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-bold text-wrap text-start d-inline-flex align-items-center" style="white-space: normal; line-height: 1.3; font-size: 0.95rem; max-width: 100%;">
              <i class="fas fa-user-shield me-2 text-warning flex-shrink-0"></i> <span>Chef: {{ $cName }}</span>
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
              <h5 class="fw-bold font-serif mb-1 text-wrap" style="word-break: break-word;">{{ $cName }}</h5>
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
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h3 class="font-serif fw-bold mb-1"><i class="fas fa-compass text-success me-2"></i> Activités, Artisanats & Attraits</h3>
            <p class="text-muted small mb-0">Découvrez les activités économiques, culturelles et touristiques du village {{ $village->name }}.</p>
          </div>
          @if(!$village->activities->isEmpty())
          <div class="input-group" style="max-width: 280px;">
            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="searchActivite" class="form-control border-start-0 ps-0" placeholder="Rechercher une activité…" oninput="filterActivites()">
          </div>
          @endif
        </div>

        @if($village->activities->isEmpty())
          <div class="text-center py-5">
            <i class="fas fa-hiking fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucune activité enregistrée</h5>
            <p class="text-muted small">Les activités culturelles et économiques de ce village seront bientôt répertoriées.</p>
          </div>
        @else
          <div class="row g-4" id="activitesGrid">
            @foreach($village->activities as $act)
              @php $aImg = CloudinaryHelper::url($act->image); @endphp
              <div class="col-md-6 col-lg-4 activite-item" data-name="{{ strtolower($act->name) }}" data-type="{{ strtolower($act->type ?? '') }}">
                <div class="card h-100 border-0 shadow-sm custom-card overflow-hidden" style="cursor:pointer;" onclick="openActiviteModal({{ $act->id }})">
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
                  <div class="card-footer bg-transparent border-top border-secondary border-opacity-10 py-2 px-4">
                    <small class="text-success fw-semibold"><i class="fas fa-eye me-1"></i> Voir les détails</small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div id="activitesEmpty" class="text-center py-5 d-none">
            <i class="fas fa-search fs-1 text-muted opacity-50 mb-3"></i>
            <p class="text-muted">Aucune activité ne correspond à votre recherche.</p>
          </div>
        @endif
      </div>
    </div>

    <!-- 3. PERSONNALITÉS & ÉLITES -->
    <div class="tab-pane fade" id="content-personalities" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h3 class="font-serif fw-bold mb-1"><i class="fas fa-user-tie text-warning me-2"></i> Notables & Figures Marquantes</h3>
            <p class="text-muted small mb-0">Portraits des personnalités, élites et grands noms issus du village {{ $village->name }}.</p>
          </div>
          @if(!$village->personalities->isEmpty())
          <div class="d-flex gap-2 flex-wrap">
            <!-- Recherche -->
            <div class="input-group" style="max-width: 220px;">
              <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
              <input type="text" id="searchPersonnalite" class="form-control border-start-0 ps-0" placeholder="Rechercher…" oninput="filterPersonnalites()">
            </div>
            <!-- Filtre statut -->
            <select id="filterStatut" class="form-select" style="max-width: 160px;" onchange="filterPersonnalites()">
              <option value="">Tous les statuts</option>
              @foreach($village->personalities->pluck('statut')->filter()->unique()->sort() as $statut)
                <option value="{{ strtolower($statut) }}">{{ $statut }}</option>
              @endforeach
            </select>
          </div>
          @endif
        </div>

        @if($village->personalities->isEmpty())
          <div class="text-center py-5">
            <i class="fas fa-user-tie fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucune personnalité répertoriée</h5>
            <p class="text-muted small">Le répertoire des notables et élites de ce village est en cours de constitution.</p>
          </div>
        @else
          <div class="row g-4" id="personnalitesGrid">
            @foreach($village->personalities as $p)
              @php $pImg = CloudinaryHelper::url($p->image); @endphp
              <div class="col-md-6 col-lg-4 personnalite-item" data-name="{{ strtolower($p->name) }}" data-statut="{{ strtolower($p->statut ?? '') }}">
                <div class="card h-100 border-0 shadow-sm custom-card text-center p-4" style="cursor:pointer; transition: transform .18s, box-shadow .18s;" onclick="openPersonnaliteModal({{ $p->id }})" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.15)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3 shadow" style="width: 100px; height: 100px; border: 3px solid var(--accent-green);">
                    @if($pImg)
                      <img src="{{ $pImg }}" alt="{{ $p->name }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                      <div class="w-100 h-100 bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center">
                        <i class="fas fa-user fs-2"></i>
                      </div>
                    @endif
                  </div>
                  <h5 class="fw-bold font-serif mb-1">{{ $p->name }}</h5>
                  <span class="badge bg-warning bg-opacity-10 text-dark fw-bold mb-2 px-3 py-1">{{ $p->statut ?? 'Notable' }}</span>
                  <p class="text-muted small mb-2">{{ Str::limit($p->description ?? 'Figure marquante du village.', 90) }}</p>
                  @if($p->contact)
                    <small class="text-success fw-semibold"><i class="fas fa-phone me-1"></i> {{ $p->contact }}</small>
                  @endif
                  <div class="mt-3 pt-2 border-top border-secondary border-opacity-10">
                    <small class="text-warning fw-semibold"><i class="fas fa-eye me-1"></i> Voir le profil complet</small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div id="personnalitesEmpty" class="text-center py-5 d-none">
            <i class="fas fa-search fs-1 text-muted opacity-50 mb-3"></i>
            <p class="text-muted">Aucune personnalité ne correspond à votre recherche.</p>
          </div>
        @endif
      </div>
    </div>

    <!-- 4. ARTISANS & PROS -->
    <div class="tab-pane fade" id="content-pros" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h3 class="font-serif fw-bold mb-1"><i class="fas fa-briefcase text-info me-2"></i> Artisans & Professionnels Locaux</h3>
            <p class="text-muted small mb-0">Répertoire des métiers, ateliers et services disponibles dans le village.</p>
          </div>
          @if(!$village->professionals->isEmpty())
          <div class="input-group" style="max-width: 260px;">
            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="searchPro" class="form-control border-start-0 ps-0" placeholder="Nom ou profession…" oninput="filterPros()">
          </div>
          @endif
        </div>

        @if($village->professionals->isEmpty())
          <div class="text-center py-5">
            <i class="fas fa-briefcase fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucun artisan enregistré</h5>
            <p class="text-muted small">Les artisans et professionnels du village seront prochainement ajoutés.</p>
          </div>
        @else
          <div class="row g-4" id="prosGrid">
            @foreach($village->professionals as $pro)
              @php $proImg = CloudinaryHelper::url($pro->image); @endphp
              <div class="col-md-6 col-lg-4 pro-item" data-name="{{ strtolower($pro->name) }}" data-profession="{{ strtolower($pro->profession ?? '') }}">
                <div class="card h-100 border-0 shadow-sm custom-card p-4" style="cursor:pointer; transition: transform .18s, box-shadow .18s;" onclick="openProModal({{ $pro->id }})" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.15)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 overflow-hidden d-flex align-items-center justify-content-center flex-shrink-0" style="width:64px;height:64px;background:rgba(6,182,212,.08);">
                      @if($proImg)
                        <img src="{{ $proImg }}" alt="{{ $pro->name }}" class="w-100 h-100" style="object-fit: cover;">
                      @else
                        <i class="fas fa-tools fs-3 text-info"></i>
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
                  <div class="mt-2 pt-2 border-top border-secondary border-opacity-10">
                    <small class="text-info fw-semibold"><i class="fas fa-eye me-1"></i> Voir le profil complet</small>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div id="prosEmpty" class="text-center py-5 d-none">
            <i class="fas fa-search fs-1 text-muted opacity-50 mb-3"></i>
            <p class="text-muted">Aucun artisan ne correspond à votre recherche.</p>
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
              @php $evImg = CloudinaryHelper::url($ev->image); @endphp
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

<!-- ============================================================
     MODAL — Personnalité
============================================================ -->
<div class="modal fade" id="modalPersonnalite" tabindex="-1" aria-labelledby="modalPersonnaliteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
      <!-- Header avec dégradé -->
      <div class="modal-header border-0 px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #0f172a, #166534);">
        <div class="d-flex align-items-center gap-3 w-100">
          <div id="mpPhoto" class="rounded-circle overflow-hidden shadow flex-shrink-0" style="width:72px;height:72px;border:3px solid #22c55e;background:#1e293b;"></div>
          <div>
            <h5 class="modal-title fw-bold font-serif text-white mb-0" id="modalPersonnaliteLabel">—</h5>
            <span id="mpStatut" class="badge bg-warning text-dark fw-bold px-3 py-1 mt-1">—</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Corps -->
      <div class="modal-body p-4 p-md-5">
        <div class="row g-4">
          <div class="col-md-7">
            <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="fas fa-info-circle me-1"></i> Description</h6>
            <p id="mpDescription" class="text-main mb-4">—</p>
          </div>
          <div class="col-md-5">
            <div id="mpContactWrap" class="d-none">
              <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="fas fa-address-book me-1"></i> Contact</h6>
              <a id="mpContact" href="#" class="btn btn-outline-success btn-sm rounded-pill px-3"><i class="fas fa-phone me-1"></i> <span></span></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL — Artisan / Professionnel
============================================================ -->
<div class="modal fade" id="modalPro" tabindex="-1" aria-labelledby="modalProLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
      <div class="modal-header border-0 px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #0c1a2e, #0e7490);">
        <div class="d-flex align-items-center gap-3 w-100">
          <div id="proPhoto" class="rounded-3 overflow-hidden shadow flex-shrink-0 d-flex align-items-center justify-content-center" style="width:72px;height:72px;background:rgba(6,182,212,.15);"></div>
          <div>
            <h5 class="modal-title fw-bold font-serif text-white mb-0" id="modalProLabel">—</h5>
            <span id="proProfession" class="badge bg-info bg-opacity-75 text-white fw-bold px-3 py-1 mt-1">—</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-md-5">
        <div class="row g-4">
          <div class="col-md-7">
            <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="fas fa-info-circle me-1"></i> Description</h6>
            <p id="proDescription" class="text-main mb-4">—</p>
          </div>
          <div class="col-md-5">
            <div id="proContactWrap" class="d-none">
              <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="fas fa-phone me-1"></i> Contact</h6>
              <a id="proContact" href="#" class="btn btn-outline-info btn-sm rounded-pill px-3"><i class="fas fa-phone me-1"></i> <span></span></a>
            </div>
            <div id="proWhatsappWrap" class="d-none mt-3">
              <a id="proWhatsapp" href="#" target="_blank" class="btn btn-success btn-sm rounded-pill px-3"><i class="fab fa-whatsapp me-1"></i> WhatsApp</a>
            </div>
            <div id="proEmailWrap" class="d-none mt-3">
              <a id="proEmail" href="#" class="btn btn-outline-secondary btn-sm rounded-pill px-3"><i class="fas fa-envelope me-1"></i> <span></span></a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL — Activité
============================================================ -->
<div class="modal fade" id="modalActivite" tabindex="-1" aria-labelledby="modalActiviteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
      <div class="modal-header border-0 px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #052e16, #166534);">
        <div class="d-flex align-items-center gap-3 w-100">
          <div id="actPhoto" class="rounded-3 overflow-hidden shadow flex-shrink-0 d-flex align-items-center justify-content-center" style="width:72px;height:72px;background:rgba(34,197,94,.15);"></div>
          <div>
            <h5 class="modal-title fw-bold font-serif text-white mb-0" id="modalActiviteLabel">—</h5>
            <span id="actType" class="badge bg-info bg-opacity-75 text-white fw-bold px-3 py-1 mt-1">—</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 p-md-5">
        <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="fas fa-info-circle me-1"></i> Description</h6>
        <p id="actDescription" class="text-main mb-0">—</p>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     DONNÉES JSON pour les modals (injectées par Blade)
============================================================ -->
@php
  $personnalitesJson = $village->personalities->map(function($p) {
    return [
      'id'          => $p->id,
      'name'        => $p->name,
      'statut'      => $p->statut ?? 'Notable',
      'description' => $p->description ?? 'Figure marquante du village.',
      'contact'     => $p->contact,
      'image'       => \App\Helpers\CloudinaryHelper::url($p->image),
    ];
  })->values();

  $professionnelsJson = $village->professionals->map(function($p) {
    return [
      'id'          => $p->id,
      'name'        => $p->name,
      'profession'  => $p->profession ?? 'Artisan',
      'description' => $p->description ?? 'Service professionnel local.',
      'contact'     => $p->contact,
      'whatsapp'    => $p->whatsapp ?? null,
      'email'       => $p->email ?? null,
      'image'       => \App\Helpers\CloudinaryHelper::url($p->image),
    ];
  })->values();

  $activitesJson = $village->activities->map(function($a) {
    return [
      'id'          => $a->id,
      'name'        => $a->name,
      'type'        => $a->type ?? 'Activité',
      'description' => $a->description ?? '-',
      'image'       => \App\Helpers\CloudinaryHelper::url($a->image),
    ];
  })->values();
@endphp
<script>
const PERSONNALITES  = @json($personnalitesJson);
const PROFESSIONNELS = @json($professionnelsJson);
const ACTIVITES      = @json($activitesJson);

// ── Ouvrir modal Personnalité ──────────────────────────────
function openPersonnaliteModal(id) {
  const p = PERSONNALITES.find(x => x.id === id);
  if (!p) return;

  const photoEl = document.getElementById('mpPhoto');
  photoEl.innerHTML = p.image
    ? `<img src="${p.image}" class="w-100 h-100" style="object-fit:cover;" alt="${p.name}">`
    : `<div class="w-100 h-100 d-flex align-items-center justify-content-center text-warning" style="background:rgba(234,179,8,.1);"><i class="fas fa-user fs-2"></i></div>`;

  document.getElementById('modalPersonnaliteLabel').textContent = p.name;
  document.getElementById('mpStatut').textContent = p.statut;
  document.getElementById('mpDescription').textContent = p.description;

  const cWrap = document.getElementById('mpContactWrap');
  const cBtn  = document.getElementById('mpContact');
  if (p.contact) {
    cBtn.href = `tel:${p.contact}`;
    cBtn.querySelector('span').textContent = p.contact;
    cWrap.classList.remove('d-none');
  } else {
    cWrap.classList.add('d-none');
  }

  new bootstrap.Modal(document.getElementById('modalPersonnalite')).show();
}

// ── Ouvrir modal Professionnel ─────────────────────────────
function openProModal(id) {
  const p = PROFESSIONNELS.find(x => x.id === id);
  if (!p) return;

  const photoEl = document.getElementById('proPhoto');
  photoEl.innerHTML = p.image
    ? `<img src="${p.image}" class="w-100 h-100" style="object-fit:cover;" alt="${p.name}">`
    : `<i class="fas fa-tools fs-2 text-info"></i>`;

  document.getElementById('modalProLabel').textContent = p.name;
  document.getElementById('proProfession').textContent = p.profession;
  document.getElementById('proDescription').textContent = p.description;

  const cWrap = document.getElementById('proContactWrap');
  const cBtn  = document.getElementById('proContact');
  if (p.contact) {
    cBtn.href = `tel:${p.contact}`;
    cBtn.querySelector('span').textContent = p.contact;
    cWrap.classList.remove('d-none');
  } else { cWrap.classList.add('d-none'); }

  const wWrap = document.getElementById('proWhatsappWrap');
  const wBtn  = document.getElementById('proWhatsapp');
  if (p.whatsapp) {
    wBtn.href = `https://wa.me/${p.whatsapp.replace(/\D/g,'')}`;
    wWrap.classList.remove('d-none');
  } else { wWrap.classList.add('d-none'); }

  const eWrap = document.getElementById('proEmailWrap');
  const eBtn  = document.getElementById('proEmail');
  if (p.email) {
    eBtn.href = `mailto:${p.email}`;
    eBtn.querySelector('span').textContent = p.email;
    eWrap.classList.remove('d-none');
  } else { eWrap.classList.add('d-none'); }

  new bootstrap.Modal(document.getElementById('modalPro')).show();
}

// ── Ouvrir modal Activité ──────────────────────────────────
function openActiviteModal(id) {
  const a = ACTIVITES.find(x => x.id === id);
  if (!a) return;

  const photoEl = document.getElementById('actPhoto');
  photoEl.innerHTML = a.image
    ? `<img src="${a.image}" class="w-100 h-100" style="object-fit:cover;" alt="${a.name}">`
    : `<i class="fas fa-hiking fs-2 text-success"></i>`;

  document.getElementById('modalActiviteLabel').textContent = a.name;
  document.getElementById('actType').textContent = a.type;
  document.getElementById('actDescription').textContent = a.description;

  new bootstrap.Modal(document.getElementById('modalActivite')).show();
}

// ── Filtres Personnalités ─────────────────────────────────
function filterPersonnalites() {
  const q = (document.getElementById('searchPersonnalite')?.value || '').toLowerCase().trim();
  const s = (document.getElementById('filterStatut')?.value || '').toLowerCase().trim();
  const items = document.querySelectorAll('.personnalite-item');
  let count = 0;
  items.forEach(el => {
    const matchName   = el.dataset.name.includes(q);
    const matchStatut = !s || el.dataset.statut === s;
    const show = matchName && matchStatut;
    el.style.display = show ? '' : 'none';
    if (show) count++;
  });
  document.getElementById('personnalitesEmpty').classList.toggle('d-none', count > 0);
}

// ── Filtres Artisans ──────────────────────────────────────
function filterPros() {
  const q = (document.getElementById('searchPro')?.value || '').toLowerCase().trim();
  const items = document.querySelectorAll('.pro-item');
  let count = 0;
  items.forEach(el => {
    const show = el.dataset.name.includes(q) || el.dataset.profession.includes(q);
    el.style.display = show ? '' : 'none';
    if (show) count++;
  });
  document.getElementById('prosEmpty').classList.toggle('d-none', count > 0);
}

// ── Filtres Activités ─────────────────────────────────────
function filterActivites() {
  const q = (document.getElementById('searchActivite')?.value || '').toLowerCase().trim();
  const items = document.querySelectorAll('.activite-item');
  let count = 0;
  items.forEach(el => {
    const show = el.dataset.name.includes(q) || el.dataset.type.includes(q);
    el.style.display = show ? '' : 'none';
    if (show) count++;
  });
  document.getElementById('activitesEmpty').classList.toggle('d-none', count > 0);
}
</script>
@endsection
