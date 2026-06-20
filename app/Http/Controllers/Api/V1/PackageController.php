<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        return PackageResource::collection(
            Package::withCount('customers')->orderBy('price')->get()
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'speed' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);

        $package = Package::create($data);
        return (new PackageResource($package))->response()->setStatusCode(201);
    }

    public function show(Package $package): PackageResource
    {
        return new PackageResource($package->loadCount('customers'));
    }

    public function update(Request $request, Package $package): PackageResource
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'speed' => 'nullable|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ]);
        $package->update($data);
        return new PackageResource($package);
    }

    public function destroy(Package $package): JsonResponse
    {
        if ($package->customers()->count() > 0) {
            return response()->json(['message' => 'Paket masih digunakan pelanggan.'], 422);
        }
        $package->delete();
        return response()->json(['message' => 'Paket berhasil dihapus.']);
    }
}
