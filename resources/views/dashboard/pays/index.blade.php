@extends('layouts.dashboard')

@section('title', 'Cameroun & Divisions Administratives - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1"><i class="fas fa-globe text-success me-2"></i> Cameroun & Découpages Administratifs</h2>
    <p class="text-muted small mb-0">Gestion du territoire national du Cameroun (+237) et de ses découpages (Régions, Départements, Arrondissements).</p>
  </div>
</div>

<div class="admin-card">
  @if($countries->isEmpty())
    <div class="text-center py-5">
      <i class="fas fa-globe fs-1 text-muted opacity-50 mb-3"></i>
      <h4 class="fw-bold">Aucun pays répertorié</h4>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Indicatif</th>
            <th>Pays</th>
            <th>Code ISO</th>
            <th>Arborescence des Divisions</th>
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
                  <span class="badge bg-success bg-opacity-10 text-success me-1">{{ implode(' ➔ ', $types) }}</span>
                @else
                  <span class="text-muted small">Aucun type</span>
                @endif
              </td>
              <td class="fw-bold">{{ number_format($country->administrative_divisions_count ?? 0) }}</td>
              <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="openShowCountry({{ json_encode($country) }})">
                  <i class="fas fa-eye me-1"></i> Voir l'Arborescence
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>

<!-- Modal Show Country -->
<div class="modal fade" id="modalShowCountry" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><i class="fas fa-globe text-success me-2"></i> Territoire du Cameroun</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body py-4">
        <div class="row g-3 text-center mb-4">
          <div class="col-4"><small class="text-muted d-block">Indicatif</small><strong class="fs-5 text-success">+237</strong></div>
          <div class="col-4"><small class="text-muted d-block">Pays</small><strong class="fs-5">Cameroun</strong></div>
          <div class="col-4"><small class="text-muted d-block">ISO</small><span class="badge bg-secondary bg-opacity-20 text-main fs-6">CM</span></div>
        </div>
        <h6 class="fw-bold font-serif mb-2 text-start">Divisions Administratives :</h6>
        <div class="p-3 bg-body-tertiary rounded text-start small">
          <strong>Niveau 1 :</strong> Régions (Adamaoua, Centre, Est, Littoral, Nord, Nord-Ouest, Ouest, Sud, Sud-Ouest, Extrême-Nord)<br>
          <strong>Niveau 2 :</strong> Départements<br>
          <strong>Niveau 3 :</strong> Arrondissements / Groupements
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function openShowCountry(c) {
    new bootstrap.Modal(document.getElementById('modalShowCountry')).show();
  }
</script>
@endsection