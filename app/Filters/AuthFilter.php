<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $autenticado = session()->get('usuario_logado') === true
            && (bool) session()->get('usuario_id');

        if ($autenticado) {
            return null;
        }

        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON(['success' => false]);
        }

        return redirect()->to(base_url('/'))
            ->with('erro', 'Faça login para continuar.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}