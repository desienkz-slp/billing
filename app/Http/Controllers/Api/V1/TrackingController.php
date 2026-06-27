<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:34              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Controllers\Api\V1; use App\Http\Controllers\Controller; use App\Models\EmployeeLocation; use Illuminate\Http\Request; class TrackingController extends Controller { public function updateLocation(Request $CaBx1) { goto H0kd0; iTjWA: $mNbko = $CaBx1->user(); goto ApkMZ; H0kd0: $CaBx1->validate(['latitude' => 'required|numeric', 'longitude' => 'required|numeric', 'battery_level' => 'nullable|integer']); goto iTjWA; gWj1c: return response()->json(['status' => 'success', 'message' => 'Location updated successfully', 'data' => $eEe7a]); goto eeXAe; ApkMZ: $eEe7a = EmployeeLocation::create(['tenant_id' => $mNbko->tenant_id, 'user_id' => $mNbko->id, 'latitude' => $CaBx1->latitude, 'longitude' => $CaBx1->longitude, 'battery_level' => $CaBx1->battery_level, 'recorded_at' => now()]); goto gWj1c; eeXAe: } public function liveLocations(Request $CaBx1) { goto vrcuR; RJ9oB: return response()->json(['status' => 'success', 'data' => $CNZZu]); goto gpl84; hZtD6: $CNZZu = EmployeeLocation::with('user:id,name,role_id')->where('tenant_id', $OPVm2)->whereIn('id', function ($Oa5s4) { $Oa5s4->selectRaw('MAX(id)')->from('employee_locations')->groupBy('user_id'); })->get(); goto RJ9oB; vrcuR: $OPVm2 = $CaBx1->user()->tenant_id; goto hZtD6; gpl84: } }
