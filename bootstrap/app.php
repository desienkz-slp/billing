<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:37              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 use Illuminate\Foundation\Application; use Illuminate\Foundation\Configuration\Exceptions; use Illuminate\Foundation\Configuration\Middleware; return Application::configure(basePath: dirname(__DIR__))->withRouting(web: __DIR__ . '/../routes/web.php', api: __DIR__ . '/../routes/api.php', commands: __DIR__ . '/../routes/console.php', health: '/up')->withMiddleware(function (Middleware $MWOUG): void { goto ja3g6; ja3g6: $MWOUG->alias(['tenant' => \App\Http\Middleware\ResolveTenant::class, 'permission' => \App\Http\Middleware\CheckPermission::class, 'license' => \App\Http\Middleware\CheckLicense::class, 'system_admin' => \App\Http\Middleware\SystemAdmin::class]); goto nagpG; FJ616: \Illuminate\Auth\Middleware\RedirectIfAuthenticated::redirectUsing(function () { return route('app-gateway', [], false); }); goto wqEHN; nagpG: $MWOUG->web(append: [\App\Http\Middleware\ResolveTenant::class, \App\Http\Middleware\HandleInertiaRequests::class]); goto FJ616; wqEHN: })->withExceptions(function (Exceptions $eoWNJ): void { })->create();
