<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Gêneros iniciais. Pode rodar mais de uma vez: só insere o que ainda não existe.
 *
 *   php spark db:seed GenerosSeeder
 */
class GenerosSeeder extends Seeder
{
    public function run()
    {
        $generos = [
            'Romance',
            'Fantasia',
            'Ficção científica',
            'Suspense',
            'Terror',
            'Biografia',
            'Autoajuda',
            'Infantojuvenil',
            'HQ e mangá',
            'Didático',
            'Poesia',
            'História',
        ];

        $tabela = $this->db->table('generos');

        foreach ($generos as $nome) {
            if ($tabela->where('nome', $nome)->countAllResults() === 0) {
                $tabela->insert(['nome' => $nome]);
            }
        }
    }
}
