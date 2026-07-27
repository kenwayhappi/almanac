@extends('layouts.dashboard')

@section('title', 'Autorités Administratives - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-user-shield text-success me-2"></i> Autorités Administratives</h2>
    <p class="text-muted small mb-0">Gestion des autorités (Sous-préfets, Maires, Représentants) rattachées aux cantons.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreatePersonnaliteAdmin">
    <i class="fas fa-plus me-1"></i> Ajouter une Autorité
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.personnalites_administratives.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher une autorité par nom, prénom ou rôle..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.personnalites_administratives.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($personnalites->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-user-shield fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucune autorité administrative trouvée</h4>
      <p class="text-muted">Commencez par ajouter une première autorité administrative.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nom & Prénom</th>
            <th>Rôle / Poste</th>
            <th>Groupement / Canton</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($personnalites as $p)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle overflow-hidden bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                    @if($p->photo)
                      <img src="{{ Storage::url($p->photo) }}" alt="{{ $p->nom }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas fa-user-shield fs-5"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6">{{ $p->nom }} {{ $p->prenom }}</div>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-info bg-opacity-10 text-info fw-bold">{{ $p->role }}</span></td>
              <td><span class="badge bg-secondary bg-opacity-20 text-main">{{ $p->villageGroup->name ?? '-' }}</span></td>
              <td class="text-end">
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-start-pill px-3" onclick="openShowPersonnaliteAdmin({{ json_encode($p) }})">
                    <i class="fas fa-eye me-1"></i> Voir
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning px-3" onclick="openEditPersonnaliteAdmin({{ json_encode($p) }})">
                    <i class="fas fa-pen me-1"></i> Éditer
                  </button>
                  <form action="{{ route('dashboard.personnalites_administratives.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
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

    @if($personnalites->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $personnalites->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Personnalite Admin -->
<div class="modal fade" id="modalCreatePersonnaliteAdmin" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-user-shield text-success me-2"></i> Nouvelle Autorité Administrative</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.personnalites_administratives.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom <span class="text-danger">*</span></label>
              <input type="text" name="nom" class="form-control py-2" placeholder="Ex: Mbida" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Prénom <span class="text-danger">*</span></label>
              <input type="text" name="prenom" class="form-control py-2" placeholder="Ex: Jean Claude" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Poste / Rôle <span class="text-danger">*</span></label>
              <input type="text" name="role" class="form-control py-2" placeholder="Ex: Sous-Préfet, Maire..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Groupement <span class="text-danger">*</span></label>
              <select name="village_group_id" class="form-select py-2" required>
                <option value="">Sélectionner un groupement...</option>
                @foreach($villageGroups as $g)
                  <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Photo Officielle</label>
              <input type="file" name="photo" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Biographie & Projets</label>
              <textarea name="biographie" class="form-control" rows="3" placeholder="Description..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success rounded-pill px-5">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Personnalite Admin -->
<div class="modal fade" id="modalEditPersonnaliteAdmin" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer l'Autorité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditPersonnaliteAdmin" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom</label>
              <input type="text" id="pa_edit_nom" name="nom" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Prénom</label>
              <input type="text" id="pa_edit_prenom" name="prenom" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Rôle</label>
              <input type="text" id="pa_edit_role" name="role" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Groupement</label>
              <select id="pa_edit_village_group_id" name="village_group_id" class="form-select py-2" required>
                @foreach($villageGroups as $g)
                  <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Changer Photo</label>
              <input type="file" name="photo" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Biographie</label>
              <textarea id="pa_edit_bio" name="biographie" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Personnalite Admin -->
<div class="modal fade" id="modalShowPersonnaliteAdmin" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-user-shield text-info me-2"></i> Fiche d'Autorité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="pa_show_name" class="fw-bold font-serif text-success mb-1"></h4>
        <span id="pa_show_role" class="badge bg-info bg-opacity-10 text-info fw-bold mb-3 px-3 py-2"></span>
        <p id="pa_show_group" class="text-muted small mb-2"></p>
        <p id="pa_show_bio" class="text-main small px-3"></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditPersonnaliteAdmin(p) {
    $('#formEditPersonnaliteAdmin').attr('action', '/dashboard/personnalites-administratives/' + p.id);
    $('#pa_edit_nom').val(p.nom);
    $('#pa_edit_prenom').val(p.prenom);
    $('#pa_edit_role').val(p.role);
    $('#pa_edit_village_group_id').val(p.village_group_id);
    $('#pa_edit_bio').val(p.biographie || '');
    new bootstrap.Modal(document.getElementById('modalEditPersonnaliteAdmin')).show();
  }

  function openShowPersonnaliteAdmin(p) {
    $('#pa_show_name').text((p.prenom || '') + ' ' + (p.nom || ''));
    $('#pa_show_role').text(p.role || 'Autorité Administrative');
    $('#pa_show_group').text('Groupement: ' + (p.village_group ? p.village_group.name : '-'));
    $('#pa_show_bio').text(p.biographie || 'Aucune biographie.');
    new bootstrap.Modal(document.getElementById('modalShowPersonnaliteAdmin')).show();
  }
</script>
@endsection