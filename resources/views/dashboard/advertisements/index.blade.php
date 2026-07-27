@extends('layouts.dashboard')

@section('title', 'Gestion des Publicités - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-ad text-warning me-2"></i> Régie Publicitaire & Bannières</h2>
    <p class="text-muted small mb-0">Gestion des bannières, vidéos sponsorisées et suivi des impressions uniques.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateAd">
    <i class="fas fa-plus me-1"></i> Créer une Publicité
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.advertisements.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher une annonce ou un annonceur..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.advertisements.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($advertisements->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-ad fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucune publicité enregistrée</h4>
      <p class="text-muted">Créez votre première annonce sponsorisée.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Publicité</th>
            <th>Type</th>
            <th>Emplacement</th>
            <th>Vues Uniques</th>
            <th>Annonceur / Contact</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($advertisements as $ad)
            @php $a = (object) $ad; @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-3 overflow-hidden bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="fas fa-bullhorn fs-5"></i>
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6">{{ $a->title ?? 'Annonce Sponsorisée' }}</div>
                    <small class="text-muted">ID: #{{ $a->id }}</small>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-info bg-opacity-10 text-info text-uppercase font-monospace">{{ $a->type }}</span></td>
              <td><span class="badge bg-dark bg-opacity-50 text-white">{{ $a->position ?? 'accueil' }}</span></td>
              <td class="fw-bold text-success"><i class="fas fa-eye me-1"></i> {{ number_format($a->views ?? 0) }}</td>
              <td>
                <div class="small fw-semibold">{{ $a->owner_name ?? 'Non défini' }}</div>
                <small class="text-muted">{{ $a->owner_contact ?? '-' }}</small>
              </td>
              <td class="text-end">
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-start-pill px-3" onclick="openShowAd({{ json_encode($a) }})">
                    <i class="fas fa-eye me-1"></i> Voir
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning px-3" onclick="openEditAd({{ json_encode($a) }})">
                    <i class="fas fa-pen me-1"></i> Éditer
                  </button>
                  <form action="{{ route('dashboard.advertisements.destroy', $a->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression de la publicité ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-end-pill px-3">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($advertisements->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $advertisements->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Ad -->
<div class="modal fade" id="modalCreateAd" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-ad text-warning me-2"></i> Nouvelle Publicité Sponsorisée</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.advertisements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Titre de la Publicité</label>
              <input type="text" name="title" class="form-control py-2" placeholder="Ex: Offre Partenaire...">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Type <span class="text-danger">*</span></label>
              <select name="type" class="form-select py-2" required>
                <option value="photo">Photo (Image)</option>
                <option value="video">Vidéo (MP4)</option>
                <option value="pdf">Document PDF</option>
                <option value="text">Texte pur</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Emplacement <span class="text-danger">*</span></label>
              <select name="position" class="form-select py-2" required>
                <option value="accueil">Page d'Accueil</option>
                <option value="rechercher">Page de Recherche</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Fichier Média</label>
              <input type="file" name="file" class="form-control py-2">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Client / Propriétaire</label>
              <input type="text" name="owner_name" class="form-control py-2" placeholder="Ex: Ets Njoya...">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Contact Client</label>
              <input type="text" name="owner_contact" class="form-control py-2" placeholder="+237...">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Date de fin</label>
              <input type="date" name="end_date" class="form-control py-2">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Contenu Texte (si format texte)</label>
              <textarea name="content" class="form-control" rows="3" placeholder="Texte promotionnel..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success rounded-pill px-5">Publier</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Ad -->
<div class="modal fade" id="modalEditAd" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer la Publicité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditAd" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Titre de la Publicité</label>
              <input type="text" id="ad_edit_title" name="title" class="form-control py-2">
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Type</label>
              <select id="ad_edit_type" name="type" class="form-select py-2" required>
                <option value="photo">Photo</option>
                <option value="video">Vidéo</option>
                <option value="pdf">PDF</option>
                <option value="text">Texte</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Emplacement</label>
              <select id="ad_edit_position" name="position" class="form-select py-2" required>
                <option value="accueil">Accueil</option>
                <option value="rechercher">Recherche</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Fichier</label>
              <input type="file" name="file" class="form-control py-2">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Client</label>
              <input type="text" id="ad_edit_owner_name" name="owner_name" class="form-control py-2">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Contact Client</label>
              <input type="text" id="ad_edit_owner_contact" name="owner_contact" class="form-control py-2">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Contenu Texte</label>
              <textarea id="ad_edit_content" name="content" class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning rounded-pill px-5">Mettre à Jour</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Show Ad -->
<div class="modal fade" id="modalShowAd" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-ad text-info me-2"></i> Détails de la Publicité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="ad_show_title" class="fw-bold font-serif text-warning mb-1"></h4>
        <span id="ad_show_type" class="badge bg-info bg-opacity-10 text-info fw-bold mb-3 px-3 py-2"></span>
        <div class="p-3 bg-body-tertiary rounded text-start mb-3">
          <div class="small text-muted mb-1">Emplacement : <strong id="ad_show_pos"></strong></div>
          <div class="small text-muted mb-1">Vues uniques : <strong id="ad_show_views" class="text-success"></strong></div>
          <div class="small text-muted">Annonceur : <strong id="ad_show_owner"></strong></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditAd(ad) {
    $('#formEditAd').attr('action', '/dashboard/advertisements/' + ad.id);
    $('#ad_edit_title').val(ad.title || '');
    $('#ad_edit_type').val(ad.type);
    $('#ad_edit_position').val(ad.position);
    $('#ad_edit_owner_name').val(ad.owner_name || '');
    $('#ad_edit_owner_contact').val(ad.owner_contact || '');
    $('#ad_edit_content').val(ad.content || '');
    new bootstrap.Modal(document.getElementById('modalEditAd')).show();
  }

  function openShowAd(ad) {
    $('#ad_show_title').text(ad.title || 'Annonce Sponsorisée');
    $('#ad_show_type').text(ad.type.toUpperCase());
    $('#ad_show_pos').text(ad.position);
    $('#ad_show_views').text((ad.views || 0) + ' vues uniques');
    $('#ad_show_owner').text((ad.owner_name || 'Non défini') + ' (' + (ad.owner_contact || '-') + ')');
    new bootstrap.Modal(document.getElementById('modalShowAd')).show();
  }
</script>
@endsection