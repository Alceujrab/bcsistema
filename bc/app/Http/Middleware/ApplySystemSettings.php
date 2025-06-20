<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Helpers\ConfigHelper;
use Illuminate\Support\Facades\View;

class ApplySystemSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Compartilhar configurações com todas as views
        View::share('companyData', ConfigHelper::getCompanyData());
        View::share('dashboardConfig', ConfigHelper::getDashboardConfig());
        
        // Configurações de aparência
        $appearance = ConfigHelper::getByCategory('appearance');
        View::share('appearanceConfig', $appearance);
        
        // Configurações gerais
        $general = ConfigHelper::getByCategory('general');
        View::share('generalConfig', $general);

        return $next($request);
    }
}
