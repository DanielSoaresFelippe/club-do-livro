<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table            = 'conversas';
    protected $primaryKey       = 'id_conversa';
    protected $allowedFields    = ['id_livro', 'id_usuario_dono', 'id_usuario_interessado', 'data_inicio'];
    
    protected $useTimestamps    = false;

    public function listarConversasDoUsuario(int $meuId): array
    {
        $db = \Config\Database::connect();

        $builder = $db->table('conversas c');
        $builder->select('
            c.id_conversa,
            c.id_livro,
            l.titulo AS livro_titulo,
            l.imagem_capa,
            u.id_usuario AS id_outro_usuario,
            u.nome AS nome_outro_usuario,
            u.foto_perfil
        ');
        $builder->join('livros l', 'l.id_livro = c.id_livro');
        $builder->join('usuarios u', 'u.id_usuario = IF(c.id_usuario_dono = ' . $db->escape($meuId) . ', c.id_usuario_interessado, c.id_usuario_dono)');
        $builder->groupStart()
                ->where('c.id_usuario_dono', $meuId)
                ->orWhere('c.id_usuario_interessado', $meuId)
                ->groupEnd();
        $builder->orderBy('c.id_conversa', 'DESC');

        return $builder->get()->getResultArray();
    }
}