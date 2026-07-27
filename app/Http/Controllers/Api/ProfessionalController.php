<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfessionalController extends Controller
{
    public function index(Request $request)
    {
        $query = Professional::query();

        if ($request->has('village_id')) {
            $query->where('village_id', $request->village_id);
        }

        $professionals = $query->get();
        return response()->json(['data' => $professionals], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'profession' => 'required|string|max:100',
            'contact' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:20',
            'village_id' => 'required|exists:villages,id',
            'image' => 'nullable|string', // Base64 attendu
        ]);

        $data = $request->all();
        if ($request->has('image')) {
            $imagePath = $this->storeImage($request->image);
            $data['image'] = $imagePath;
        }

        $professional = Professional::create($data);
        return response()->json(['data' => $professional], 201);
    }

    public function show($id)
    {
        $professional = Professional::findOrFail($id);
        return response()->json(['data' => $professional], 200);
    }

    public function update(Request $request, $id)
    {
        $professional = Professional::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'profession' => 'required|string|max:100',
            'contact' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'whatsapp' => 'nullable|string|max:20',
            'village_id' => 'required|exists:villages,id',
            'image' => 'nullable|string',
        ]);

        $data = $request->all();
        if ($request->has('image')) {
            if ($professional->image) {
                Storage::delete('public/' . $professional->image);
            }
            $imagePath = $this->storeImage($request->image);
            $data['image'] = $imagePath;
        }

        $professional->update($data);
        return response()->json(['data' => $professional], 200);
    }

    public function destroy($id)
    {
        $professional = Professional::findOrFail($id);
        if ($professional->image) {
            Storage::delete('public/' . $professional->image);
        }
        $professional->delete();
        return response()->json(null, 204);
    }

    private function storeImage($base64Image)
    {
        $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        $path = 'professionals/' . uniqid() . '.jpg';
        Storage::put('public/' . $path, $image);
        return $path;
    }
}
