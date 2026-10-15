<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Livros oferecidos em um lance de troca (N livros por proposta).
 */
class CriarPropostaLivros extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id_proposta_livro' => [
                'type'           => 'INT',
                'auto_increment' => true,
            ],
            'id_proposta' => [
                'type' => 'INT',
            ],
            'id_livro' => [
                'type' => 'INT',
            ],
        ]);

        $this->forge->addPrimaryKey('id_proposta_livro');
        $this->forge->addUniqueKey(['id_proposta', 'id_livro'], 'uq_proposta_livro');
        $this->forge->addForeignKey('id_proposta', 'propostas', 'id_proposta', '', 'CASCADE', 'fk_proposta_livros_proposta');
        $this->forge->addForeignKey('id_livro', 'livros', 'id_livro', '', 'CASCADE', 'fk_proposta_livros_livro');

        $this->forge->createTable('proposta_livros', false, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('proposta_livros', true);
    }
}
