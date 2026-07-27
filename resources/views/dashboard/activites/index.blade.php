@extends('layouts.dashboard')

@section('title', 'Activités du Cameroun - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-hiking text-success me-2"></i> Activités & Initiatives</h2>
    <p class="text-muted small mb-0">Gestion des activités culturelles, économiques et artisanales des villages.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateActivite">
    <i class="fas fa-plus me-1"></i> Ajouter une Activité
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.activites.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher une activité par nom ou type..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.activites.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($activites->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-hiking fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucune activité enregistrée</h4>
      <p class="text-muted">Créez votre première activité pour un village.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Activité</th>
            <th>Type</th>
            <th>Village</th>
            <th>Description</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($activites as $act)
            @php
              $actImg = $act->image ? (Str::startsWith($act->image, ['http://', 'https://']) ? $act->image : Storage::url($act->image)) : null;
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-3 overflow-hidden bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0;">
                    @if($actImg)
                      <img src="{{ $actImg }}" alt="{{ $act->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas fa-hiking"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6 mb-0">{{ $act->name }}</div>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-info bg-opacity-10 text-info text-uppercase">{{ $act->type }}</span></td>
              <td><span class="badge bg-secondary bg-opacity-20 text-main">{{ $act->village->name ?? '-' }}</span></td>
              <td class="text-muted small" style="max-width: 280px;">{{ Str::limit($act->description ?? '-', 80) }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-circle" style="width:34px;height:34px;padding:0;" title="Voir" onclick="openShowActivite({{ json_encode($act) }})">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-circle" style="width:34px;height:34px;padding:0;" title="Éditer" onclick="openEditActivite({{ json_encode($act) }})">
                    <i class="fas fa-pen"></i>
                  </button>
                  <form action="{{ route('dashboard.activites.destroy', $act->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette activité ?');">
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

    @if(method_exists($activites, 'hasPages') && $activites->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $activites->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Activite -->
<div class="modal fade" id="modalCreateActivite" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-plus-circle text-success me-2"></i> Nouvelle Activité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.activites.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom de l'Activité <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Poterie, Tissage..." required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Type <span class="text-danger">*</span></label>
              <select name="type" class="form-select py-2" required>
                <option value="Artisanale">Artisanale</option>
                <option value="Économique">Économique</option>
                <option value="Touristique">Touristique</option>
                <option value="Agricole">Agricole</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Village <span class="text-danger">*</span></label>
              <select name="village_id" class="form-select py-2" required>
                <option value="">Sélectionner un village...</option>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Illustration / Image</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Présentation..."></textarea>
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

<!-- Modal Edit Activite -->
<div class="modal fade" id="modalEditActivite" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer l'Activité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditActivite" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom de l'Activité</label>
              <input type="text" id="act_edit_name" name="name" class="form-control py-2" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Type</label>
              <select id="act_edit_type" name="type" class="form-select py-2" required>
                <option value="Artisanale">Artisanale</option>
                <option value="Économique">Économique</option>
                <option value="Touristique">Touristique</option>
                <option value="Agricole">Agricole</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Village</label>
              <select id="act_edit_village_id" name="village_id" class="form-select py-2" required>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Photo</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea id="act_edit_desc" name="description" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Activite -->
<div class="modal fade" id="modalShowActivite" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-info-circle text-info me-2"></i> Détails de l'Activité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="act_show_name" class="fw-bold font-serif text-success mb-2"></h4>
        <span id="act_show_type" class="badge bg-info bg-opacity-10 text-info fw-bold mb-3 px-3 py-2"></span>
        <p id="act_show_village" class="text-muted small mb-2"></p>
        <p id="act_show_desc" class="text-main small px-3"></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditActivite(act) {
    $('#formEditActivite').attr('action', '/dashboard/activites/' + act.id);
    $('#act_edit_name').val(act.name);
    $('#act_edit_type').val(act.type);
    $('#act_edit_village_id').val(act.village_id);
    $('#act_edit_desc').val(act.description || '');
    new bootstrap.Modal(document.getElementById('modalEditActivite')).show();
  }

  function openShowActivite(act) {
    $('#act_show_name').text(act.name);
    $('#act_show_type').text(act.type);
    $('#act_show_village').text('Village: ' + (act.village ? act.village.name : '-'));
    $('#act_show_desc').text(act.description || 'Aucune description.');
    new bootstrap.Modal(document.getElementById('modalShowActivite')).show();
  }
</script>
@endsection