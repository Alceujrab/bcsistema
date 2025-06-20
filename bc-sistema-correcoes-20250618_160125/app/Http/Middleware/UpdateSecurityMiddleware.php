<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar se está em modo de manutenção durante updates
        if (app()->isDownForMaintenance() && !$this->isUpdateRequest($request)) {
            return response()->view('system.maintenance', [], 503);
        }

        // Verificar se o IP está autorizado (opcional)
        if (config('updater.restrict_ip') && !$this->isAuthorizedIp($request)) {
            abort(403, 'Acesso negado ao sistema de atualizações');
        }

        // Verificar se o usuário tem permissão (se autenticado)
        if (auth()->check() && !$this->hasUpdatePermission($request)) {
            abort(403, 'Você não tem permissão para acessar o sistema de atualizações');
        }

        // Log de acesso ao sistema de updates
        \Log::info('Acesso ao sistema de updates', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'user_id' => auth()->id(),
            'route' => $request->route()->getName()
        ]);

        return $next($request);
    }

    private function isUpdateRequest(Request $request): bool
    {
        return str_starts_with($request->path(), 'system/update');
    }

    private function isAuthorizedIp(Request $request): bool
    {
        $authorizedIps = config('updater.authorized_ips', []);
        
        if (empty($authorizedIps)) {
            return true; // Se não há IPs configurados, permite todos
        }

        $clientIp = $request->ip();
        
        foreach ($authorizedIps as $authorizedIp) {
            if ($this->ipMatches($clientIp, $authorizedIp)) {
                return true;
            }
        }

        return false;
    }

    private function ipMatches(string $clientIp, string $authorizedIp): bool
    {
        // Suporte para CIDR (ex: 192.168.1.0/24)
        if (strpos($authorizedIp, '/') !== false) {
            list($subnet, $mask) = explode('/', $authorizedIp);
            $subnet = ip2long($subnet);
            $clientIp = ip2long($clientIp);
            $mask = ~((1 << (32 - $mask)) - 1);
            
            return ($subnet & $mask) === ($clientIp & $mask);
        }

        // Comparação direta
        return $clientIp === $authorizedIp;
    }

    private function hasUpdatePermission(Request $request): bool
    {
        $user = auth()->user();
        
        // Verificar se o usuário é admin ou tem permissão específica
        return $user->is_admin ?? false || 
               $user->can('manage-updates') ?? false ||
               $user->hasRole('admin') ?? false;
    }
}
