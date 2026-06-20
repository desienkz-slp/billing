<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index()
    {
        return AreaResource::collection(
            Area::withCount('customers')->orderBy('name')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        $area = Area::create($data);
        return (new AreaResource($area))->response()->setStatusCode(201);
    }

    public function show(Area $area): AreaResource
    {
        return new AreaResource($area->loadCount('customers'));
    }

    public function update(Request $request, Area $area): AreaResource
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);
        $area->update($data);
        return new AreaResource($area);
    }

    public function destroy(Area $area): JsonResponse
    {
        if ($area->customers()->count() > 0) {
            return response()->json(['message' => 'Area masih digunakan pelanggan.'], 422);
        }
        $area->delete();
        return response()->json(['message' => 'Area berhasil dihapus.']);
    }
}
