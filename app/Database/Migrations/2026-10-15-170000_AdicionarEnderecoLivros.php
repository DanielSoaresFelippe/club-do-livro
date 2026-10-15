<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Separa a localização do livro em bairro / cidade / estado (preenchidos via ViaCEP).
 *
 * A coluna `localizacao` ("Cidade - UF") é mantida porque o filtro do catálogo usa ela;
 * o LivroModel passa a montá-la automaticamente a partir de cidade + estado.
 */
class AdicionarEnderecoLivros extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('livros', [
            'bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'cep',
            ],
            'cidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'bairro',
            ],
            'estado' => [
                'type'       => 'CHAR',
                'constraint' => 2,
                'null'       => true,
                'after'      => 'cidade',
            ],
        ]);

        $this->db->query('CREATE INDEX idx_livros_cidade_estado ON livros (cidade, estado)');

        // Aproveita o que já foi cadastrado no formato "Cidade - UF"
        $this->db->query("
            UPDATE livros
               SET cidade = TRIM(SUBSTRING_INDEX(localizacao, ' - ', 1)),
                   estado = UPPER(TRIM(SUBSTRING_INDEX(localizacao, ' - ', -1)))
             WHERE localizacao LIKE '% - __'
        ");
    }

    public function down(): void
    {
        $this->db->query('DROP INDEX idx_livros_cidade_estado ON livros');
        $this->forge->dropColumn('livros', ['bairro', 'cidade', 'estado']);
    }
}
