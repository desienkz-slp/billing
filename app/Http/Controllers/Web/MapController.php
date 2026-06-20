<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerCoordinate;
use App\Models\Odc;
use App\Models\Odp;
use App\Models\Package;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MapController extends Controller
{
    /**
     * Main map page.
     */
    public function index()
    {
        $areas = Area::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $packages = Package::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        // Stats
        $totalCustomers = Customer::whereHas('coordinate')->count();
        $totalOdps = Odp::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $totalOdcs = Odc::whereNotNull('latitude')->whereNotNull('longitude')->count();

        return Inertia::render('Map/Index', compact('areas', 'packages', 'totalCustomers', 'totalOdps', 'totalOdcs'));
    }

    /**
     * API: Get customers with coordinates for map markers.
     */
    public function apiCustomers(Request $request)
    {
        $query = Customer::with(['coordinate', 'area:id,name', 'package:id,name,speed', 'odp:id,name'])
            ->whereHas('coordinate');

        // Filters
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('status')) {
            if ($request->status === 'isolated') {
                $query->where('is_isolated', true);
            } elseif ($request->status === 'active') {
                $query->where('status', 'active')->where('is_isolated', false);
            } elseif ($request->status === 'inactive') {
                $query->where('status', '!=', 'active');
            }
        }
        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        $customers = $query->get()->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'username' => $c->username,
                'phone' => $c->phone,
                'address' => $c->address,
                'status' => $c->is_isolated ? 'isolated' : ($c->status === 'active' ? 'active' : 'inactive'),
                'area' => $c->area?->name,
                'package' => $c->package?->name,
                'speed' => $c->package?->speed,
                'odp' => $c->odp?->name,
                'lat' => (float) $c->coordinate->latitude,
                'lng' => (float) $c->coordinate->longitude,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $customers]);
    }

    /**
     * API: Get ODPs with coordinates.
     */
    public function apiOdps(Request $request)
    {
        $query = Odp::with('area:id,name')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $odps = $query->get()->map(function ($o) {
            $used = $o->customers()->count();
            return [
                'id' => $o->id,
                'name' => $o->name,
                'area' => $o->area?->name,
                'capacity' => $o->capacity ?? 0,
                'used' => $used,
                'available' => max(0, ($o->capacity ?? 0) - $used),
                'lat' => (float) $o->latitude,
                'lng' => (float) $o->longitude,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $odps]);
    }

    /**
     * API: Get ODCs with coordinates.
     */
    public function apiOdcs(Request $request)
    {
        $query = Odc::with('area:id,name')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        $odcs = $query->get()->map(function ($o) {
            $odpCount = $o->odps()->count();
            return [
                'id' => $o->id,
                'name' => $o->name,
                'area' => $o->area?->name,
                'capacity' => $o->capacity ?? 0,
                'used' => $o->used ?? 0,
                'available' => max(0, ($o->capacity ?? 0) - ($o->used ?? 0)),
                'odp_count' => $odpCount,
                'lat' => (float) $o->latitude,
                'lng' => (float) $o->longitude,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $odcs]);
    }

    /**
     * Update/create customer coordinate.
     */
    public function updateCoordinate(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        CustomerCoordinate::updateOrCreate(
            ['customer_id' => $customer->id],
            [
                'tenant_id' => session('tenant_id'),
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
            ]
        );

        return response()->json(['status' => 'success', 'message' => "Koordinat {$customer->name} berhasil disimpan."]);
    }

    /**
     * Delete customer coordinate.
     */
    public function deleteCoordinate(Customer $customer)
    {
        CustomerCoordinate::where('customer_id', $customer->id)->delete();
        return response()->json(['status' => 'success', 'message' => "Koordinat {$customer->name} berhasil dihapus."]);
    }
}
