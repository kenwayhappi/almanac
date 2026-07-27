@extends('layouts.dashboard')

@section('content')
<div class="container py-5 mt-4">
    <h1 class="mb-4 fw-bold text-primary"><i class="fas fa-edit me-2"></i> Modifier la Contribution</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="bg-white p-4 rounded-3 shadow-sm">
        <h3>Modifier la Contribution pour {{ $event->name }}</h3>
        <form action="{{ route('dashboard.events.contributions.update', [$event->id, $contribution->id]) }}" method="POST" class="row g-3">
            @csrf
            @method('PUT')

            <div class="col-md-4">
                <label for="contributor_type" class="form-label fw-semibold">Type de contributeur <span class="text-danger">*</span></label>
                <select class="form-control" id="contributor_type" name="contributor_type" required>
                    <option value="person" {{ old('contributor_type', $contribution->contributor_type) == 'person' ? 'selected' : '' }}>Personne</option>
                    <option value="association" {{ old('contributor_type', $contribution->contributor_type) == 'association' ? 'selected' : '' }}>Association</option>
                </select>
                @error('contributor_type')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="name" class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $contribution->name) }}" required>
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-4">
                <label for="amount" class="form-label fw-semibold">Montant (€) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount', $contribution->amount) }}" required>
                @error('amount')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-12 d-flex justify-content-between mt-4">
                <a href="{{ route('dashboard.events.show', $event->id) }}" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> Annuler
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-save me-2"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-primary {
        background-color: #2e7d32;
        border-color: #2e7d32;
    }
    .btn-primary:hover {
        background-color: #1b5e20;
        border-color: #1b5e20;
    }
    .text-primary {
        color: #2e7d32 !important;
    }
</style>
@endsection
