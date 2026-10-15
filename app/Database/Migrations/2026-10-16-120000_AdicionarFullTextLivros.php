<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Índice FULLTEXT para a busca do catálogo (título e autor).
 *
 * Um LIKE '%termo%' não usa índice e varre a tabela inteira a cada busca;
 * MATCH ... AGAINST usa o índice invertido do InnoDB.
 */
class AdicionarFullTextLivros extends Migration
{
    public function up(): void
    {
        $this->db->query('ALTER TABLE livros ADD FULLTEXT INDEX ft_livros_titulo_autor (titulo, autor)');
    }

    public function down(): void
    {
        $this->db->query('DROP INDEX ft_livros_titulo_autor ON livros');
    }
}
