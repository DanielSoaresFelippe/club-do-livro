<?php

namespace App\Models;

use CodeIgniter\Model;

class MensagemModel extends Model
{
    protected $table            = 'mensagens';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id_conversa', 'remetente_id', 'destinatario_id', 'conteudo', 'criado_em'];
    protected $useTimestamps    = false;
}