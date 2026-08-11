<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AdvertisementDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Advertisement::query();
            if ($request->has('search') && !empty($request->search)) {
                $query->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('owner_name', 'like', '%' . $request->search . '%');
            }
            $advertisements = $query->paginate(9)->withQueryString();
            $advertisements->getCollection()->transform(function ($advertisement) {
                $advertisement->file_url = $advertisement->file_path
                    ? CloudinaryHelper::url($advertisement->file_path, $advertisement->type)
                    : null;
                return $advertisement;
            });

            return view('dashboard.advertisements.index', compact('advertisements'));
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des publicités', ['message' => $e->getMessage()]);
            return view('dashboard.advertisements.index', ['advertisements' => collect([])])
                ->with('error', 'Erreur lors du chargement des publicités.');
        }
    }

    public function create()
    {
        try {
            return view('dashboard.advertisements.create');
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la page de création', ['message' => $e->getMessage()]);
            return redirect()->route('dashboard.advertisements.index')
                ->with('error', 'Impossible de charger la page de création.');
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:video,photo,audio,pdf,text',
            'position' => 'required|in:accueil,rechercher',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',
            'owner_contact' => 'nullable|string|max:20',
            'owner_name' => 'nullable|string|max:100',
            'file' => [
                'nullable', 'file', 'max:15360', // 15MB max pour éviter les erreurs Nginx 413
                function ($attribute, $value, $fail) use ($request) {
                    $type = $request->input('type');
                    $mimes = [
                        'video' => ['mp4', 'mpeg', 'avi', 'mov', 'webm'],
                        'photo' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                        'audio' => ['mp3', 'wav', 'ogg'],
                        'pdf' => ['pdf'],
                        'text' => null,
                    ];
                    $allowedExtensions = $mimes[$type] ?? [];
                    if ($type === 'text' && $request->hasFile('file')) {
                        $fail('Aucun fichier ne doit être fourni pour le type "text".');
                    } elseif ($type !== 'text' && !$request->hasFile('file')) {
                        $fail('Un fichier est requis pour ce type.');
                    } elseif ($type !== 'text' && !in_array(strtolower($value->getClientOriginalExtension()), $allowedExtensions)) {
                        $fail("Le fichier doit être de type : " . implode(', ', $allowedExtensions) . ".");
                    }
                },
            ],
            'content' => 'nullable|string|required_if:type,text|max:100000',
        ], [
            'file.max' => 'Le fichier est trop volumineux (15 Mo maximum autorisé pour éviter les erreurs de serveur).',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée pour store publicité', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $advertisement = new Advertisement();
            $advertisement->title = $request->title;
            $advertisement->type = $request->type;
            $advertisement->position = $request->position;
            $advertisement->end_date = $request->end_date;
            $advertisement->end_time = $request->end_time;
            $advertisement->owner_contact = $request->owner_contact;
            $advertisement->owner_name = $request->owner_name;

            if ($request->type !== 'text' && $request->hasFile('file')) {
                $publicId = CloudinaryHelper::upload($request->file('file'), 'advertisements');
                $advertisement->file_path = $publicId;
            } elseif ($request->type === 'text') {
                $advertisement->content = $request->content;
            }

            $advertisement->save();

            return redirect()->route('dashboard.advertisements.index')
                ->with('success', 'Publicité créée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création de la publicité', ['message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la création de la publicité.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $advertisement = Advertisement::findOrFail($id);
            $advertisement = [
                'id' => $advertisement->id,
                'title' => $advertisement->title,
                'type' => $advertisement->type,
                'position' => $advertisement->position,
                'views' => $advertisement->views,
                'end_date' => $advertisement->end_date,
                'end_time' => $advertisement->end_time,
                'owner_contact' => $advertisement->owner_contact,
                'owner_name' => $advertisement->owner_name,
                'file_path' => $advertisement->file_path,
                'file_url' => $advertisement->file_path ? CloudinaryHelper::url($advertisement->file_path, $advertisement->type) : null,
                'content' => $advertisement->content,
                'created_at' => $advertisement->created_at,
                'updated_at' => $advertisement->updated_at,
            ];

            return view('dashboard.advertisements.show', compact('advertisement'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la publicité', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('dashboard.advertisements.index')
                ->with('error', "Publicité non trouvée (ID: $id)");
        }
    }

    public function edit($id)
    {
        try {
            $advertisement = Advertisement::findOrFail($id);
            $advertisement = [
                'id' => $advertisement->id,
                'title' => $advertisement->title,
                'type' => $advertisement->type,
                'position' => $advertisement->position,
                'end_date' => $advertisement->end_date,
                'end_time' => $advertisement->end_time,
                'owner_contact' => $advertisement->owner_contact,
                'owner_name' => $advertisement->owner_name,
                'file_path' => $advertisement->file_path,
                'file_url' => $advertisement->file_path ? CloudinaryHelper::url($advertisement->file_path, $advertisement->type) : null,
                'content' => $advertisement->content,
            ];

            return view('dashboard.advertisements.edit', compact('advertisement'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la publicité pour édition', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->route('dashboard.advertisements.index')
                ->with('error', "Publicité non trouvée (ID: $id)");
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:video,photo,audio,pdf,text',
            'position' => 'required|in:accueil,rechercher',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',
            'owner_contact' => 'nullable|string|max:20',
            'owner_name' => 'nullable|string|max:100',
            'file' => [
                'nullable', 'file', 'max:15360', // 15MB max
                function ($attribute, $value, $fail) use ($request) {
                    $type = $request->input('type');
                    $mimes = [
                        'video' => ['mp4', 'mpeg', 'avi', 'mov', 'webm'],
                        'photo' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                        'audio' => ['mp3', 'wav', 'ogg'],
                        'pdf' => ['pdf'],
                        'text' => null,
                    ];
                    $allowedExtensions = $mimes[$type] ?? [];
                    if ($type === 'text' && $request->hasFile('file')) {
                        $fail('Aucun fichier ne doit être fourni pour le type "text".');
                    } elseif ($type !== 'text' && !in_array(strtolower($value->getClientOriginalExtension()), $allowedExtensions)) {
                        $fail("Le fichier doit être de type : " . implode(', ', $allowedExtensions) . ".");
                    }
                },
            ],
            'content' => 'nullable|string|required_if:type,text|max:100000',
        ], [
            'file.max' => 'Le fichier est trop volumineux (15 Mo maximum autorisé pour éviter les erreurs de serveur).',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation échouée pour update publicité', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $advertisement = Advertisement::findOrFail($id);
            $advertisement->title = $request->title;
            $advertisement->type = $request->type;
            $advertisement->position = $request->position;
            $advertisement->end_date = $request->end_date;
            $advertisement->end_time = $request->end_time;
            $advertisement->owner_contact = $request->owner_contact;
            $advertisement->owner_name = $request->owner_name;

            if ($request->type !== 'text' && $request->hasFile('file')) {
                CloudinaryHelper::delete($advertisement->file_path);
                $publicId = CloudinaryHelper::upload($request->file('file'), 'advertisements');
                $advertisement->file_path = $publicId;
            } elseif ($request->type === 'text') {
                $advertisement->content = $request->content;
                CloudinaryHelper::delete($advertisement->file_path);
                $advertisement->file_path = null;
            }

            $advertisement->save();

            return redirect()->route('dashboard.advertisements.index')
                ->with('success', 'Publicité mise à jour avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour de la publicité', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la mise à jour de la publicité.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $advertisement = Advertisement::findOrFail($id);

            CloudinaryHelper::delete($advertisement->file_path);
            Log::info('Fichier publicité supprimé de Cloudinary', ['public_id' => $advertisement->file_path]);

            $advertisement->delete();

            return redirect()->route('dashboard.advertisements.index')
                ->with('success', 'Publicité supprimée avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la publicité', ['id' => $id, 'message' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression de la publicité.');
        }
    }
}