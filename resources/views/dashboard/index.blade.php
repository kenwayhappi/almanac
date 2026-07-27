@extends('layouts.dashboard')

@section('title', 'Tableau de Bord Admin - Almanac')

@section('content')
<!-- Header Page Title -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
  <div>
    <h2 class="fw-bold font-serif mb-1">Tableau de Bord</h2>
    <p class="text-muted small mb-0">Vue d'ensemble des statistiques, territoires, publicités et activités récentes.</p>
  </div>

  <div class="d-flex gap-2">
    <a href="{{ route('dashboard.villages.create') }}" class="btn btn-sm btn-success rounded-pill px-3">
      <i class="fas fa-plus me-1"></i> Nouveau Village
    </a>
    <a href="{{ route('dashboard.advertisements.create') }}" class="btn btn-sm btn-outline-success rounded-pill px-3">
      <i class="fas fa-ad me-1"></i> Nouvelle Pub
    </a>
  </div>
</div>

<!-- KPI Cards Row -->
<div class="row g-3 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-icon bg-success bg-opacity-10 text-success">
        <i class="fas fa-tree"></i>
      </div>
      <div>
        <h3 class="fw-bold mb-0">{{ number_format($villageCount ?? 0) }}</h3>
        <span class="text-muted small fw-semibold">Villages Enregistrés</span>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-icon bg-primary bg-opacity-10 text-primary">
        <i class="fas fa-layer-group"></i>
      </div>
      <div>
        <h3 class="fw-bold mb-0">{{ number_format($groupementCount ?? 0) }}</h3>
        <span class="text-muted small fw-semibold">Groupements / Cantons</span>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
        <i class="fas fa-ad"></i>
      </div>
      <div>
        <h3 class="fw-bold mb-0">{{ number_format($advertisementCount ?? 0) }}</h3>
        <span class="text-muted small fw-semibold">Publicités Actives</span>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="kpi-card">
      <div class="kpi-icon bg-info bg-opacity-10 text-info">
        <i class="fas fa-eye"></i>
      </div>
      <div>
        <h3 class="fw-bold mb-0">{{ number_format($totalAdViews ?? 0) }}</h3>
        <span class="text-muted small fw-semibold">Vues Machine Uniques</span>
      </div>
    </div>
  </div>
</div>

<!-- Interactive Charts Row -->
<div class="row g-4 mb-4">
  <!-- Bar Chart: Villages per Groupement -->
  <div class="col-lg-8">
    <div class="admin-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="fw-bold mb-1"><i class="fas fa-chart-bar text-success me-2"></i> Top Groupements par Nombres de Villages</h5>
          <small class="text-muted">Répartition des villages traditionnels par canton</small>
        </div>
        <span class="badge bg-success bg-opacity-10 text-success">Statistiques</span>
      </div>
      <div style="height: 300px; position: relative;">
        <canvas id="groupementsBarChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Doughnut Chart: Advertisements by Type -->
  <div class="col-lg-4">
    <div class="admin-card h-100">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h5 class="fw-bold mb-1"><i class="fas fa-chart-pie text-warning me-2"></i> Publicités par Format</h5>
          <small class="text-muted">Vidéo, Photo, PDF, Texte</small>
        </div>
      </div>
      <div style="height: 250px; position: relative;" class="d-flex justify-content-center align-items-center">
        <canvas id="adsTypeDoughnutChart"></canvas>
      </div>
    </div>
  </div>
</div>

<!-- Summary Tables Row -->
<div class="row g-4">
  <!-- Recent Villages Table -->
  <div class="col-lg-6">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-tree text-success me-2"></i> Derniers Villages</h5>
        <a href="{{ route('dashboard.villages.index') }}" class="btn btn-sm btn-link text-success text-decoration-none">Tout voir <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Village</th>
              <th>Groupement</th>
              <th>Population</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentVillages as $v)
              <tr>
                <td>
                  <div class="fw-bold">{{ $v->name }}</div>
                  <small class="text-muted">{{ $v->chef_village ? 'Chef: '.$v->chef_village : 'Non spécifié' }}</small>
                </td>
                <td><span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $v->villageGroup->name ?? '-' }}</span></td>
                <td>{{ number_format($v->population ?? 0) }} hab.</td>
                <td class="text-end">
                  <a href="{{ route('dashboard.villages.edit', $v->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Éditer">
                    <i class="fas fa-pen"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-center text-muted py-3">Aucun village enregistré</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Recent Advertisements Table -->
  <div class="col-lg-6">
    <div class="admin-card">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0"><i class="fas fa-ad text-warning me-2"></i> Dernières Publicités</h5>
        <a href="{{ route('dashboard.advertisements.index') }}" class="btn btn-sm btn-link text-success text-decoration-none">Tout voir <i class="fas fa-arrow-right"></i></a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Type</th>
              <th>Position</th>
              <th>Vues Uniques</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($recentAds as $ad)
              <tr>
                <td class="fw-bold">{{ Str::limit($ad->title ?? 'Annonce', 20) }}</td>
                <td><span class="badge bg-info bg-opacity-10 text-info text-uppercase">{{ $ad->type }}</span></td>
                <td><span class="badge bg-dark bg-opacity-50 text-white">{{ $ad->position }}</span></td>
                <td class="fw-bold text-success"><i class="fas fa-eye me-1"></i> {{ number_format($ad->views ?? 0) }}</td>
                <td class="text-end">
                  <a href="{{ route('dashboard.advertisements.edit', $ad->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Éditer">
                    <i class="fas fa-pen"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr><td colspan="5" class="text-center text-muted py-3">Aucune publicité disponible</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // 1. Chart.js - Bar Chart for Groupements
    const groupementsData = @json($groupementsChartData);
    const ctxBar = document.getElementById('groupementsBarChart').getContext('2d');
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: groupementsData.map(d => d.label),
        datasets: [{
          label: 'Nombre de Villages',
          data: groupementsData.map(d => d.count),
          backgroundColor: 'rgba(34, 197, 94, 0.75)',
          borderColor: '#16a34a',
          borderWidth: 2,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.05)' } },
          x: { grid: { display: false } }
        }
      }
    });

    // 2. Chart.js - Doughnut Chart for Ads Types
    const adsTypeData = @json($adsTypeData);
    const ctxDoughnut = document.getElementById('adsTypeDoughnutChart').getContext('2d');
    new Chart(ctxDoughnut, {
      type: 'doughnut',
      data: {
        labels: adsTypeData.map(d => d.type),
        datasets: [{
          data: adsTypeData.map(d => d.count),
          backgroundColor: [
            '#3b82f6',
            '#22c55e',
            '#f59e0b',
            '#ec4899',
            '#8b5cf6'
          ],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'bottom', labels: { boxWidth: 12 } }
        }
      }
    });
  });
</script>
@endsection
