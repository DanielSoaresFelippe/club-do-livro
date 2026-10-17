<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Transforma `propostas` na tabela de lances do módulo de Troca e Venda:
 *
 * - tipo: 'dinheiro' ou 'troca' (os livros da troca ficam em `proposta_livros`)
 * - status ganha 'contraproposta' e 'cancelada'
 * - id_proposta_pai encadeia contrapropostas (Etapa 3)
 * - id_usuario_autor diz quem fez aquela oferta (interessado ou dono, na contraproposta)
 * - id_livro_oferecido sai: passa a existir apenas em `proposta_livros`
 */
class AjustarPropostasParaLances extends Migration
{
    public function up(): void
    {
        // tipo: 'compra' vira 'dinheiro'
        $this->db->query("ALTER TABLE propostas MODIFY tipo ENUM('troca','compra','emprestimo','dinheiro') NOT NULL");
        $this->db->query("UPDATE propostas SET tipo = 'dinheiro' WHERE tipo = 'compra'");
        $this->db->query("ALTER TABLE propostas MODIFY tipo ENUM('dinheiro','troca') NOT NULL");

        $this->db->query("
            ALTER TABLE propostas MODIFY status
                ENUM('pendente','aceita','recusada','contraproposta','cancelada','expirada','concluida')
                NOT NULL DEFAULT 'pendente'
        ");

        $this->forge->addColumn('propostas', [
            'id_proposta_pai' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'id_proposta',
            ],
            'id_usuario_autor' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'id_usuario_interessado',
            ],
            'mensagem' => [
                'type'  => 'TEXT',
                'null'  => true,
                'after' => 'valor_oferecido',
            ],
            'data_resposta' => [
                'type'  => 'DATETIME',
                'null'  => true,
                'after' => 'data_criacao',
            ],
        ]);

        $this->db->query('UPDATE propostas SET id_usuario_autor = id_usuario_interessado');
        $this->db->query('ALTER TABLE propostas MODIFY id_usuario_autor INT NOT NULL');

        $this->db->query('
            ALTER TABLE propostas
                ADD CONSTRAINT fk_propostas_pai   FOREIGN KEY (id_proposta_pai)  REFERENCES propostas(id_proposta) ON DELETE CASCADE,
                ADD CONSTRAINT fk_propostas_autor FOREIGN KEY (id_usuario_autor) REFERENCES usuarios(id_usuario)   ON DELETE CASCADE
        ');

        $this->db->query('CREATE INDEX idx_propostas_livro_status ON propostas (id_livro, status)');

        // Índice simples em id_livro fica redundante (sobra se o down() já tiver rodado)
        if ($this->db->query("SHOW INDEX FROM propostas WHERE Key_name = 'fk_propostas_livro'")->getRow()) {
            $this->db->query('DROP INDEX fk_propostas_livro ON propostas');
        }

        // Livro oferecido único -> tabela de livros oferecidos
        $this->db->query('
            INSERT INTO proposta_livros (id_proposta, id_livro)
            SELECT id_proposta, id_livro_oferecido
              FROM propostas
             WHERE id_livro_oferecido IS NOT NULL
        ');

        $this->forge->dropForeignKey('propostas', 'fk_propostas_oferecido');
        $this->forge->dropColumn('propostas', 'id_livro_oferecido');
    }

    public function down(): void
    {
        $this->forge->addColumn('propostas', [
            'id_livro_oferecido' => [
                'type'  => 'INT',
                'null'  => true,
                'after' => 'id_usuario_interessado',
            ],
        ]);

        // Volta só o primeiro livro de cada troca (o modelo antigo aceitava apenas um)
        $this->db->query('
            UPDATE propostas p
              JOIN (SELECT id_proposta, MIN(id_livro) AS id_livro FROM proposta_livros GROUP BY id_proposta) pl
                ON pl.id_proposta = p.id_proposta
               SET p.id_livro_oferecido = pl.id_livro
        ');

        $this->db->query('
            ALTER TABLE propostas
                ADD CONSTRAINT fk_propostas_oferecido FOREIGN KEY (id_livro_oferecido) REFERENCES livros(id_livro) ON DELETE SET NULL
        ');

        // O MySQL passa a usar idx_propostas_livro_status na FK de id_livro;
        // recria um índice simples antes de removê-lo.
        if (!$this->db->query("SHOW INDEX FROM propostas WHERE Key_name = 'fk_propostas_livro'")->getRow()) {
            $this->db->query('CREATE INDEX fk_propostas_livro ON propostas (id_livro)');
        }
        $this->db->query('DROP INDEX idx_propostas_livro_status ON propostas');
        $this->forge->dropForeignKey('propostas', 'fk_propostas_pai');
        $this->forge->dropForeignKey('propostas', 'fk_propostas_autor');
        $this->forge->dropColumn('propostas', ['id_proposta_pai', 'id_usuario_autor', 'mensagem', 'data_resposta']);

        $this->db->query("UPDATE propostas SET status = 'recusada' WHERE status IN ('contraproposta','cancelada')");
        $this->db->query("
            ALTER TABLE propostas MODIFY status
                ENUM('pendente','aceita','recusada','expirada','concluida') NOT NULL DEFAULT 'pendente'
        ");

        $this->db->query("ALTER TABLE propostas MODIFY tipo ENUM('troca','compra','emprestimo','dinheiro') NOT NULL");
        $this->db->query("UPDATE propostas SET tipo = 'compra' WHERE tipo = 'dinheiro'");
        $this->db->query("ALTER TABLE propostas MODIFY tipo ENUM('troca','compra','emprestimo') NOT NULL");
    }
}
