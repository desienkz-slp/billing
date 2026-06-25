<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:05:24              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Illuminate\Http\Request; use Inertia\Middleware; class HandleInertiaRequests extends Middleware { protected $rootView = 'app'; public function version(Request $PTTWb): ?string { return parent::version($PTTWb); } public function share(Request $PTTWb): array { $inag1 = $PTTWb->user(); return [...parent::share($PTTWb), 'auth' => ['user' => $inag1, 'role' => $inag1 ? $inag1->role : null, 'isAdmin' => $inag1 ? $inag1->is_system_admin : false], 'flash' => ['success' => $PTTWb->session()->get('success'), 'error' => $PTTWb->session()->get('error')], 'tenant' => $PTTWb->attributes->get('tenant'), 'company' => config('company')]; } }