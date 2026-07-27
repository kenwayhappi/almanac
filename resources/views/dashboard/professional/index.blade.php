@extends('layouts.dashboard')

@section('title', 'Artisans & Pros - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-briefcase text-success me-2"></i> Artisans & Professionnels</h2>
    <p class="text-muted small mb-0">Gestion des acteurs économiques et métiers du secteur local au Cameroun.</p>
  </div>

  <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateProfessional">
    <i class="fas fa-plus me-1"></i> Ajouter un Artisan
  </button>
</div>

<!-- Search & Filter Card -->
<div class="admin-card mb-4 p-3">
  <form action="{{ route('dashboard.professional.index') }}" method="GET" class="row g-2 align-items-center">
    <div class="col-md-8 col-lg-9">
      <div class="input-group">
        <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
        <input type="text" name="search" class="form-control border-start-0" placeholder="Rechercher un artisan par nom ou profession..." value="{{ request('search') }}">
      </div>
    </div>
    <div class="col-md-4 col-lg-3 d-flex gap-2">
      <button type="submit" class="btn btn-success flex-grow-1 rounded-pill"><i class="fas fa-search me-1"></i> Rechercher</button>
      @if(request('search'))
        <a href="{{ route('dashboard.professional.index') }}" class="btn btn-outline-secondary rounded-pill" title="Réinitialiser"><i class="fas fa-undo"></i></a>
      @endif
    </div>
  </form>
</div>

<div class="admin-card">
  @if($professionals->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-briefcase fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucun artisan répertorié</h4>
      <p class="text-muted">Inscrivez un premier artisan ou professionnel.</p>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Artisan / Entreprise</th>
            <th>Profession / Métier</th>
            <th>Village</th>
            <th>Contact</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($professionals as $pro)
            <tr>
              <td>
                <div class="d-flex align-items-center gap-3">
                  <div class="rounded-circle overflow-hidden bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                    @if($pro->image)
                      <img src="{{ \App\Helpers\CloudinaryHelper::url($pro->image) }}" alt="{{ $pro->name }}" class="w-100 h-100" style="object-fit:cover;">
                    @else
                      <i class="fas fa-briefcase fs-5"></i>
                    @endif
                  </div>
                  <div>
                    <div class="fw-bold font-serif fs-6">{{ $pro->name }}</div>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-info bg-opacity-10 text-info fw-bold">{{ $pro->profession ?? 'Artisan' }}</span></td>
              <td><span class="badge bg-secondary bg-opacity-20 text-main">{{ $pro->village->name ?? '-' }}</span></td>
              <td class="small text-muted">{{ $pro->contact ?? '-' }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2 py-1 small" onclick="openShowProfessional({{ json_encode($pro) }})" title="Voir les détails">
                    <i class="fas fa-eye me-1"></i> Voir
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 small" onclick="openEditProfessional({{ json_encode($pro) }})" title="Éditer">
                    <i class="fas fa-pen"></i>
                  </button>
                  <form action="{{ route('dashboard.professional.destroy', $pro->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cet artisan ?');">
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

    @if($professionals->hasPages())
      <div class="d-flex justify-content-center mt-4">
        {{ $professionals->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Create Professional -->
<div class="modal fade" id="modalCreateProfessional" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-briefcase text-success me-2"></i> Nouvel Artisan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.professional.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom / Raison Sociale <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Atelier Sculpteur..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Village <span class="text-danger">*</span></label>
              <select name="village_id" class="form-select py-2" required>
                <option value="">Sélectionner un village...</option>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Profession / Métier <span class="text-danger">*</span></label>
              <input type="text" name="profession" class="form-control py-2" placeholder="Ex: Sculpteur, Tisserand..." required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Contact / Téléphone</label>
              <input type="text" name="contact" class="form-control py-2" placeholder="+237...">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Photo / Logo</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description des Services</label>
              <textarea name="description" class="form-control" rows="3" placeholder="Services proposés..."></textarea>
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

<!-- Modal Edit Professional -->
<div class="modal fade" id="modalEditProfessional" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-pen text-warning me-2"></i> Éditer l'Artisan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditProfessional" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom / Raison Sociale</label>
              <input type="text" id="pro_edit_name" name="name" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Village</label>
              <select id="pro_edit_village_id" name="village_id" class="form-select py-2" required>
                @foreach($villages as $v)
                  <option value="{{ $v->id }}">{{ $v->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Profession</label>
              <input type="text" id="pro_edit_profession" name="profession" class="form-control py-2" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Contact</label>
              <input type="text" id="pro_edit_contact" name="contact" class="form-control py-2">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Remplacer Photo</label>
              <input type="file" name="image" class="form-control py-2" accept="image/*">
            </div>
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Description</label>
              <textarea id="pro_edit_desc" name="description" class="form-control" rows="3"></textarea>
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

<!-- Modal Show Professional -->
<div class="modal fade" id="modalShowProfessional" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-briefcase text-info me-2"></i> Fiche d'Artisan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <h4 id="pro_show_name" class="fw-bold font-serif text-success mb-1"></h4>
        <span id="pro_show_profession" class="badge bg-info bg-opacity-10 text-info fw-bold mb-3 px-3 py-2"></span>
        <p id="pro_show_village" class="text-muted small mb-2"></p>
        <p id="pro_show_contact" class="text-success small mb-2"></p>
        <p id="pro_show_desc" class="text-main small px-3"></p>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openEditProfessional(pro) {
    $('#formEditProfessional').attr('action', '/dashboard/professional/' + pro.id);
    $('#pro_edit_name').val(pro.name);
    $('#pro_edit_village_id').val(pro.village_id);
    $('#pro_edit_profession').val(pro.profession || '');
    $('#pro_edit_contact').val(pro.contact || '');
    $('#pro_edit_desc').val(pro.description || '');
    new bootstrap.Modal(document.getElementById('modalEditProfessional')).show();
  }

  function openShowProfessional(pro) {
    $('#pro_show_name').text(pro.name);
    $('#pro_show_profession').text(pro.profession || 'Artisan');
    $('#pro_show_village').text('Village: ' + (pro.village ? pro.village.name : '-'));
    $('#pro_show_contact').text('Contact: ' + (pro.contact || 'Non spécifié'));
    $('#pro_show_desc').text(pro.description || 'Aucune description.');
    new bootstrap.Modal(document.getElementById('modalShowProfessional')).show();
  }
</script>
@endsection