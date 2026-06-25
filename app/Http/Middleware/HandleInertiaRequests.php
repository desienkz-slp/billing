<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-25 10:49:51              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace App\Http\Middleware; use Illuminate\Http\Request; use Inertia\Middleware; class HandleInertiaRequests extends Middleware { protected $rootView = 'app'; public function version(Request $u9qhv): ?string { return parent::version($u9qhv); } public function share(Request $u9qhv): array { $Ie2cF = $u9qhv->user(); return [...parent::share($u9qhv), 'auth' => ['user' => $Ie2cF, 'role' => $Ie2cF ? $Ie2cF->role : null, 'isAdmin' => $Ie2cF ? $Ie2cF->is_system_admin : false], 'flash' => ['success' => $u9qhv->session()->get('success'), 'error' => $u9qhv->session()->get('error')], 'tenant' => $u9qhv->attributes->get('tenant'), 'company' => config('company')]; } }
