<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contributions pour {{ $event->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20mm;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #4caf50;
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .total {
            margin-top: 20px;
            font-size: 16px;
            font-weight: bold;
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <h1>Contributions pour l'événement "{{ $event->name }}"</h1>
    <h2>Village: {{ $event->village->name ?? 'Non spécifié' }}</h2>

    @if($contributions->isEmpty())
        <p style="text-align: center; color: #777;">Aucune contribution enregistrée pour cet événement.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Type</th>
                    <th>Montant (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contributions as $contribution)
                    <tr>
                        <td>{{ $contribution->name ?? 'Inconnu' }}</td>
                        <td>{{ $contribution->contributor_type === 'person' ? 'Personne' : 'Association' }}</td>
                        <td>{{ number_format($contribution->amount, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total">
            Montant total: {{ number_format($totalAmount, 0, ',', ' ') }} FCFA
        </div>
    @endif

    <div class="footer">
        Généré le {{ now()->format('d/m/Y') }}
    </div>
</body>
</html>
