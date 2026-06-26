<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-27 01:21:31              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Controllers\Api\V1; use App\Http\Controllers\Controller; use App\Models\EmployeeLocation; use Illuminate\Http\Request; class TrackingController extends Controller { public function updateLocation(Request $DVgdY) { goto jYPSO; jYPSO: $DVgdY->validate(['latitude' => 'required|numeric', 'longitude' => 'required|numeric', 'battery_level' => 'nullable|integer']); goto ClbTF; h_0DN: return response()->json(['status' => 'success', 'message' => 'Location updated successfully', 'data' => $Q6YSc]); goto LXJvs; ClbTF: $l8Pdr = $DVgdY->user(); goto xhYH9; xhYH9: $Q6YSc = EmployeeLocation::create(['tenant_id' => $l8Pdr->tenant_id, 'user_id' => $l8Pdr->id, 'latitude' => $DVgdY->latitude, 'longitude' => $DVgdY->longitude, 'battery_level' => $DVgdY->battery_level, 'recorded_at' => now()]); goto h_0DN; LXJvs: } public function liveLocations(Request $DVgdY) { goto oc0To; oc0To: $C6ucv = $DVgdY->user()->tenant_id; goto E7gmi; DbjqF: return response()->json(['status' => 'success', 'data' => $BOYLG]); goto LTvbt; E7gmi: $BOYLG = EmployeeLocation::with('user:id,name,role_id')->where('tenant_id', $C6ucv)->whereIn('id', function ($OpYw4) { $OpYw4->selectRaw('MAX(id)')->from('employee_locations')->groupBy('user_id'); })->get(); goto DbjqF; LTvbt: } }
