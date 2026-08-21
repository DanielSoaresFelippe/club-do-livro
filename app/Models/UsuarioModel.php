<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'nome',
        'email',
        'senha',
        'tipo',
        'foto_perfil',
        'telefone',
        'ativo',
    ];

    protected $validationRules = [
        'nome'     => 'required|min_length[2]|max_length[150]',
        'email'    => 'required|valid_email|is_unique[usuarios.email]',
        'senha'    => 'required|min_length[8]|regex_match[/^(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).+$/]',
        'telefone' => 'permit_empty|min_length[8]|max_length[20]',
        'tipo'     => 'required|in_list[cliente,colaborador,admin]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Esse e-mail já está cadastrado.',
        ],
        'senha' => [
            'min_length'  => 'A senha precisa ter no mínimo 8 caracteres.',
            'regex_match' => 'A senha precisa ter 1 letra maiúscula, 1 número e 1 caractere especial.',
        ],
    ];
}