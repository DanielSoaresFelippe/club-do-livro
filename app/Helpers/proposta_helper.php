<?php

/**
 * Textos usados nas telas de propostas / negociação.
 */

if (!function_exists('rotuloStatusProposta')) {
    function rotuloStatusProposta(string $status): string
    {
        return [
            'pendente'       => 'Pendente',
            'aceita'         => 'Aceita',
            'recusada'       => 'Recusada',
            'contraproposta' => 'Contraproposta feita',
            'cancelada'      => 'Cancelada',
            'expirada'       => 'Expirada',
            'concluida'      => 'Concluída',
        ][$status] ?? ucfirst($status);
    }
}

if (!function_exists('resumoOferta')) {
    /**
     * "R$ 25,00" ou "Troca por: Duna, O Hobbit"
     */
    function resumoOferta(array $proposta): string
    {
        if ($proposta['tipo'] === 'dinheiro') {
            return 'R$ ' . number_format((float) $proposta['valor_oferecido'], 2, ',', '.');
        }

        $titulos = array_column($proposta['livros_oferecidos'] ?? [], 'titulo');

        return 'Troca por: ' . ($titulos ? implode(', ', $titulos) : '—');
    }
}

if (!function_exists('propostasAguardandoResposta')) {
    /**
     * Contador do menu: ofertas esperando resposta do usuário logado.
     */
    function propostasAguardandoResposta(): int
    {
        static $total = null;

        $idUsuario = (int) session()->get('usuario_id');

        if (!$idUsuario) {
            return 0;
        }

        return $total ??= (new \App\Models\PropostaModel())->contarAguardandoResposta($idUsuario);
    }
}
