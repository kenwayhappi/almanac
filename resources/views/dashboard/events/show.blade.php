@extends('layouts.dashboard')

@section('title', $event->name . ' - Cotisations & Détails - Almanac Admin')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <div class="d-flex align-items-center gap-2 mb-1">
      <a href="{{ route('dashboard.events.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Retour"><x-icon name="arrow-left" /></a>
      <h2 class="fw-bold font-serif mb-0"><x-icon name="calendar-alt" class="text-success me-2" /> {{ $event->name }}</h2>
    </div>
    <p class="text-muted small mb-0">Détails de l'événement, suivi des cotisations et exportation du rapport PDF.</p>
  </div>

  <div class="d-flex gap-2 flex-wrap">
    <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddContribution">
      <x-icon name="hand-holding-usd" class="me-1" /> Ajouter une Cotisation
    </button>
    <a href="{{ route('dashboard.events.contributions.pdf', $event->id) }}" class="btn btn-outline-danger rounded-pill px-4" target="_blank">
      <x-icon name="file-pdf" class="me-1" /> Exporter PDF
    </a>
  </div>
</div>

<!-- Event Info Summary Card -->
<div class="admin-card mb-4 p-4">
  <div class="row align-items-center g-4">
    <div class="col-md-3 col-lg-2 text-center text-md-start">
      <div class="rounded-3 overflow-hidden bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center mx-auto mx-md-0" style="width:120px;height:120px;">
        @if($event->image)
          <img src="{{ \App\Helpers\CloudinaryHelper::url($event->image) }}" alt="{{ $event->name }}" class="w-100 h-100" style="object-fit:cover;">
        @else
          <x-icon name="calendar-day" class="fs-1" />
        @endif
      </div>
    </div>
    <div class="col-md-9 col-lg-10">
      <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
        <span class="badge bg-warning bg-opacity-20 text-dark fw-bold px-3 py-2 text-uppercase">{{ $event->type }}</span>
        <span class="badge bg-success bg-opacity-20 text-success fw-bold px-3 py-2"><x-icon name="map-marker-alt" class="me-1" /> {{ $event->village->name ?? 'Village non spécifié' }}</span>
      </div>
      <h3 class="fw-bold font-serif mb-2">{{ $event->name }}</h3>
      <p class="text-muted mb-3">{{ $event->description ?: 'Aucune description spécifique renseignée pour cet événement.' }}</p>
      
      <div class="d-flex flex-wrap gap-4 text-muted small">
        <div><x-icon name="calendar-check" class="text-success me-1" /> <strong>Début :</strong> {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') : '-' }}</div>
        <div><x-icon name="calendar-times" class="text-danger me-1" /> <strong>Fin :</strong> {{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('d/m/Y') : '-' }}</div>
        <div><x-icon name="coins" class="text-warning me-1" /> <strong>Total Cotisations :</strong> <span class="fw-bold text-success fs-6">{{ number_format($contributions->sum('amount'), 0, ',', ' ') }} FCFA</span></div>
      </div>
    </div>
  </div>
</div>

<!-- Contributions / Cotisations Table Card -->
<div class="admin-card">
  <div class="d-flex justify-content-between align-items-center p-3 border-bottom border-secondary border-opacity-10">
    <h5 class="fw-bold font-serif mb-0"><x-icon name="list-alt" class="text-success me-2" /> Liste des Cotisations & Contributions</h5>
    <span class="badge bg-primary rounded-pill px-3 py-2">{{ $contributions->total() }} contribution(s)</span>
  </div>

  @if($contributions->isEmpty())
    <div class="text-center py-5">
      <x-icon name="hand-holding-usd" class="fs-1 text-muted opacity-50 mb-3" />
      <h4 class="fw-bold">Aucune cotisation enregistrée</h4>
      <p class="text-muted mb-3">Cliquez sur le bouton ci-dessus pour ajouter la première cotisation pour cet événement.</p>
      <button type="button" class="btn btn-sm btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#modalAddContribution">
        <x-icon name="plus" class="me-1" /> Ajouter une Cotisation
      </button>
    </div>
  @else
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Nom du Contributeur</th>
            <th>Type</th>
            <th>Montant (FCFA)</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($contributions as $c)
            <tr>
              <td>
                <div class="fw-bold font-serif">{{ $c->name }}</div>
              </td>
              <td>
                @if($c->contributor_type === 'association')
                  <span class="badge bg-info bg-opacity-10 text-info fw-bold"><x-icon name="users" class="me-1" /> Association / Élites</span>
                @else
                  <span class="badge bg-secondary bg-opacity-20 text-main fw-bold"><x-icon name="user" class="me-1" /> Personne Physique</span>
                @endif
              </td>
              <td>
                <span class="fw-bold text-success fs-6">{{ number_format($c->amount, 0, ',', ' ') }} FCFA</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('dashboard.events.contributions.edit', [$event->id, $c->id]) }}" class="btn btn-sm btn-outline-warning rounded-pill px-2 py-1 small" title="Éditer">
                    <x-icon name="pen" />
                  </a>
                  <form action="{{ route('dashboard.events.contributions.destroy', [$event->id, $c->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette cotisation ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2 py-1 small" title="Supprimer">
                      <x-icon name="trash" />
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($contributions->hasPages())
      <div class="d-flex justify-content-center p-3 border-top border-secondary border-opacity-10">
        {{ $contributions->links() }}
      </div>
    @endif
  @endif
</div>

<!-- Modal Add Contribution -->
<div class="modal fade" id="modalAddContribution" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content admin-card border-0">
      <div class="modal-header border-bottom border-secondary border-opacity-10">
        <h5 class="modal-title fw-bold font-serif"><x-icon name="hand-holding-usd" class="text-success me-2" /> Nouvelle Cotisation</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('dashboard.events.contributions.store', $event->id) }}" method="POST">
        @csrf
        <div class="modal-body py-4">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-bold text-uppercase text-muted">Nom du Contributeur <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control py-2" placeholder="Ex: Jean-Paul Mbouda ou Comité Bafang" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Type de Contributeur <span class="text-danger">*</span></label>
              <select name="contributor_type" class="form-select py-2" required>
                <option value="person">Personne Physique</option>
                <option value="association">Association / Élites</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-bold text-uppercase text-muted">Montant (FCFA) <span class="text-danger">*</span></label>
              <input type="number" name="amount" class="form-control py-2" placeholder="Ex: 50000" min="0" step="500" required>
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
@endsection
