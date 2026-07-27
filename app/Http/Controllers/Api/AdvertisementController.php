<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CloudinaryHelper;
use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdvertisementController extends Controller
{
    public function index()
    {
        $advertisements = Advertisement::all();
        return response()->json([
            'success' => true,
            'data' => $advertisements
        ], 200);
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
                'nullable', 'file', 'max:102400',
                function ($attribute, $value, $fail) use ($request) {
                    $type = $request->input('type');
                    $mimes = [
                        'video' => ['mp4', 'mpeg', 'avi'],
                        'photo' => ['jpg', 'jpeg', 'png'],
                        'audio' => ['mp3', 'wav'],
                        'pdf' => ['pdf'],
                        'text' => null,
                    ];
                    $allowedExtensions = $mimes[$type] ?? [];
                    if ($type === 'text' && $request->hasFile('file')) {
                        $fail('Aucun fichier ne doit etre fourni pour le type "text".');
                    } elseif ($type !== 'text' && !$request->hasFile('file')) {
                        $fail('Un fichier est requis pour ce type.');
                    } elseif ($type !== 'text' && !in_array(strtolower($value->getClientOriginalExtension()), $allowedExtensions)) {
                        $fail("Le fichier doit etre de type : " . implode(', ', $allowedExtensions) . ".");
                    }
                },
            ],
            'content' => 'nullable|string|required_if:type,text|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

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
        return response()->json(['success' => true, 'data' => $advertisement], 201);
    }

    public function show($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $advertisement->recordUniqueView();
        return response()->json([
            'success' => true,
            'data' => $advertisement
        ], 200);
    }

    public function trackView($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        $wasRecorded = $advertisement->recordUniqueView();
        return response()->json([
            'success' => true,
            'views' => $advertisement->views,
            'recorded' => $wasRecorded
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $advertisement = Advertisement::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'type' => 'required|in:video,photo,audio,pdf,text',
            'position' => 'required|in:accueil,rechercher',
            'end_date' => 'nullable|date',
            'end_time' => 'nullable|date_format:H:i',
            'owner_contact' => 'nullable|string|max:20',
            'owner_name' => 'nullable|string|max:100',
            'file' => [
                'nullable',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) use ($request) {
                    $type = $request->input('type');
                    $mimes = [
                        'video' => 'mp4,mpeg,avi',
                        'photo' => 'jpg,jpeg,png',
                        'audio' => 'mp3,wav',
                        'pdf' => 'pdf',
                        'text' => null,
                    ];
                    if ($type === 'text' && $request->hasFile('file')) {
                        $fail('Aucun fichier ne doit etre fourni pour le type "text".');
                    } elseif ($type !== 'text' && $request->hasFile('file') && $mimes[$type] && !in_array($value->getClientOriginalExtension(), explode(',', $mimes[$type]))) {
                        $fail("Le fichier doit etre de type : {$mimes[$type]}.");
                    }
                },
            ],
            'content' => 'nullable|string|required_if:type,text|max:100000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

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
        return response()->json([
            'success' => true,
            'data' => $advertisement
        ], 200);
    }

    public function destroy($id)
    {
        $advertisement = Advertisement::findOrFail($id);
        CloudinaryHelper::delete($advertisement->file_path);
        $advertisement->delete();
        return response()->json([
            'success' => true,
            'message' => 'Publicite supprimee avec succes'
        ], 204);
    }
}