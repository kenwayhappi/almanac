@extends('layouts.dashboard')

@section('title', 'Événements du Cameroun - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-calendar-alt text-success me-2"></i> Événements & Célébrations</h2>
    <p class="text-muted small mb-0">Gestion des festivals, cérémonies traditionnelles et rencontres du Cameroun.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateEvent">
    <i class="fas fa-plus me-1"></i> Ajouter un Événement
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.events.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher un événement par nom..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.events.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($events->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-calendar-alt fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucun événement répertorié</h4>
      <p class="text-muted">Commencez par ajouter un événement pour un village.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Événement</th>
            <th>Type</th>
            <th>Village</th>
            <th>Dates</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($events as $ev)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-3 overflow-hidden bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                    @if($ev->image)
                      <img src="{{ \App\Helpers\CloudinaryHelper::url($ev->image) }}" alt="{{ $ev->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas fa-calendar-day fs-5"></i>
                    @endif
                  </div>
                  <div>
                    <a href="{{ route('dashboard.events.show', $ev->id) }}" class="fw-bold font-serif fs-6 text-decoration-none text-main hover-success">{{ $ev->name }}</a>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-warning bg-opacity-10 text-dark fw-bold text-uppercase">{{ $ev->type }}</span></td>
              <td><span class="badge bg-secondary bg-opacity-20 text-main">{{ $ev->village->name ?? '-' }}</span></td>
              <td>
                <span class="small fw-semibold text-success">{{ $ev->start_date ? \Carbon\Carbon::parse($ev->start_date)->format('d/m/Y') : '-' }}</span>
                <span class="small text-muted"> au </span>
                <span class="small fw-semibold text-danger">{{ $ev->end_date ? \Carbon\Carbon::parse($ev->end_date)->format('d/m/Y') : '-' }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('dashboard.events.show', $ev->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 small" title="Voir les cotisations et détails">
                    <i class="fas fa-eye me-1"></i> Voir & Cotisations
                  </a>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 small" onclick="openEditEvent({{ json_encode($ev) }})" title="Éditer">
                    <i class="fas fa-pen"></i>
                  </button>
                  <form action="{{ route('dashboard.events.destroy', $ev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet événement ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 small" title="Supprimer">
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

    @if($events->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Event -->
<div class="modal fade" id="modalCreateEvent" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-calendar-plus text-success me-2"></i> Nouvel Événement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom de l'Événement <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Festival Medumba..." required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Type <span class="text-danger">*</span></label>
              <select name="type" class="form-select py-2" required>
                <option value="Culturel">Culturel</option>
                <option value="Traditionnel">Traditionnel</option>
                <option value="Religieux">Religieux</option>
                <option value="Sportif">Sportif</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Village <span class="text-danger">*</span></label>
              <select name="village_id" class="form-select py-2" required>
                <option value="">Sélectionner...</option>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Date Début <span class="text-danger">*</span></label>
              <input type="date" name="start_date" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Date Fin <span class="text-danger">*</span></label>
              <input type="date" name="end_date" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Affiche / Image</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Programme..."></textarea>
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

<!-- Modal Edit Event -->
<div class="modal fade" id="modalEditEvent" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer l'Événement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditEvent" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom de l'Événement</label>
              <input type="text" id="ev_edit_name" name="name" class="form-control py-2" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Type</label>
              <select id="ev_edit_type" name="type" class="form-select py-2" required>
                <option value="Culturel">Culturel</option>
                <option value="Traditionnel">Traditionnel</option>
                <option value="Religieux">Religieux</option>
                <option value="Sportif">Sportif</option>
                <option value="Autre">Autre</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Village</label>
              <select id="ev_edit_village_id" name="village_id" class="form-select py-2" required>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Date Début</label>
              <input type="date" id="ev_edit_start" name="start_date" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Date Fin</label>
              <input type="date" id="ev_edit_end" name="end_date" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Photo</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea id="ev_edit_desc" name="description" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Event -->
<div class="modal fade" id="modalShowEvent" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-calendar-day text-info me-2"></i> Détails de l'Événement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="ev_show_name" class="fw-bold font-serif text-success mb-2"></h4>
        <span id="ev_show_type" class="badge bg-warning bg-opacity-10 text-dark fw-bold mb-3 px-3 py-2"></span>
        <p id="ev_show_village" class="text-muted small mb-2"></p>
        <p id="ev_show_desc" class="text-main small px-3"></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditEvent(ev) {
    $('#formEditEvent').attr('action', '/dashboard/events/' + ev.id);
    $('#ev_edit_name').val(ev.name);
    $('#ev_edit_type').val(ev.type);
    $('#ev_edit_village_id').val(ev.village_id);
    $('#ev_edit_start').val(ev.start_date ? ev.start_date.substring(0, 10) : '');
    $('#ev_edit_end').val(ev.end_date ? ev.end_date.substring(0, 10) : '');
    $('#ev_edit_desc').val(ev.description || '');
    new bootstrap.Modal(document.getElementById('modalEditEvent')).show();
  }

  function openShowEvent(ev) {
    $('#ev_show_name').text(ev.name);
    $('#ev_show_type').text(ev.type);
    $('#ev_show_village').text('Village: ' + (ev.village ? ev.village.name : '-'));
    $('#ev_show_desc').text(ev.description || 'Aucune description.');
    new bootstrap.Modal(document.getElementById('modalShowEvent')).show();
  }
</script>
@endsection