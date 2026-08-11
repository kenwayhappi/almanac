@extends('layouts.dashboard')

@section('title', 'Groupements du Cameroun - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-layer-group text-success me-2"></i> Groupements & Cantons</h2>
    <p class="text-muted small mb-0">Gestion des cantons traditionnels regroupant les villages du Cameroun.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateGroupement">
    <i class="fas fa-plus me-1"></i> Nouveau Groupement
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.groupements.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher par nom de groupement ou chef de canton..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.groupements.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if(count($groupements ?? []) === 0)
    <div class="text-center py-5">
      <i class="fas fa-layer-group fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucun groupement trouvé</h4>
      <p class="text-muted">Commencez par enregistrer un premier canton/groupement.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Groupement</th>
            <th>Chef Canton</th>
            <th>Description</th>
            <th>Rattachement</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($groupements as $groupement)
            @php $g = (object) $groupement; @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle overflow-hidden bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0;">
                    @if(isset($g->image) && $g->image)
                      <img src="{{ $g->image }}" alt="{{ $g->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas fa-layer-group text-success"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6 mb-0">{{ $g->name }}</div>
                    <small class="text-muted">Canton Traditionnel</small>
                  </div>
                </div>
              </td>
              <td>
                @if(isset($g->chef_groupement) && $g->chef_groupement)
                  <span class="badge bg-warning bg-opacity-10 text-dark text-wrap text-start" style="white-space: normal; max-width: 200px;"><i class="fas fa-crown me-1 text-warning"></i> {{ $g->chef_groupement }}</span>
                @else
                  <span class="text-muted small">Non spécifié</span>
                @endif
              </td>
              <td class="text-muted small" style="max-width: 250px;">
                {{ Str::limit($g->description ?? 'Groupement culturel.', 70) }}
              </td>
              <td>
                <span class="badge bg-secondary bg-opacity-20 text-main">{{ is_array($g->parent ?? null) ? ($g->parent['name'] ?? '-') : ($g->parent->name ?? '-') }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-circle" style="width:34px;height:34px;padding:0;" title="Voir" onclick="openShowGroupement({{ json_encode($g) }})">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-circle" style="width:34px;height:34px;padding:0;" title="Éditer" onclick="openEditGroupement({{ json_encode($g) }})">
                    <i class="fas fa-pen"></i>
                  </button>
                  <form action="{{ route('dashboard.groupements.destroy', $g->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer ce groupement ?');">
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

    @if(isset($paginator) && $paginator->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $paginator->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Groupement -->
<div class="modal fade" id="modalCreateGroupement" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-plus-circle text-success me-2"></i> Nouveau Groupement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.groupements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="pays" value="237">
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Groupement <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Canton Bandjoun..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Chef Supérieur de Canton</label>
              <input type="text" name="chef_groupement" class="form-control py-2" placeholder="Nom du Chef...">
            </div>

            <!-- Localisation Cascade (Région -> Département -> Arrondissement) -->
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Région</label>
              <select class="form-select py-2" id="create_g_region" name="division1">
                <option value="">Sélectionner une région</option>
                @if(isset($regions))
                  @foreach($regions as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Département</label>
              <select class="form-select py-2" id="create_g_dept" name="division2">
                <option value="">Sélectionner un département</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Arrondissement</label>
              <select class="form-select py-2" id="create_g_arrond" name="division3">
                <option value="">Sélectionner un arrondissement</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Photo du Chef</label>
              <input type="file" name="chef_image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Couverture / Blason</label>
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

<!-- Modal Edit Groupement -->
<div class="modal fade" id="modalEditGroupement" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer le Groupement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditGroupement" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="pays" value="237">
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Groupement</label>
              <input type="text" id="g_edit_name" name="name" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Chef Supérieur</label>
              <input type="text" id="g_edit_chef" name="chef_groupement" class="form-control py-2">
            </div>

            <!-- Localisation Cascade (Région -> Département -> Arrondissement) -->
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Région</label>
              <select class="form-select py-2" id="edit_g_region" name="division1">
                <option value="">Sélectionner une région</option>
                @if(isset($regions))
                  @foreach($regions as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                  @endforeach
                @endif
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Département</label>
              <select class="form-select py-2" id="edit_g_dept" name="division2">
                <option value="">Sélectionner un département</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Arrondissement</label>
              <select class="form-select py-2" id="edit_g_arrond" name="division3">
                <option value="">Sélectionner un arrondissement</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Changer Photo Chef</label>
              <input type="file" name="chef_image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Changer Couverture</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea id="g_edit_desc" name="description" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Groupement -->
<div class="modal fade" id="modalShowGroupement" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-layer-group text-info me-2"></i> Fiche du Groupement</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h3 id="g_show_name" class="fw-bold font-serif text-success mb-2"></h3>
        <p id="g_show_chef" class="badge bg-warning bg-opacity-10 text-dark fw-bold px-3 py-2 fs-6 mb-3"></p>
        <p id="g_show_desc" class="text-muted small px-3"></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditGroupement(g) {
    $('#formEditGroupement').attr('action', '/dashboard/groupements/' + g.id);
    $('#g_edit_name').val(g.name);
    $('#g_edit_chef').val(g.chef_groupement || '');
    $('#g_edit_desc').val(g.description || '');

    // Reset location dropdowns
    $('#edit_g_region').val('');
    $('#edit_g_dept').empty().append('<option value="">Sélectionner un département</option>');
    $('#edit_g_arrond').empty().append('<option value="">Sélectionner un arrondissement</option>');

    // Pre-populate location cascade if exists
    if (g.region_id) {
      $('#edit_g_region').val(g.region_id);
      $.get('/api/divisions/parent/' + g.region_id, function(res) {
        if (res.success && res.divisions) {
          res.divisions.forEach(function(d) {
            const sel = (g.department_id && g.department_id == d.id) ? 'selected' : '';
            $('#edit_g_dept').append(`<option value="${d.id}" ${sel}>${d.name}</option>`);
          });

          if (g.department_id) {
            $.get('/api/divisions/parent/' + g.department_id, function(res2) {
              if (res2.success && res2.divisions) {
                res2.divisions.forEach(function(arr) {
                  const sel2 = (g.arrondissement_id && g.arrondissement_id == arr.id) ? 'selected' : '';
                  $('#edit_g_arrond').append(`<option value="${arr.id}" ${sel2}>${arr.name}</option>`);
                });
              }
            });
          }
        }
      });
    }

    new bootstrap.Modal(document.getElementById('modalEditGroupement')).show();
  }

  function openShowGroupement(g) {
    $('#g_show_name').text(g.name);
    $('#g_show_chef').text('Chef Canton: ' + (g.chef_groupement || 'Non spécifié'));
    $('#g_show_desc').text(g.description || 'Aucune description.');
    new bootstrap.Modal(document.getElementById('modalShowGroupement')).show();
  }

  // Location Cascade JS (Région -> Département -> Arrondissement)
  $('#create_g_region').on('change', function() {
    const parentId = $(this).val();
    $('#create_g_dept').empty().append('<option value="">Sélectionner un département</option>');
    $('#create_g_arrond').empty().append('<option value="">Sélectionner un arrondissement</option>');
    if (parentId) {
      $.get('/api/divisions/parent/' + parentId, function(res) {
        if (res.success && res.divisions) {
          res.divisions.forEach(function(d) {
            $('#create_g_dept').append(`<option value="${d.id}">${d.name}</option>`);
          });
        }
      });
    }
  });

  $('#create_g_dept').on('change', function() {
    const parentId = $(this).val();
    $('#create_g_arrond').empty().append('<option value="">Sélectionner un arrondissement</option>');
    if (parentId) {
      $.get('/api/divisions/parent/' + parentId, function(res) {
        if (res.success && res.divisions) {
          res.divisions.forEach(function(d) {
            $('#create_g_arrond').append(`<option value="${d.id}">${d.name}</option>`);
          });
        }
      });
    }
  });

  $('#edit_g_region').on('change', function() {
    const parentId = $(this).val();
    $('#edit_g_dept').empty().append('<option value="">Sélectionner un département</option>');
    $('#edit_g_arrond').empty().append('<option value="">Sélectionner un arrondissement</option>');
    if (parentId) {
      $.get('/api/divisions/parent/' + parentId, function(res) {
        if (res.success && res.divisions) {
          res.divisions.forEach(function(d) {
            $('#edit_g_dept').append(`<option value="${d.id}">${d.name}</option>`);
          });
        }
      });
    }
  });

  $('#edit_g_dept').on('change', function() {
    const parentId = $(this).val();
    $('#edit_g_arrond').empty().append('<option value="">Sélectionner un arrondissement</option>');
    if (parentId) {
      $.get('/api/divisions/parent/' + parentId, function(res) {
        if (res.success && res.divisions) {
          res.divisions.forEach(function(d) {
            $('#edit_g_arrond').append(`<option value="${d.id}">${d.name}</option>`);
          });
        }
      });
    }
  });
</script>
@endsection