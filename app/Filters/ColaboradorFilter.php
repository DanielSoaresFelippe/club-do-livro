<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ColaboradorFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $tipo = session()->get('usuario_tipo');

        $permitido = in_array($tipo, ['colaborador', 'admin'], true);

        if ($permitido) {
            return null;
        }

        if ($request->isAJAX()) {
            return service('response')
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'erro' => 'Área exclusiva para colaboradores.']);
        }

        return redirect()->to(base_url('livro'))
            ->with('erro', 'Essa área é exclusiva para usuários colaboradores.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
