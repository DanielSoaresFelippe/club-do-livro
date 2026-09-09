<?php

namespace App\Models;

use CodeIgniter\Model;

class HistoricoModel extends Model
{
    protected $table      = 'historico_transacoes';
    protected $primaryKey = 'id_transacao';

    public function listarDoUsuario(int $idUsuario): array
    {
        $historico = $this->select('livros.id_livro, livros.titulo, livros.autor, livros.imagem_capa, historico_transacoes.tipo, historico_transacoes.data_conclusao')
            ->join('livros', 'livros.id_livro = historico_transacoes.id_livro')
            ->groupStart()
                ->where('historico_transacoes.id_comprador', $idUsuario)
                ->orWhere('historico_transacoes.id_vendedor', $idUsuario)
            ->groupEnd()
            ->orderBy('historico_transacoes.data_conclusao', 'DESC')
            ->findAll();

        return array_map(function ($item) {
            $item['imagem_capa'] = $item['imagem_capa'] ?? base_url('assets/img/capa-padrao.png');
            return $item;
        }, $historico);
    }
}