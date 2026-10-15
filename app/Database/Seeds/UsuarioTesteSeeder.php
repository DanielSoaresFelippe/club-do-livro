<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Usuários de TESTE para desenvolvimento local (não rode em produção).
 *
 *   php spark db:seed UsuarioTesteSeeder
 *
 * Login: colaborador.teste@ / interessado.teste@ / leitor.teste@clubedolivro.test
 * Senha: teste-ca3e889a
 */
class UsuarioTesteSeeder extends Seeder
{
    public const SENHA = 'teste-ca3e889a';

    public function run()
    {
        $usuarios = [
            ['nome' => 'Colaborador Teste', 'email' => 'colaborador.teste@clubedolivro.test', 'tipo' => 'colaborador'],
            ['nome' => 'Interessado Teste', 'email' => 'interessado.teste@clubedolivro.test', 'tipo' => 'colaborador'],
            ['nome' => 'Leitor Teste',      'email' => 'leitor.teste@clubedolivro.test',      'tipo' => 'cliente'],
        ];

        $tabela = $this->db->table('usuarios');

        foreach ($usuarios as $usuario) {
            if ($tabela->where('email', $usuario['email'])->countAllResults() === 0) {
                $tabela->insert($usuario + [
                    'senha' => password_hash(self::SENHA, PASSWORD_DEFAULT),
                    'ativo' => 1,
                ]);
            }
        }
    }
}
