@extends('layouts.dashboard')

@section('title', 'Cameroun & Divisions Administratives - Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-globe text-success me-2"></i> Cameroun & Découpages Administratifs</h2>
    <p class="text-muted small mb-0">Gestion du territoire national du Cameroun (+237) et de ses découpages (Régions, Départements, Arrondissements).</p>
  </div>
  <div>
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateCountry">
      <i class="fas fa-plus-circle me-1"></i> Ajouter un Pays / Territoire
    </button>
  </div>
</div>

<div class="admin-card mb-4">
  @if($countries->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-globe fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucun pays répertorié</h4>
      <p class="text-muted small mb-3">Ajoutez le territoire national du Cameroun pour commencer la structuration.</p>
      <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalCreateCountry">
        <i class="fas fa-plus me-1"></i> Créer le Pays
      </button>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Indicatif</th>
            <th>Pays</th>
            <th>Code ISO</th>
            <th>Arborescence des Types</th>
            <th>Total Divisions</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($countries as $country)
            <tr>
              <td class="fw-bold text-success">+{{ $country->id }}</td>
              <td class="fw-bold font-serif fs-6">🇨🇲 {{ $country->name }}</td>
              <td><span class="badge bg-secondary bg-opacity-20 text-main font-monospace">{{ $country->code }}</span></td>
              <td>
                @php
                  $types = $country->administrativeDivisionTypes->pluck('name')->toArray();
                @endphp
                @if(count($types) > 0)
                  <span class="badge bg-success bg-opacity-10 text-success me-1"><i class="fas fa-sitemap me-1"></i> {{ implode(' ➔ ', $types) }}</span>
                @else
                  <span class="text-muted small">Aucun type configuré</span>
                @endif
              </td>
              <td class="fw-bold"><span class="badge bg-info bg-opacity-10 text-info px-3 py-1">{{ number_format($country->administrative_divisions_count ?? 0) }} divisions</span></td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-2 py-1 small" onclick="openTreeCountry({{ json_encode($country) }})" title="Gérer l'arborescence et les divisions">
                    <i class="fas fa-sitemap me-1"></i> Divisions
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 small" onclick="openEditCountry({{ json_encode($country) }})" title="Modifier le Pays">
                    <i class="fas fa-edit"></i>
                  </button>
                  <form action="{{ route('dashboard.pays.destroy', $country->id) }}" method="POST" class="d-inline" onsubmit="return confirmDoubleDelete('{{ addslashes($country->name) }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 small" title="Supprimer le Pays">
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

    @if($countries->hasPages())
      <div class="mt-4">
        {{ $countries->links() }}
      </div>
    @endif
  @endif
</div>

<!-- ============================================================
     MODAL — Ajouter un Pays
============================================================ -->
<div class="modal fade" id="modalCreateCountry" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content admin-card border-0 shadow-lg">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-globe text-success me-2"></i> Ajouter un NOUVEAU Pays / Territoire</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.pays.store') }}" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Indicatif Téléphonique <span class="text-danger">*</span></label>
              <input type="text" name="id" class="form-control" placeholder="ex: 237" required>
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Pays <span class="text-danger">*</span></label>
              <input type="text" name="nom" class="form-control" placeholder="ex: Cameroun" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Code ISO <span class="text-danger">*</span></label>
              <input type="text" name="code" class="form-control" placeholder="ex: CM" required>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top border-secondary border-opacity-10">
            <h6 class="fw-bold font-serif mb-2"><i class="fas fa-layer-group text-warning me-2"></i> Niveaux d'Arborescence Administrative</h6>
            <p class="text-muted small mb-3">Définissez les niveaux hiérarchiques du pays (dans l'ordre de la plus grande à la plus petite division).</p>

            <div class="row g-2 mb-2">
              <div class="col-md-4">
                <input type="text" name="divisions_types[]" class="form-control form-control-sm" value="Région" placeholder="Niveau 1 (ex: Région)" required>
              </div>
              <div class="col-md-4">
                <input type="text" name="divisions_types[]" class="form-control form-control-sm" value="Département" placeholder="Niveau 2 (ex: Département)" required>
              </div>
              <div class="col-md-4">
                <input type="text" name="divisions_types[]" class="form-control form-control-sm" value="Arrondissement" placeholder="Niveau 3 (ex: Arrondissement)" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success rounded-pill px-5"><i class="fas fa-check me-1"></i> Créer le Pays</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL — Modifier le Pays
============================================================ -->
<div class="modal fade" id="modalEditCountry" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content admin-card border-0 shadow-lg">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-edit text-warning me-2"></i> Modifier le Pays / Territoire</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditCountry" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label small fw-bold text-uppercase text-muted">Indicatif Téléphonique <span class="text-danger">*</span></label>
              <input type="text" id="editCountryId" name="id" class="form-control" required>
            </div>
            <div class="col-md-5">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Pays <span class="text-danger">*</span></label>
              <input type="text" id="editCountryName" name="nom" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label small fw-bold text-uppercase text-muted">Code ISO <span class="text-danger">*</span></label>
              <input type="text" id="editCountryCode" name="code" class="form-control" required>
            </div>
          </div>
          <div id="editTypesContainer" class="mt-4 pt-3 border-top border-secondary border-opacity-10">
            <!-- Dynamically populated with type inputs -->
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-5"><i class="fas fa-save me-1"></i> Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL — Arborescence & Gestion des Divisions
============================================================ -->
<div class="modal fade" id="modalTreeCountry" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content admin-card border-0 shadow-lg">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <div>
          <h5 class="modal-title fw-bold font-serif mb-0"><i class="fas fa-sitemap text-success me-2"></i> <span id="treeCountryTitle">Cameroun</span> — Arborescence des Divisions</h5>
          <small class="text-muted">Ajoutez, modifiez ou supprimez les Régions, Départements et Arrondissements.</small>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <!-- Action Bar -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 p-3 bg-body-tertiary rounded-3">
          <div class="input-group" style="max-width: 320px;">
            <span class="input-group-text bg-transparent border-end-0"><i class="fas fa-search text-muted"></i></span>
            <input type="text" id="searchDivisionInput" class="form-control border-start-0 ps-0" placeholder="Rechercher une division…" oninput="filterDivisionsTree()">
          </div>
          <button type="button" class="btn btn-success btn-sm rounded-pill px-4 fw-bold" onclick="openCreateDivisionModal()">
            <i class="fas fa-plus-circle me-1"></i> Ajouter une Division
          </button>
        </div>

        <!-- Tree List Container -->
        <div id="treeDivisionsList" class="d-flex flex-column gap-3">
          <!-- Populated dynamically via JS -->
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL — Ajouter une Division
============================================================ -->
<div class="modal fade" id="modalCreateDivision" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0 shadow-lg">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-plus-circle text-success me-2"></i> Ajouter une Division Administrative</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.pays.divisions.store') }}" method="POST">
        @csrf
        <input type="hidden" id="cdCountryId" name="country_id">
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Nom de la Division <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" placeholder="ex: Région de l'Ouest, Mifi, Bafoussam 1er" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Type de Division <span class="text-danger">*</span></label>
            <select id="cdTypeId" name="type_id" class="form-select" required>
              <!-- Populated dynamically -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Division Parente (Optionnel)</label>
            <select id="cdParentId" name="parent_id" class="form-select">
              <option value="">-- Aucune (Division Principale / Région) --</option>
              <!-- Populated dynamically -->
            </select>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success rounded-pill px-5"><i class="fas fa-check me-1"></i> Ajouter</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================
     MODAL — Modifier une Division
============================================================ -->
<div class="modal fade" id="modalEditDivision" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0 shadow-lg">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-edit text-warning me-2"></i> Modifier la Division</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formEditDivision" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body p-4">
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Nom de la Division <span class="text-danger">*</span></label>
            <input type="text" id="edName" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Type de Division</label>
            <select id="edTypeId" name="type_id" class="form-select">
              <!-- Populated dynamically -->
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase text-muted">Division Parente</label>
            <select id="edParentId" name="parent_id" class="form-select">
              <option value="">-- Aucune (Division Principale / Région) --</option>
              <!-- Populated dynamically -->
            </select>
          </div>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-10">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-5"><i class="fas fa-save me-1"></i> Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
let currentCountryData = null;

// ── Opening Edit Country Modal ──────────────────────────────
function openEditCountry(c) {
  document.getElementById('formEditCountry').action = `/dashboard/pays/${c.id}`;
  document.getElementById('editCountryId').value = c.id;
  document.getElementById('editCountryName').value = c.name;
  document.getElementById('editCountryCode').value = c.code;

  const container = document.getElementById('editTypesContainer');
  container.innerHTML = '<h6 class="fw-bold font-serif mb-2"><i class="fas fa-layer-group text-warning me-2"></i> Types de Divisions</h6>';

  if (c.administrative_division_types && c.administrative_division_types.length > 0) {
    let row = '<div class="row g-2 mb-2">';
    c.administrative_division_types.forEach((t, i) => {
      row += `<div class="col-md-4">
                <label class="small text-muted mb-1">Niveau ${t.level}</label>
                <input type="text" name="divisions_types[]" class="form-control form-control-sm" value="${t.name}" required>
              </div>`;
    });
    row += '</div>';
    container.innerHTML += row;
  }

  new bootstrap.Modal(document.getElementById('modalEditCountry')).show();
}

// ── Opening Tree & Divisions Modal ──────────────────────────
function openTreeCountry(c) {
  currentCountryData = c;
  document.getElementById('treeCountryTitle').textContent = c.name;

  renderDivisionsTree();
  new bootstrap.Modal(document.getElementById('modalTreeCountry')).show();
}

// ── Render Divisions Tree View ──────────────────────────────
function renderDivisionsTree(filterText = '') {
  const container = document.getElementById('treeDivisionsList');
  container.innerHTML = '';

  if (!currentCountryData || !currentCountryData.administrative_divisions || currentCountryData.administrative_divisions.length === 0) {
    container.innerHTML = `<div class="text-center py-4 text-muted">
      <i class="fas fa-folder-open fs-2 mb-2 opacity-50"></i>
      <p class="mb-0">Aucune division administrative enregistrée pour ce pays.</p>
    </div>`;
    return;
  }

  const allDivisions = currentCountryData.administrative_divisions;
  const q = filterText.toLowerCase().trim();

  // Root divisions (level 1, e.g. parent_id is null)
  const rootDivisions = allDivisions.filter(d => !d.parent_id);

  if (rootDivisions.length === 0) {
    container.innerHTML = `<div class="text-center py-4 text-muted">Aucune division principale trouvée.</div>`;
    return;
  }

  let html = '';

  rootDivisions.forEach(root => {
    const matchRoot = root.name.toLowerCase().includes(q);
    const children  = root.children || [];
    let childHtml = '';
    let hasVisibleChild = false;

    children.forEach(child => {
      const matchChild = child.name.toLowerCase().includes(q);
      const grandChildren = child.children || [];
      let grandChildHtml = '';
      let hasVisibleGrandChild = false;

      grandChildren.forEach(gc => {
        const matchGc = gc.name.toLowerCase().includes(q);
        if (!q || matchGc || matchChild || matchRoot) {
          hasVisibleGrandChild = true;
          grandChildHtml += `
            <div class="d-flex align-items-center justify-content-between p-2 rounded border border-secondary border-opacity-10 mb-1 ms-4 bg-body-tertiary">
              <span class="small fw-semibold"><i class="fas fa-map-marker-alt text-info me-1"></i> ${gc.name}</span>
              <div class="d-inline-flex gap-1">
                <button type="button" class="btn btn-xs btn-outline-warning py-0 px-2 small" onclick="openEditDivisionModal(${gc.id}, '${gc.name.replace(/'/g, "\\'")}', ${gc.type_id}, ${gc.parent_id})"><i class="fas fa-edit"></i></button>
                <form action="/dashboard/pays/divisions/${gc.id}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer la division ${gc.name.replace(/'/g, "\\'")} ?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2 small"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </div>`;
        }
      });

      if (!q || matchChild || matchRoot || hasVisibleGrandChild) {
        hasVisibleChild = true;
        childHtml += `
          <div class="p-2 border border-secondary border-opacity-10 rounded mb-2 ms-3 bg-card">
            <div class="d-flex align-items-center justify-content-between">
              <span class="fw-bold text-main small"><i class="fas fa-building text-warning me-1"></i> ${child.name}</span>
              <div class="d-inline-flex gap-1">
                <button type="button" class="btn btn-xs btn-outline-success py-0 px-2 small" onclick="openCreateDivisionModal(${child.id}, 3)"><i class="fas fa-plus me-1"></i> Sous-division</button>
                <button type="button" class="btn btn-xs btn-outline-warning py-0 px-2 small" onclick="openEditDivisionModal(${child.id}, '${child.name.replace(/'/g, "\\'")}', ${child.type_id}, ${child.parent_id})"><i class="fas fa-edit"></i></button>
                <form action="/dashboard/pays/divisions/${child.id}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer le département ${child.name.replace(/'/g, "\\'")} ?');">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2 small"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </div>
            ${grandChildHtml ? `<div class="mt-2">${grandChildHtml}</div>` : ''}
          </div>`;
      }
    });

    if (!q || matchRoot || hasVisibleChild) {
      const typeName = root.type ? root.type.name : 'Région';
      html += `
        <div class="card border border-secondary border-opacity-10 shadow-sm p-3 rounded-3">
          <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom border-secondary border-opacity-10">
            <div>
              <span class="badge bg-success bg-opacity-20 text-success me-2">${typeName}</span>
              <strong class="fs-6 font-serif">${root.name}</strong>
            </div>
            <div class="d-inline-flex gap-2">
              <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="openCreateDivisionModal(${root.id}, 2)"><i class="fas fa-plus me-1"></i> Ajouter Département</button>
              <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" onclick="openEditDivisionModal(${root.id}, '${root.name.replace(/'/g, "\\'")}', ${root.type_id}, null)"><i class="fas fa-edit me-1"></i> Modifier</button>
              <form action="/dashboard/pays/divisions/${root.id}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer la région ${root.name.replace(/'/g, "\\'")} et ses sous-divisions ?');">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fas fa-trash"></i></button>
              </form>
            </div>
          </div>
          ${childHtml ? `<div>${childHtml}</div>` : '<div class="small text-muted ps-2">Aucun département dans cette région.</div>'}
        </div>`;
    }
  });

  container.innerHTML = html || `<div class="text-center py-4 text-muted">Aucune division ne correspond à "${filterText}".</div>`;
}

function filterDivisionsTree() {
  const val = document.getElementById('searchDivisionInput').value;
  renderDivisionsTree(val);
}

// ── Open Create Division Modal ──────────────────────────────
function openCreateDivisionModal(parentId = null, targetLevel = 1) {
  if (!currentCountryData) return;

  document.getElementById('cdCountryId').value = currentCountryData.id;

  // Populate Types select
  const typeSelect = document.getElementById('cdTypeId');
  typeSelect.innerHTML = '';
  const types = currentCountryData.administrative_division_types || [];
  types.forEach(t => {
    const selected = (t.level === targetLevel) ? 'selected' : '';
    typeSelect.innerHTML += `<option value="${t.id}" ${selected}>${t.name} (Niveau ${t.level})</option>`;
  });

  // Populate Parents select
  const parentSelect = document.getElementById('cdParentId');
  parentSelect.innerHTML = '<option value="">-- Aucune (Division Principale / Région) --</option>';
  const allDivs = currentCountryData.administrative_divisions || [];
  allDivs.forEach(d => {
    const sel = (parentId && d.id === parentId) ? 'selected' : '';
    parentSelect.innerHTML += `<option value="${d.id}" ${sel}>${d.name}</option>`;
  });

  new bootstrap.Modal(document.getElementById('modalCreateDivision')).show();
}

// ── Open Edit Division Modal ────────────────────────────────
function openEditDivisionModal(id, name, typeId, parentId) {
  if (!currentCountryData) return;

  document.getElementById('formEditDivision').action = `/dashboard/pays/divisions/${id}`;
  document.getElementById('edName').value = name;

  // Types
  const typeSelect = document.getElementById('edTypeId');
  typeSelect.innerHTML = '';
  (currentCountryData.administrative_division_types || []).forEach(t => {
    const sel = (t.id === typeId) ? 'selected' : '';
    typeSelect.innerHTML += `<option value="${t.id}" ${sel}>${t.name} (Niveau ${t.level})</option>`;
  });

  // Parents
  const parentSelect = document.getElementById('edParentId');
  parentSelect.innerHTML = '<option value="">-- Aucune (Division Principale / Région) --</option>';
  (currentCountryData.administrative_divisions || []).forEach(d => {
    if (d.id !== id) {
      const sel = (parentId && d.id === parentId) ? 'selected' : '';
      parentSelect.innerHTML += `<option value="${d.id}" ${sel}>${d.name}</option>`;
    }
  });

  new bootstrap.Modal(document.getElementById('modalEditDivision')).show();
}

// ── Double Confirmation suppression Pays ────────────────────
function confirmDoubleDelete(countryName) {
  const step1 = confirm(`⚠️ ATTENTION CRITIQUE !\n\nVous êtes sur le point de SUPPRIMER le pays "${countryName}".\n\nCela va TOUT SUPPRIMER automatiquement en cascade :\n- Toutes les Régions, Départements et Arrondissements\n- Tous les Groupements et Cantons rattachés\n- Tous les Villages, Acteurs, Événements et Médias Cloudinary\n\nSouhaitez-vous CONTINUER (Étape 1/2) ?`);
  if (!step1) return false;

  const step2 = confirm(`🚨 CONFIRMATION FINALE (Étape 2/2) !\n\nCette action est TOTALEMENT IRRÉVERSIBLE.\nTout le territoire et toutes les données de "${countryName}" seront détruits définitivement.\n\nÊtes-vous ABSOLUMENT CERTAIN de vouloir TOUT SUPPRIMER ?`);
  return step2;
}
</script>
@endsection