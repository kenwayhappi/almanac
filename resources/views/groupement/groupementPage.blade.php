@extends('layouts.app')

@section('title', 'Groupement '.$group['name'].' - Almanac')

@section('content')
<div class="container py-4">
  <!-- Groupement Hero Banner -->
  @php
    use App\Helpers\CloudinaryHelper;
    $gBanner  = isset($group['image']) && $group['image'] ? CloudinaryHelper::url($group['image']) : asset('images/logofinal.png');
    $chefImg  = isset($group['chef_image']) && $group['chef_image'] ? CloudinaryHelper::url($group['chef_image']) : null;
    $vList    = $group['villages'] ?? [];
    $pasList  = $group['personnalites_administratives'] ?? collect([]);
  @endphp
  <div class="custom-card p-4 p-md-5 mb-4 position-relative overflow-hidden" style="background: linear-gradient(135deg, rgba(15, 23, 42, 0.95), rgba(34, 197, 94, 0.85)), url('{{ $gBanner }}') center/cover; color:#fff; border-radius:24px;">
    <div class="row align-items-center g-4 position-relative" style="z-index:2;">
      <div class="col-lg-8">
        <span class="badge bg-warning text-dark font-bold px-3 py-2 rounded-pill mb-3">
          <i class="fas fa-layer-group me-1"></i> Canton / Groupement
        </span>
        <h1 class="font-serif fw-bold display-4 text-white mb-2">{{ $group['name'] }}</h1>
        <p class="lead opacity-90 fs-6 mb-4">
          {{ Str::limit($group['description'] ?? 'Groupement traditionnel regroupant plusieurs villages et localités d\'histoire.', 180) }}
        </p>

        <div class="d-flex flex-wrap gap-3 align-items-center">
          <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-bold fs-6">
            <i class="fas fa-home me-1 text-success"></i> {{ count($vList) }} {{ Str::plural('Village', count($vList)) }} Rattaché{{ count($vList) > 1 ? 's' : '' }}
          </span>
          @if(isset($group['chef_groupement']) && $group['chef_groupement'])
            <span class="badge bg-white text-dark shadow-sm px-3 py-2 rounded-pill fw-bold fs-6">
              <i class="fas fa-crown me-1 text-warning"></i> Chef de Canton: {{ $group['chef_groupement'] }}
            </span>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Interactive Sub-Navigation Tabs -->
  <div class="custom-card p-2 mb-4">
    <ul class="nav nav-pills nav-fill flex-column flex-sm-row gap-2" id="groupementTab" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold active py-3 w-100" id="gtab-villages" data-bs-toggle="pill" data-bs-target="#gcontent-villages" type="button" role="tab">
          <i class="fas fa-tree me-2 text-success"></i> Villages du Groupement ({{ count($vList) }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold py-3 w-100" id="gtab-pas" data-bs-toggle="pill" data-bs-target="#gcontent-pas" type="button" role="tab">
          <i class="fas fa-user-shield me-2 text-warning"></i> Personnalités Administratives ({{ count($pasList) }})
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link text-main font-semibold py-3 w-100" id="gtab-histoire" data-bs-toggle="pill" data-bs-target="#gcontent-histoire" type="button" role="tab">
          <i class="fas fa-book-open me-2 text-primary"></i> Histoire & Patrimoine
        </button>
      </li>
    </ul>
  </div>

  <!-- Groupement Tab Contents -->
  <div class="tab-content" id="groupementTabContent">

    <!-- 1. VILLAGES DU GROUPEMENT -->
    <div class="tab-pane fade show active" id="gcontent-villages" role="tabpanel">
      <div class="row g-4 mb-4">
        <!-- Chef Canton Card -->
        <div class="col-lg-4">
          <div class="custom-card p-4 h-100">
            <h4 class="font-serif fw-bold mb-3"><i class="fas fa-crown text-warning me-2"></i> Chefferie du Canton</h4>
            <hr class="opacity-10 mb-4">

            <div class="text-center mb-4">
              <div class="rounded-circle overflow-hidden mx-auto mb-3 shadow" style="width: 140px; height: 140px; border: 4px solid var(--accent-green);">
                @if($chefImg)
                  <img src="{{ $chefImg }}" alt="Chef Canton" class="w-100 h-100" style="object-fit: cover;">
                @else
                  <div class="w-100 h-100 bg-secondary bg-opacity-20 d-flex align-items-center justify-content-center text-muted">
                    <i class="fas fa-user-shield fs-1"></i>
                  </div>
                @endif
              </div>
              <h5 class="fw-bold font-serif mb-1">{{ $group['chef_groupement'] ?? 'Chef Supérieur de Canton' }}</h5>
              <span class="badge bg-warning bg-opacity-10 text-dark fw-bold px-3 py-1">Chefferie Traditionnelle</span>
            </div>

            <div class="mb-3">
              <h6 class="fw-bold small text-uppercase text-muted">Statut</h6>
              <p class="small text-muted mb-0">Canton traditionnel regroupant les localités d'origine du territoire.</p>
            </div>
          </div>
        </div>

        <!-- Villages Grid -->
        <div class="col-lg-8">
          <div class="custom-card p-4 p-md-5 h-100">
            <h3 class="font-serif fw-bold mb-3"><i class="fas fa-tree text-success me-2"></i> Villages & Localités Rattachés</h3>
            <hr class="opacity-10 mb-4">

            @if(count($vList) === 0)
              <div class="text-center py-4">
                <i class="fas fa-home fs-2 text-muted opacity-50 mb-2"></i>
                <p class="text-muted small mb-0">Aucun village encore rattaché à ce groupement.</p>
              </div>
            @else
              <div class="row g-3">
                @foreach($vList as $v)
                  @php $vId = is_array($v) ? $v['id'] : $v->id; $vName = is_array($v) ? $v['name'] : $v->name; $vDesc = is_array($v) ? ($v['description'] ?? '') : ($v->description ?? ''); @endphp
                  <div class="col-md-6">
                    <div class="card h-100 border-0 custom-card p-3 shadow-sm">
                      <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle overflow-hidden bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; flex-shrink: 0;">
                          <i class="fas fa-home fs-5"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                          <h6 class="fw-bold font-serif mb-1 text-truncate">{{ $vName }}</h6>
                          <small class="text-muted d-block text-truncate">{{ Str::limit($vDesc, 60) }}</small>
                        </div>
                      </div>
                      <div class="mt-3 pt-2 border-top border-secondary border-opacity-10 text-end">
                        <a href="{{ route('village.show', $vId . '-' . Str::slug($vName)) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1 small">
                          Visiter la fiche <i class="fas fa-arrow-right ms-1"></i>
                        </a>
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

    <!-- 2. PERSONNALITÉS ADMINISTRATIVES -->
    <div class="tab-pane fade" id="gcontent-pas" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <div>
            <h3 class="font-serif fw-bold mb-1"><i class="fas fa-user-shield text-warning me-2"></i> Autorités Administratives du Canton</h3>
            <p class="text-muted small mb-0">Représentants administratifs, autorités de l'État et responsables locaux rattachés au canton {{ $group['name'] }}.</p>
          </div>
          @if(count($pasList) > 0)
          <div class="input-group" style="max-width: 260px;">
            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="searchPA" class="form-control border-start-0 ps-0" placeholder="Rechercher par nom…" oninput="filterPA()">
          </div>
          @endif
        </div>

        @if(count($pasList) === 0)
          <div class="text-center py-5">
            <i class="fas fa-user-shield fs-1 text-muted opacity-50 mb-3"></i>
            <h5 class="fw-bold">Aucune autorité administrative répertoriée</h5>
            <p class="text-muted small">Le répertoire des autorités administratives de ce canton sera prochainement mis à jour.</p>
          </div>
        @else
          <div class="row g-4" id="paGrid">
            @foreach($pasList as $pa)
              @php
                $paNom   = is_array($pa) ? (($pa['prenom'] ?? '') . ' ' . ($pa['nom'] ?? '')) : (($pa->prenom ?? '') . ' ' . ($pa->nom ?? ''));
                $paRole  = is_array($pa) ? ($pa['role'] ?? 'Autorité Administrative') : ($pa->role ?? 'Autorité Administrative');
                $paBio   = is_array($pa) ? ($pa['biographie'] ?? '') : ($pa->biographie ?? '');
                $paPhoto = is_array($pa) ? ($pa['photo'] ?? null) : ($pa->photo ?? null);
                $paPhotoUrl = $paPhoto ? CloudinaryHelper::url($paPhoto) : null;
                $paId    = is_array($pa) ? ($pa['id'] ?? 0) : ($pa->id ?? 0);
              @endphp
              <div class="col-md-6 col-lg-4 pa-item" data-name="{{ strtolower(trim($paNom)) }}">
                <div class="card h-100 border-0 custom-card text-center p-4" style="cursor:pointer; transition: transform .18s, box-shadow .18s;" onclick="openPAModal({{ $paId }})" onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.15)'" onmouseleave="this.style.transform='';this.style.boxShadow=''">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3 shadow" style="width: 100px; height: 100px; border: 3px solid #f59e0b;">
                    @if($paPhotoUrl)
                      <img src="{{ $paPhotoUrl }}" alt="{{ $paNom }}" class="w-100 h-100" style="object-fit: cover;">
                    @else
                      <div class="w-100 h-100 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-shield fs-2"></i>
                      </div>
                    @endif
                  </div>
                  <h5 class="fw-bold font-serif mb-1">{{ trim($paNom) }}</h5>
                  <span class="badge bg-info bg-opacity-10 text-info fw-bold mb-2 px-3 py-1">{{ $paRole }}</span>
                  <p class="text-muted small mb-0">{{ Str::limit($paBio ?? 'Autorité rattachée au canton.', 100) }}</p>
                  @if($paBio && strlen($paBio) > 60)
                  <div class="mt-3 pt-2 border-top border-secondary border-opacity-10">
                    <small class="text-warning fw-semibold"><i class="fas fa-eye me-1"></i> Voir le profil complet</small>
                  </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
          <div id="paEmpty" class="text-center py-5 d-none">
            <i class="fas fa-search fs-1 text-muted opacity-50 mb-3"></i>
            <p class="text-muted">Aucune personnalité ne correspond à votre recherche.</p>
          </div>
        @endif
      </div>
    </div>

    <!-- 3. HISTOIRE & PATRIMOINE -->
    <div class="tab-pane fade" id="gcontent-histoire" role="tabpanel">
      <div class="custom-card p-4 p-md-5">
        <h3 class="font-serif fw-bold mb-3"><i class="fas fa-book-open text-primary me-2"></i> Histoire & Origines du Canton</h3>
        <hr class="opacity-10 mb-4">

        <div class="fs-6 text-main mb-4">
          {!! nl2br(e($group['histoire'] ?? $group['description'] ?? 'Ce canton traditionnel possède un héritage culturel séculaire d\'histoire et d\'organisation coutumière.')) !!}
        </div>
      </div>
    </div>

  </div>
</div>

<!-- ============================================================
     MODAL — Personnalité Administrative
============================================================ -->
<div class="modal fade" id="modalPA" tabindex="-1" aria-labelledby="modalPALabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">
      <!-- Header -->
      <div class="modal-header border-0 px-4 pt-4 pb-3" style="background: linear-gradient(135deg, #1e1b4b, #92400e);">
        <div class="d-flex align-items-center gap-3 w-100">
          <div id="paPhoto" class="rounded-circle overflow-hidden shadow flex-shrink-0 d-flex align-items-center justify-content-center" style="width:80px;height:80px;border:3px solid #f59e0b;background:#1e293b;"></div>
          <div>
            <h5 class="modal-title fw-bold font-serif text-white mb-0" id="modalPALabel">—</h5>
            <span id="paRole" class="badge bg-warning text-dark fw-bold px-3 py-1 mt-1">—</span>
          </div>
        </div>
        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <!-- Corps -->
      <div class="modal-body p-4 p-md-5">
        <h6 class="fw-bold text-uppercase text-muted small mb-2"><i class="fas fa-scroll me-1"></i> Biographie</h6>
        <p id="paBiographie" class="text-main mb-0 lh-lg">—</p>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     DONNÉES JSON pour les modals
============================================================ -->
<script>
@php
  $pasData = collect($pasList)->map(function($pa) {
    $nom    = is_array($pa) ? (($pa['prenom'] ?? '') . ' ' . ($pa['nom'] ?? '')) : (($pa->prenom ?? '') . ' ' . ($pa->nom ?? ''));
    $role   = is_array($pa) ? ($pa['role'] ?? 'Autorité Administrative') : ($pa->role ?? 'Autorité Administrative');
    $bio    = is_array($pa) ? ($pa['biographie'] ?? '') : ($pa->biographie ?? '');
    $photo  = is_array($pa) ? ($pa['photo'] ?? null) : ($pa->photo ?? null);
    $id     = is_array($pa) ? ($pa['id'] ?? 0) : ($pa->id ?? 0);
    return [
      'id'    => $id,
      'nom'   => trim($nom),
      'role'  => $role,
      'bio'   => $bio ?: 'Autorité rattachée au canton.',
      'photo' => $photo ? \App\Helpers\CloudinaryHelper::url($photo) : null,
    ];
  });
@endphp

const PAS_DATA = @json($pasData);

function openPAModal(id) {
  const pa = PAS_DATA.find(x => x.id === id);
  if (!pa) return;

  const photoEl = document.getElementById('paPhoto');
  photoEl.innerHTML = pa.photo
    ? `<img src="${pa.photo}" class="w-100 h-100" style="object-fit:cover;" alt="${pa.nom}">`
    : `<i class="fas fa-user-shield fs-2 text-warning"></i>`;

  document.getElementById('modalPALabel').textContent = pa.nom;
  document.getElementById('paRole').textContent = pa.role;
  document.getElementById('paBiographie').textContent = pa.bio;

  new bootstrap.Modal(document.getElementById('modalPA')).show();
}

// ── Filtre recherche Personnalités Administratives ──────────
function filterPA() {
  const q = (document.getElementById('searchPA')?.value || '').toLowerCase().trim();
  const items = document.querySelectorAll('.pa-item');
  let count = 0;
  items.forEach(el => {
    const show = el.dataset.name.includes(q);
    el.style.display = show ? '' : 'none';
    if (show) count++;
  });
  document.getElementById('paEmpty').classList.toggle('d-none', count > 0);
}
</script>
@endsection
