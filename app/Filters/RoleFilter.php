<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    private function norm(string $v): string
    {
        $v = trim(mb_strtolower($v, 'UTF-8'));
        $v = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $v);
        $v = preg_replace('/[^a-z0-9_ -]+/', '', $v);
        return $v ?: '';
    }

    private function normalizeRoles($userRoles): array
    {
        if (is_string($userRoles)) {
            $parts = preg_split('/[,\|]+/', $userRoles);
            $out = [];
            foreach ($parts as $p) {
                $p = $this->norm($p);
                if ($p !== '') $out[] = $p;
            }
            return array_values(array_unique($out));
        }

        if (!is_array($userRoles)) {
            return [];
        }

        $out = [];
        foreach ($userRoles as $r) {
            if (is_string($r)) {
                $v = $this->norm($r);
                if ($v !== '') $out[] = $v;
                continue;
            }
            if (is_array($r)) {
                $val = $r['roles_Valor'] ?? $r['role'] ?? $r['nombre'] ?? $r['roles'] ?? null;
                if (is_string($val) && $val !== '') {
                    $v = $this->norm($val);
                    if ($v !== '') $out[] = $v;
                }
                $id = $r['rolesId'] ?? $r['id'] ?? null;
                if ($id !== null && is_numeric($id)) {
                    $out[] = (string)intval($id);
                }
            }
            if (is_numeric($r)) {
                $out[] = (string)intval($r);
            }
        }

        return array_values(array_unique($out));
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('auth_logged_in') || !$session->get('usuario')) {
            return redirect()
                ->to(site_url('acceso/login'))
                ->with('toast_error', 'Debes iniciar sesión primero.');
        }

        $requiredRoles = $arguments ?? [];
        if (empty($requiredRoles)) {
            return;
        }
        $required = [];
        foreach ((array)$requiredRoles as $rr) {
            if (is_string($rr)) {
                $v = $this->norm($rr);
                if ($v !== '') $required[] = $v;
            }
        }

        $usuario   = $session->get('usuario');
        $userRoles = $this->normalizeRoles($usuario['roles'] ?? []);
        $hasRole = false;
        foreach ($required as $need) {
            if (in_array($need, $userRoles, true)) {
                $hasRole = true;
                break;
            }
        }

        if (!$hasRole) {
            $fallback = site_url('/');
            $referer  = $request->getServer('HTTP_REFERER');

            return redirect()
                ->to($referer ?: $fallback)
                ->with('toast_error', 'No tienes permisos para acceder a esta sección.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}