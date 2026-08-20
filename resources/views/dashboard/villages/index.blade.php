@extends('layouts.dashboard')

@section('title', 'Villages & Quartiers du Cameroun - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-map-marker-alt text-success me-2"></i> Villages & Quartiers du Cameroun</h2>
    <p class="text-muted small mb-0">Répertoire officiel et fiches patrimoniales des localités.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateVillage">
    <i class="fas fa-plus me-1"></i> Ajouter une Localité
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.villages.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher par nom de village ou quartier..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.villages.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($villages->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-map-marker-alt fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucune localité trouvée</h4>
      <p class="text-muted">Commencez par ajouter un premier village ou quartier au répertoire.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Localité</th>
            <th>Type</th>
            <th>Chef Traditionnel</th>
            <th>Groupement / Canton</th>
            <th>Population</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($villages as $v)
            @php
              $vImg = $v->village_image ? (Str::startsWith($v->village_image, ['http://', 'https://']) ? $v->village_image : Storage::url($v->village_image)) : null;
              $isVillage = isset($v->is_village) ? (bool)$v->is_village : true;
            @endphp
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle overflow-hidden bg-success bg-opacity-10 d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0;">
                    @if($vImg)
                      <img src="{{ $vImg }}" alt="{{ $v->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas {{ $isVillage ? 'fa-tree text-success' : 'fa-city text-info' }}"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6 mb-0">{{ $v->name }}</div>
                    <small class="text-muted">ID: #{{ $v->id }}</small>
                  </div>
                </div>
              </td>
              <td>
                @if($isVillage)
                  <span class="badge bg-success bg-opacity-10 text-success fw-bold"><i class="fas fa-tree me-1"></i> Village</span>
                @else
                  <span class="badge bg-info bg-opacity-10 text-info fw-bold"><i class="fas fa-city me-1"></i> Quartier</span>
                @endif
              </td>
              <td>
                @if($isVillage)
                  @if($v->chef_village)
                    <span class="badge bg-warning bg-opacity-10 text-dark text-wrap text-start" style="white-space: normal; max-width: 200px;"><i class="fas fa-crown me-1 text-warning"></i> {{ $v->chef_village }}</span>
                  @else
                    <span class="text-muted small">Non renseigné</span>
                  @endif
                @else
                  <span class="badge bg-secondary bg-opacity-10 text-muted" title="Prend par défaut le chef du groupement"><i class="fas fa-shield-alt me-1"></i> Chef du Canton</span>
                @endif
              </td>
              <td>
                <span class="badge bg-secondary bg-opacity-20 text-main">{{ $v->villageGroup->name ?? '-' }}</span>
              </td>
              <td class="fw-semibold text-success small">
                {{ $v->population ? number_format($v->population) . ' hab.' : '-' }}
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-circle" style="width:34px;height:34px;padding:0;" title="Voir" onclick="openShowVillage({{ json_encode($v) }})">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-circle" style="width:34px;height:34px;padding:0;" title="Éditer" onclick="openEditVillage({{ json_encode($v) }})">
                    <i class="fas fa-pen"></i>
                  </button>
                  <form action="{{ route('dashboard.villages.destroy', $v->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmer la suppression ?');">
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

    @if($villages->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $villages->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Village / Quartier -->
<div class="modal fade" id="modalCreateVillage" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-plus-circle text-success me-2"></i> Ajouter un Village ou Quartier</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.villages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Type de Localité <span class="text-danger">*</span></label>
              <select name="is_village" id="create_is_village" class="form-select py-2 fw-bold text-success" required>
                <option value="1" selected>Village (avec chef traditionnel)</option>
                <option value="0">Quartier (sans chef propre, sous l'autorité du groupement)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom de la Localité <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Village Hsem / Quartier Tsen..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Groupement / Canton <span class="text-danger">*</span></label>
              <select name="village_group_id" class="form-select py-2" required>
                <option value="">Sélectionner un groupement...</option>
                @foreach($allGroupements as $g)
                  <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Population estimée</label>
              <input type="number" name="population" class="form-control py-2" placeholder="Ex: 15000">
            </div>

            <!-- Wrapper for Chef fields (only shown for Village) -->
            <div id="create_chef_fields_wrapper" class="row g-3 col-12 m-0 p-0">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-muted">Nom du Chef du Village</label>
                <input type="text" name="chef_village" id="create_chef_village" class="form-control py-2" placeholder="Nom du Chef...">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-muted">Photo du Chef</label>
                <input type="file" name="chief_image" id="create_chief_image" class="form-control py-2" accept="image/*">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Photo de Couverture</label>
              <input type="file" name="village_image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Photos Carrousel (Max 4)</label>
              <input type="file" name="carousel_images[]" class="form-control py-2" accept="image/*" multiple>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description / Présentation</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Présentation de la localité..."></textarea>
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

<!-- Modal Edit Village / Quartier -->
<div class="modal fade" id="modalEditVillage" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer la Localité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditVillage" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Type de Localité <span class="text-danger">*</span></label>
              <select name="is_village" id="edit_is_village" class="form-select py-2 fw-bold text-success" required>
                <option value="1">Village (avec chef traditionnel)</option>
                <option value="0">Quartier (sans chef propre, sous l'autorité du groupement)</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom de la Localité <span class="text-danger">*</span></label>
              <input type="text" id="edit_name" name="name" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Groupement / Canton <span class="text-danger">*</span></label>
              <select id="edit_village_group_id" name="village_group_id" class="form-select py-2" required>
                @foreach($allGroupements as $g)
                  <option value="{{ $g->id }}">{{ $g->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Population estimée</label>
              <input type="number" id="edit_population" name="population" class="form-control py-2">
            </div>

            <!-- Wrapper for Edit Chef fields -->
            <div id="edit_chef_fields_wrapper" class="row g-3 col-12 m-0 p-0">
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-muted">Chef du Village</label>
                <input type="text" id="edit_chef" name="chef_village" class="form-control py-2">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Photo Chef</label>
                <input type="file" name="chief_image" id="edit_chief_image" class="form-control py-2" accept="image/*">
              </div>
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Photo Couverture</label>
              <input type="file" name="village_image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Ajouter Photos Carrousel (Max 4)</label>
              <input type="file" name="carousel_images[]" class="form-control py-2" accept="image/*" multiple>
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Village / Quartier -->
<div class="modal fade" id="modalShowVillage" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-info-circle text-info me-2"></i> Détails de la Localité</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="show_name" class="fw-bold font-serif text-success mb-1"></h4>
        <span id="show_type_badge" class="badge mb-3"></span>
        <div class="p-3 bg-body-tertiary rounded text-start mb-3">
          <div class="small text-muted mb-1">Rattachement : <strong id="show_group" class="text-main"></strong></div>
          <div class="small text-muted mb-1">Chef : <strong id="show_chef" class="text-warning"></strong></div>
          <div class="small text-muted mb-1">Population estimée : <strong id="show_pop" class="text-success"></strong></div>
          <div class="small text-muted mt-2">Description : <p id="show_desc" class="mb-0 mt-1 text-main"></p></div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  $(document).ready(function() {
    // Dynamic toggle for Create Modal
    $('#create_is_village').on('change', function() {
      if ($(this).val() == '0') {
        $('#create_chef_fields_wrapper').slideUp(200);
        $('#create_chef_village').val('');
        $('#create_chief_image').val('');
      } else {
        $('#create_chef_fields_wrapper').slideDown(200);
      }
    });

    // Dynamic toggle for Edit Modal
    $('#edit_is_village').on('change', function() {
      if ($(this).val() == '0') {
        $('#edit_chef_fields_wrapper').slideUp(200);
        $('#edit_chef').val('');
        $('#edit_chief_image').val('');
      } else {
        $('#edit_chef_fields_wrapper').slideDown(200);
      }
    });
  });

  function openEditVillage(v) {
    $('#formEditVillage').attr('action', '/dashboard/villages/' + v.id);
    $('#edit_name').val(v.name);
    $('#edit_groupement_id').val(v.village_group_id);

    const isVillage = (v.is_village !== undefined && v.is_village !== null) ? v.is_village : 1;
    $('#edit_is_village').val(isVillage.toString());

    if (isVillage == 0) {
      $('#edit_chef_fields_wrapper').hide();
      $('#edit_chef').val('');
    } else {
      $('#edit_chef_fields_wrapper').show();
      $('#edit_chef').val(v.chef_village || '');
    }

    $('#edit_pop').val(v.population || '');
    $('#edit_description').val(v.description || '');
    new bootstrap.Modal(document.getElementById('modalEditVillage')).show();
  }

  function openShowVillage(v) {
    $('#show_name').text(v.name);
    const isVillage = (v.is_village !== undefined && v.is_village !== null) ? v.is_village : 1;
    
    if (isVillage == 1) {
      $('#show_type_badge').attr('class', 'badge bg-success bg-opacity-10 text-success fw-bold').html('<i class="fas fa-tree me-1"></i> Village');
      $('#show_chef').text(v.chef_village || 'Non renseigné');
    } else {
      $('#show_type_badge').attr('class', 'badge bg-info bg-opacity-10 text-info fw-bold').html('<i class="fas fa-city me-1"></i> Quartier');
      $('#show_chef').text('Prend le chef du Canton (' + (v.village_group ? (v.village_group.chef_groupement || 'Chef du canton') : 'Chef du canton') + ')');
    }

    $('#show_group').text(v.village_group ? v.village_group.name : '-');
    $('#show_pop').text(v.population ? v.population.toLocaleString() + ' hab.' : 'Non renseigné');
    $('#show_desc').text(v.description || 'Aucune description.');
    new bootstrap.Modal(document.getElementById('modalShowVillage')).show();
  }
</script>
@endsection