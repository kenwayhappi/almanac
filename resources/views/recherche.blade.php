@extends('layouts.app')

@section('title', 'Moteur de Recherche - Almanac')

@section('content')
<div class="container py-4">
  <!-- Search Hero Header -->
  <div class="custom-card p-4 p-md-5 mb-4 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(22, 163, 74, 0.08), rgba(15, 23, 42, 0.03)); border-color: rgba(22, 163, 74, 0.2);">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold mb-3">
          <i class="fas fa-search me-1"></i> Explorer l'Almanac
        </span>
        <h1 class="font-serif fw-bold display-5 mb-3">Rechercher un Village ou un Groupement</h1>
        <p class="text-muted lead fs-6">
          Retrouvez facilement les informations démographiques, historiques et culturelles des villages et groupements du Cameroun.
        </p>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- Filter Sidebar (Desktop & Mobile) -->
    <div class="col-lg-4 col-xl-3">
      <div class="custom-card p-4 sticky-lg-top" style="top: 90px; z-index: 10;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold mb-0"><i class="fas fa-filter text-success me-2"></i> Filtres</h5>
          <a href="{{ route('recherche') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 text-decoration-none" title="Réinitialiser tous les filtres">
            <i class="fas fa-undo me-1"></i> Effacer
          </a>
        </div>

        <form action="{{ route('recherche') }}" method="GET" id="searchFilterForm">
          <!-- Type selector (Compact & Elegant on Desktop) -->
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Type de recherche</label>
            <input type="hidden" name="searchType" id="searchTypeInput" value="{{ $searchType ?? 'villages' }}">
            <div class="btn-group btn-group-sm w-100" role="group">
              <button type="button" class="btn btn-sm btn-outline-success rounded-start-pill py-1 px-3 fw-bold {{ ($searchType ?? 'villages') === 'villages' ? 'active' : '' }}" id="tabBtnVillages" onclick="switchSearchTab('villages')">
                <i class="fas fa-tree me-1"></i> Villages
              </button>
              <button type="button" class="btn btn-sm btn-outline-success rounded-end-pill py-1 px-3 fw-bold {{ ($searchType ?? '') === 'groupements' ? 'active' : '' }}" id="tabBtnGroupements" onclick="switchSearchTab('groupements')">
                <i class="fas fa-layer-group me-1"></i> Groupements
              </button>
            </div>
          </div>

          <!-- Keyword Search -->
          <div class="mb-3">
            <label for="nameInput" class="form-label small fw-bold text-uppercase text-muted">Nom / Mot-clé</label>
            <div class="input-group">
              <span class="input-group-text bg-body border-end-0"><i class="fas fa-search text-muted"></i></span>
              <input type="text" class="form-control border-start-0" id="nameInput" name="name" value="{{ request('name') ?? request('search') }}" placeholder="Ex: Yaoundé, Bandjoun...">
            </div>
          </div>

          <!-- Country Filter (Cascade Trigger) -->
          <div class="mb-3">
            <label for="paysSelect" class="form-label small fw-bold text-uppercase text-muted">Pays</label>
            <select class="form-select" id="paysSelect" name="pays">
              <option value="">Tous les pays</option>
              @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ request('pays') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Division 1: Région (AJAX Cascade) -->
          <div class="mb-3" id="regionContainer" style="{{ request('pays') ? 'display:block;' : 'display:none;' }}">
            <label for="division1Select" class="form-label small fw-bold text-uppercase text-muted">Région</label>
            <select class="form-select" id="division1Select" name="division1">
              <option value="">Toutes les régions</option>
            </select>
          </div>

          <!-- Division 2: Département (AJAX Cascade) -->
          <div class="mb-3" id="departementContainer" style="display:none;">
            <label for="division2Select" class="form-label small fw-bold text-uppercase text-muted">Département</label>
            <select class="form-select" id="division2Select" name="division2">
              <option value="">Tous les départements</option>
            </select>
          </div>

          <!-- Division 3: Arrondissement (AJAX Cascade) -->
          <div class="mb-3" id="arrondissementContainer" style="display:none;">
            <label for="arrondissementSelect" class="form-label small fw-bold text-uppercase text-muted">Arrondissement</label>
            <select class="form-select" id="arrondissementSelect" name="arrondissement">
              <option value="">Tous les arrondissements</option>
            </select>
          </div>

          <!-- Groupement de Rattachement Filter -->
          <div class="mb-4">
            <label for="groupementSelect" class="form-label small fw-bold text-uppercase text-muted">Groupement de Rattachement</label>
            <select class="form-select" id="groupementSelect" name="division3">
              <option value="">Tous les groupements</option>
              @if(isset($allGroupements))
                @foreach($allGroupements as $g)
                  <option value="{{ $g->id }}" {{ request('division3') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
              @endif
            </select>
          </div>

          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-accent py-2">
              <i class="fas fa-search me-1"></i> Rechercher
            </button>
            <a href="{{ route('recherche') }}" class="btn btn-outline-secondary rounded-pill py-2 text-center">
              <i class="fas fa-rotate-left me-1"></i> Réinitialiser
            </a>
          </div>
        </form>
      </div>

      <!-- Sponsor Ads Sidebar -->
      @if(isset($initialAds) && $initialAds->count() > 0)
      <div class="custom-card p-3 mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <span class="small fw-bold text-uppercase text-success"><i class="fas fa-ad me-1"></i> Partenaires</span>
          <span class="badge bg-secondary bg-opacity-10 text-secondary">Sponsorisé</span>
        </div>
        <div class="owl-carousel owl-theme ad-sidebar-carousel">
          @foreach($initialAds as $ad)
            <div class="item text-center cursor-pointer" onclick="viewAdvertisement({{ $ad->id }}, '{{ addslashes($ad->title ?? 'Annonce') }}', '{{ $ad->type }}', '{{ $ad->file_url }}', '{{ addslashes($ad->content ?? '') }}')">
              <div class="position-relative overflow-hidden rounded-3 mb-2" style="max-height:180px; background:#000;">
                @if($ad->type === 'photo')
                  <img src="{{ $ad->file_url }}" alt="{{ $ad->title }}" class="img-fluid w-100" style="height:180px; object-fit:cover;" onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'d-flex flex-column align-items-center justify-content-center h-100 py-5 bg-secondary bg-opacity-20 text-muted\'><i class=\'fas fa-image fs-2 text-warning mb-1\'></i><span class=\'small\'>Annonce Photo</span></div>';">
                @elseif($ad->type === 'video')
                  <div class="d-flex align-items-center justify-content-center h-100 py-5 bg-dark text-white">
                    <i class="fas fa-play-circle fs-1 text-success"></i>
                  </div>
                @else
                  <div class="p-4 bg-success bg-opacity-10 text-start">
                    <i class="fas fa-bullhorn fs-3 text-success mb-2"></i>
                    <p class="small text-truncate mb-0">{{ $ad->content }}</p>
                  </div>
                @endif
              </div>
              <h6 class="fw-bold small text-truncate mb-1">{{ $ad->title ?? 'Voir l\'annonce' }}</h6>
              <small class="text-success fw-semibold"><i class="fas fa-external-link-alt me-1"></i> Cliquer pour consulter</small>
            </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    <!-- Results Area (Dynamic Tab Content) -->
    <div class="col-lg-8 col-xl-9">

      <!-- Prominent Sponsored Banner for Mobile & Desktop -->
      @if(isset($initialAds) && $initialAds->count() > 0)
      <div class="custom-card p-3 mb-4 border border-warning border-opacity-20 shadow-sm" style="background: linear-gradient(135deg, rgba(254, 240, 138, 0.15), rgba(22, 163, 74, 0.05));">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill"><i class="fas fa-star me-1"></i> Espace Sponsorisé</span>
          <small class="text-muted"><i class="fas fa-info-circle me-1"></i> Cliquez pour consulter l'annonce</small>
        </div>
        <div class="owl-carousel owl-theme ad-top-carousel">
          @foreach($initialAds as $ad)
            <div class="item text-center cursor-pointer p-2" onclick="viewAdvertisement({{ $ad->id }}, '{{ addslashes($ad->title ?? 'Annonce') }}', '{{ $ad->type }}', '{{ $ad->file_url }}', '{{ addslashes($ad->content ?? '') }}')">
              <div class="position-relative overflow-hidden rounded-3 mb-2 shadow-sm" style="height: 200px; background:#000;">
                @if($ad->type === 'photo')
                  <img src="{{ $ad->file_url }}" alt="{{ $ad->title }}" class="w-100 h-100" style="object-fit: cover;" onerror="this.onerror=null; this.parentNode.innerHTML='<div class=\'d-flex flex-column align-items-center justify-content-center h-100 bg-secondary bg-opacity-20 text-muted\'><i class=\'fas fa-image fs-1 mb-1 text-warning\'></i><span class=\'small fw-bold\'>Annonce Photo</span></div>';">
                @elseif($ad->type === 'video')
                  <div class="d-flex align-items-center justify-content-center h-100 bg-dark text-white">
                    <i class="fas fa-play-circle fs-1 text-success"></i>
                  </div>
                @elseif($ad->type === 'pdf')
                  <div class="d-flex flex-column align-items-center justify-content-center h-100 bg-danger bg-opacity-10 text-danger p-3">
                    <i class="fas fa-file-pdf fs-1 mb-2"></i>
                    <span class="small fw-bold">Document PDF Sponsorisé</span>
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
              <h6 class="fw-bold font-serif text-truncate mb-1">{{ $ad->title ?? 'Voir l\'offre sponsorisée' }}</h6>
              <small class="text-success fw-semibold"><i class="fas fa-external-link-alt me-1"></i> Ouvrir l'annonce</small>
            </div>
          @endforeach
        </div>
      </div>
      @endif

      <!-- Villages Section -->
      <div id="villagesTabSection" style="{{ ($searchType ?? 'villages') === 'villages' ? 'display:block;' : 'display:none;' }}">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="fw-bold mb-1">Villages ({{ method_exists($villages, 'total') ? $villages->total() : $villages->count() }})</h4>
            <p class="text-muted small mb-0">Répertoire des localités et chefferies traditionnelles.</p>
          </div>
        </div>

        @if($villages->count() > 0)
          <div class="row g-4">
            @foreach($villages as $v)
              <div class="col-md-6 col-xl-4">
                <div class="custom-card h-100 d-flex flex-column overflow-hidden">
                  <div class="position-relative" style="height: 180px; background-color: #1e293b;">
                    @if($v->village_image)
                      <img src="{{ Storage::url($v->village_image) }}" alt="{{ $v->name }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                      <div class="d-flex flex-column align-items-center justify-content-center h-100 text-white opacity-50">
                        <i class="fas fa-tree fs-1 mb-2"></i>
                        <span class="small">Almanac</span>
                      </div>
                    @endif
                    <span class="position-absolute top-0 end-0 m-3 badge bg-dark bg-opacity-75 backdrop-blur rounded-pill">
                      <i class="fas fa-users me-1 text-warning"></i> {{ number_format($v->population ?? 0) }} hab.
                    </span>
                  </div>

                  <div class="p-4 d-flex flex-column flex-grow-1">
                    <h5 class="fw-bold font-serif mb-2">{{ $v->name }}</h5>
                    <p class="small text-muted mb-3 flex-grow-1">
                      {{ Str::limit($v->description ?? $v->histoire ?? 'Village traditionnel riche en histoire et coutumes.', 90) }}
                    </p>

                    <div class="border-top pt-3 mt-auto">
                      <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                        <span><i class="fas fa-layer-group text-success me-1"></i> {{ $v->villageGroup->name ?? 'Groupement' }}</span>
                        @if($v->chef_village)
                          <span class="text-wrap text-end" style="word-break: break-word; max-width: 60%;" title="{{ $v->chef_village }}"><i class="fas fa-user-shield text-warning me-1"></i> {{ $v->chef_village }}</span>
                        @endif
                      </div>
                      <a href="{{ route('village.show', $v->id . '-' . Str::slug($v->name)) }}" class="btn btn-sm btn-outline-success w-100 rounded-pill font-semibold">
                        Voir la fiche complète <i class="fas fa-arrow-right ms-1"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          @if(method_exists($villages, 'links') && $villages->hasPages())
            <div class="d-flex flex-column align-items-center gap-2 mt-4">
              <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-bold">
                Page {{ $villages->currentPage() }} sur {{ $villages->lastPage() }} &bull; {{ $villages->total() }} résultat(s)
              </span>
              <div>
                {{ $villages->links() }}
              </div>
            </div>
          @endif
        @else
          <div class="custom-card p-5 text-center my-4">
            <i class="fas fa-search-location fs-1 text-muted opacity-50 mb-3"></i>
            <h4 class="fw-bold">Aucun village trouvé</h4>
            <p class="text-muted">Essayez d'ajuster votre recherche ou vos filtres.</p>
          </div>
        @endif
      </div>

      <!-- Groupements Section -->
      <div id="groupementsTabSection" style="{{ ($searchType ?? '') === 'groupements' ? 'display:block;' : 'display:none;' }}">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h4 class="fw-bold mb-1">Groupements & Cantons ({{ method_exists($groupements, 'total') ? $groupements->total() : $groupements->count() }})</h4>
            <p class="text-muted small mb-0">Répertoire des structures cantonales traditionnelles.</p>
          </div>
        </div>

        @if($groupements->count() > 0)
          <div class="row g-4">
            @foreach($groupements as $g)
              <div class="col-md-6 col-xl-4">
                <div class="custom-card h-100 p-4 d-flex flex-column">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-3 d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                      <i class="fas fa-layer-group fs-4"></i>
                    </div>
                    <div>
                      <h5 class="fw-bold font-serif mb-0">{{ $g->name }}</h5>
                      <span class="badge bg-secondary bg-opacity-10 text-secondary small">Canton / Groupement</span>
                    </div>
                  </div>

                  <p class="small text-muted mb-4 flex-grow-1">
                    {{ Str::limit($g->description ?? 'Groupement de villages traditionnel.', 110) }}
                  </p>

                  <div class="border-top pt-3 mt-auto d-flex justify-content-between align-items-center">
                    <span class="small text-muted"><i class="fas fa-home me-1 text-success"></i> {{ $g->villages_count ?? 0 }} village(s)</span>
                    <a href="{{ route('groupement.show', $g->id . '-' . Str::slug($g->name)) }}" class="btn btn-sm btn-accent rounded-pill px-3">
                      Découvrir
                    </a>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          @if(method_exists($groupements, 'links') && $groupements->hasPages())
            <div class="d-flex flex-column align-items-center gap-2 mt-4">
              <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill small fw-bold">
                Page {{ $groupements->currentPage() }} sur {{ $groupements->lastPage() }} &bull; {{ $groupements->total() }} résultat(s)
              </span>
              <div>
                {{ $groupements->links() }}
              </div>
            </div>
          @endif
        @else
          <div class="custom-card p-5 text-center my-4">
            <i class="fas fa-layer-group fs-1 text-muted opacity-50 mb-3"></i>
            <h4 class="fw-bold">Aucun groupement trouvé</h4>
            <p class="text-muted">Essayez d'ajuster votre recherche.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  // Smooth Tab Switcher (Villages / Groupements) without page refresh or jumping
  function switchSearchTab(type) {
    document.getElementById('searchTypeInput').value = type;

    if (type === 'villages') {
      $('#tabBtnVillages').addClass('active');
      $('#tabBtnGroupements').removeClass('active');
      $('#villagesTabSection').fadeIn(200);
      $('#groupementsTabSection').hide();
    } else {
      $('#tabBtnGroupements').addClass('active');
      $('#tabBtnVillages').removeClass('active');
      $('#groupementsTabSection').fadeIn(200);
      $('#villagesTabSection').hide();
    }
  }

  // Dynamic AJAX Cascading Dropdowns for Country -> Region (Division 1) -> Department (Division 2) -> Groupement
  $(document).ready(function(){
    $(".ad-top-carousel, .ad-sidebar-carousel").owlCarousel({
      items: 1,
      loop: {{ isset($initialAds) && $initialAds->count() > 1 ? 'true' : 'false' }},
      margin: 15,
      autoplay: true,
      autoplayTimeout: 4500,
      autoplayHoverPause: true,
      dots: true
    });

    const selectedCountry = $('#paysSelect').val() || 237;
    const selectedDiv1 = "{{ request('division1') }}";
    const selectedDiv2 = "{{ request('division2') }}";
    const selectedDiv3 = "{{ request('arrondissement') }}";

    // Auto-load Cameroun regions on load
    loadRegions(selectedCountry, selectedDiv1);

    $('#paysSelect').on('change', function() {
      const countryId = $(this).val();
      if (countryId) {
        loadRegions(countryId);
      } else {
        $('#regionContainer, #departementContainer, #arrondissementContainer').hide();
        $('#division1Select, #division2Select, #arrondissementSelect').empty().append('<option value="">Toutes</option>');
      }
    });

    $('#division1Select').on('change', function() {
      const parentId = $(this).val();
      if (parentId) {
        loadDepartements(parentId);
      } else {
        $('#departementContainer, #arrondissementContainer').hide();
        $('#division2Select, #arrondissementSelect').empty().append('<option value="">Tous</option>');
      }
    });

    $('#division2Select').on('change', function() {
      const parentId = $(this).val();
      if (parentId) {
        loadArrondissements(parentId);
      } else {
        $('#arrondissementContainer').hide();
        $('#arrondissementSelect').empty().append('<option value="">Tous</option>');
      }
    });

    $('#arrondissementSelect').on('change', function() {
      const divisionId = $(this).val();
      if (divisionId) {
        loadGroupements(divisionId);
      }
    });

    function loadRegions(countryId, defaultVal = null) {
      $.ajax({
        url: '/api/divisions/country/' + countryId,
        method: 'GET',
        success: function(res) {
          if (res.success && res.divisions.length > 0) {
            let options = '<option value="">Toutes les régions</option>';
            res.divisions.forEach(function(div) {
              const isSelected = defaultVal && defaultVal == div.id ? 'selected' : '';
              options += `<option value="${div.id}" ${isSelected}>${div.name}</option>`;
            });
            $('#division1Select').html(options);
            $('#regionContainer').slideDown(200);

            if (defaultVal) {
              loadDepartements(defaultVal, selectedDiv2);
            }
          } else {
            $('#regionContainer, #departementContainer, #arrondissementContainer').hide();
          }
        }
      });
    }

    function loadDepartements(parentId, defaultVal = null) {
      $.ajax({
        url: '/api/divisions/parent/' + parentId,
        method: 'GET',
        success: function(res) {
          if (res.success && res.divisions.length > 0) {
            let options = '<option value="">Tous les départements</option>';
            res.divisions.forEach(function(div) {
              const isSelected = defaultVal && defaultVal == div.id ? 'selected' : '';
              options += `<option value="${div.id}" ${isSelected}>${div.name}</option>`;
            });
            $('#division2Select').html(options);
            $('#departementContainer').slideDown(200);

            if (defaultVal) {
              loadArrondissements(defaultVal, selectedDiv3);
            }
          } else {
            $('#departementContainer, #arrondissementContainer').hide();
          }
        }
      });
    }

    function loadArrondissements(parentId, defaultVal = null) {
      $.ajax({
        url: '/api/divisions/parent/' + parentId,
        method: 'GET',
        success: function(res) {
          if (res.success && res.divisions.length > 0) {
            let options = '<option value="">Tous les arrondissements</option>';
            res.divisions.forEach(function(div) {
              const isSelected = defaultVal && defaultVal == div.id ? 'selected' : '';
              options += `<option value="${div.id}" ${isSelected}>${div.name}</option>`;
            });
            $('#arrondissementSelect').html(options);
            $('#arrondissementContainer').slideDown(200);
          } else {
            $('#arrondissementContainer').hide();
          }
        }
      });
    }

    function loadGroupements(divisionId) {
      $.ajax({
        url: '/api/divisions/groupements/' + divisionId,
        method: 'GET',
        success: function(res) {
          if (res.success && res.groupements.length > 0) {
            let options = '<option value="">Tous les groupements</option>';
            res.groupements.forEach(function(g) {
              options += `<option value="${g.id}">${g.name}</option>`;
            });
            $('#groupementSelect').html(options);
          }
        }
      });
    }
  });
</script>
@endsection
