<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 11:29:45              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Controllers\Api\V1; use App\Http\Controllers\Controller; use App\Models\EmployeeLocation; use Illuminate\Http\Request; class TrackingController extends Controller { public function updateLocation(Request $u9qhv) { goto Wjyh8; bn1IC: $Ie2cF = $u9qhv->user(); goto ZJ6s3; v6ZpB: return response()->json(['status' => 'success', 'message' => 'Location updated successfully', 'data' => $F_H_r]); goto R93je; Wjyh8: $u9qhv->validate(['latitude' => 'required|numeric', 'longitude' => 'required|numeric', 'battery_level' => 'nullable|integer']); goto bn1IC; ZJ6s3: $F_H_r = EmployeeLocation::create(['tenant_id' => $Ie2cF->tenant_id, 'user_id' => $Ie2cF->id, 'latitude' => $u9qhv->latitude, 'longitude' => $u9qhv->longitude, 'battery_level' => $u9qhv->battery_level, 'recorded_at' => now()]); goto v6ZpB; R93je: } public function liveLocations(Request $u9qhv) { goto ajBAF; jUl2g: $IgoxT = EmployeeLocation::with('user:id,name,role_id')->where('tenant_id', $uE9I9)->whereIn('id', function ($aU3Kf) { $aU3Kf->selectRaw('MAX(id)')->from('employee_locations')->groupBy('user_id'); })->get(); goto NcWP_; ajBAF: $uE9I9 = $u9qhv->user()->tenant_id; goto jUl2g; NcWP_: return response()->json(['status' => 'success', 'data' => $IgoxT]); goto g9Z_M; g9Z_M: } }
