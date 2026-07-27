@extends('layouts.dashboard')

@section('title', 'Notables & Élites - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-user-tie text-success me-2"></i> Notables, Élites & Personnalités</h2>
    <p class="text-muted small mb-0">Gestion des figures marquantes et personnalités des villages du Cameroun.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreatePersonality">
    <i class="fas fa-plus me-1"></i> Ajouter une Personnalité
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.personnalite.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher un notable par nom ou statut..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.personnalite.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($personalities->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-user-tie fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucune personnalité enregistrée</h4>
      <p class="text-muted">Inscrivez la première personnalité d'un village.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Personnalité</th>
            <th>Titre / Statut</th>
            <th>Village d'Origine</th>
            <th>Contact</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($personalities as $p)
            @php
              $pImg = $p->image ? (Str::startsWith($p->image, ['http://', 'https://']) ? $p->image : Storage::url($p->image)) : null;
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle overflow-hidden bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0;">
                    @if($pImg)
                      <img src="{{ $pImg }}" alt="{{ $p->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas fa-user"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6 mb-0">{{ $p->name }}</div>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-warning bg-opacity-10 text-dark">{{ $p->statut ?? 'Notable' }}</span></td>
              <td><span class="badge bg-secondary bg-opacity-20 text-main">{{ $p->village->name ?? '-' }}</span></td>
              <td class="small text-muted">{{ $p->contact ?? '-' }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-circle" style="width:34px;height:34px;padding:0;" title="Voir" onclick="openShowPersonality({{ json_encode($p) }})">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-circle" style="width:34px;height:34px;padding:0;" title="Éditer" onclick="openEditPersonality({{ json_encode($p) }})">
                    <i class="fas fa-pen"></i>
                  </button>
                  <form action="{{ route('dashboard.personnalite.destroy', $p->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette personnalité ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" style="width:34px;height:34px;padding:0;" title="Supprimer">
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

    @if($personalities->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $personalities->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Personality -->
<div class="modal fade" id="modalCreatePersonality" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-plus-circle text-success me-2"></i> Nouvelle Personnalité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.personnalite.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom Complet <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Dr. Samuel..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Village <span class="text-danger">*</span></label>
              <select name="village_id" class="form-select py-2" required>
                <option value="">Sélectionner...</option>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Titre / Statut</label>
              <input type="text" name="statut" class="form-control py-2" placeholder="Ex: Notable, Ministre...">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Contact / Email</label>
              <input type="text" name="contact" class="form-control py-2" placeholder="Téléphone...">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Photo</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Biographie</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Parcours..."></textarea>
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

<!-- Modal Edit Personality -->
<div class="modal fade" id="modalEditPersonality" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer la Personnalité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditPersonality" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom Complet</label>
              <input type="text" id="p_edit_name" name="name" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Village</label>
              <select id="p_edit_village_id" name="village_id" class="form-select py-2" required>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Statut</label>
              <input type="text" id="p_edit_statut" name="statut" class="form-control py-2">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Contact</label>
              <input type="text" id="p_edit_contact" name="contact" class="form-control py-2">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Photo</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Biographie</label>
              <textarea id="p_edit_desc" name="description" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Personality -->
<div class="modal fade" id="modalShowPersonality" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-info-circle text-info me-2"></i> Détails de la Personnalité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="p_show_name" class="fw-bold font-serif text-success mb-1"></h4>
        <span id="p_show_statut" class="badge bg-warning bg-opacity-10 text-dark fw-bold mb-3 px-3 py-2"></span>
        <p id="p_show_village" class="text-muted small mb-2"></p>
        <p id="p_show_desc" class="text-main small px-3"></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditPersonality(p) {
    $('#formEditPersonality').attr('action', '/dashboard/personnalite/' + p.id);
    $('#p_edit_name').val(p.name);
    $('#p_edit_village_id').val(p.village_id);
    $('#p_edit_statut').val(p.statut || '');
    $('#p_edit_contact').val(p.contact || '');
    $('#p_edit_desc').val(p.description || '');
    new bootstrap.Modal(document.getElementById('modalEditPersonality')).show();
  }

  function openShowPersonality(p) {
    $('#p_show_name').text(p.name);
    $('#p_show_statut').text(p.statut || 'Notable');
    $('#p_show_village').text('Village: ' + (p.village ? p.village.name : '-'));
    $('#p_show_desc').text(p.description || 'Aucune biographie.');
    new bootstrap.Modal(document.getElementById('modalShowPersonality')).show();
  }
</script>
@endsection