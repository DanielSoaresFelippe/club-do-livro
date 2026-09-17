<div class="modal-overlay" id="modalContato">
    <div class="modal-perfil modal-contato">
        <button type="button"
                class="modal-fechar"
                id="fecharModalContato">
            &times;
        </button>
        <h2>Conversar com o dono</h2>

        <p class="modal-contato-intro">
            Fale diretamente com
            <strong><?= esc($livro['dono_nome'] ?? '') ?></strong>
            sobre
            "<?= esc($livro['titulo']) ?>":
        </p><br>
        <div class="modal-contato-lista">
            <?php if (!empty($livro['dono_telefone'])): ?>
                <?php
                $telefoneWhatsapp = preg_replace(
                    '/\D/',
                    '',
                    $livro['dono_telefone']
                );

                if (strlen($telefoneWhatsapp) <= 11) {
                    $telefoneWhatsapp = '55' . $telefoneWhatsapp;
                }

                $mensagemWhatsapp =
                    'Olá! Meu nome é ' .
                    ($interessado['nome'] ?? 'um leitor') .
                    ' e tenho interesse no livro "' .
                    $livro['titulo'] .
                    '".';
                ?>
                
                    <a class="modal-contato-item"
                    href="https://wa.me/<?= esc($telefoneWhatsapp, 'attr') ?>?text=<?= esc(urlencode($mensagemWhatsapp), 'attr') ?>"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <i class="fa-brands fa-whatsapp"></i>

                    <span>
                        Conversar pelo WhatsApp:
                        <?= esc($livro['dono_telefone']) ?>
                    </span>
                </a>

                <br><br>

            <?php endif; ?>

            <?php if (!empty($livro['dono_email'])): ?>
                
                    <a class="modal-contato-item"
                    href="<?= site_url('livro/enviarInteresse/' . $livro['id_livro']) ?>"
                >
                    <i class="fa-solid fa-envelope"></i>
                    <span>
                        Enviar interesse para <?= esc($livro['dono_email']) ?>
                    </span>
                </a>

            <?php endif; ?>

            <?php if (!empty($usuario['nome']) && !empty($livro['dono_email'])): ?>

                <div style="
                    margin-top: 25px;
                    padding: 18px;
                    background: #fcecef;
                    border: 2px solid #f2c6d2;
                    border-radius: 14px;
                    text-align: center;
                ">
                    <p style="
                        margin: 0 0 12px;
                        color: #4c6b3f;
                        font-weight: 600;
                    ">
                        Gostou desse livro?
                    </p>

                    <p style="
                        margin: 0 0 15px;
                        color: #756b63;
                        font-size: 14px;
                    ">
                        Demonstre seu interesse e entre em contato
                        com o dono.
                    </p>

                    
                        <a href="<?= site_url('livro/enviarInteresse/' . $livro['id_livro']) ?>"
                        style="
                            display: inline-block;
                            padding: 12px 22px;
                            background: #d98da1;
                            color: #fff;
                            text-decoration: none;
                            border-radius: 25px;
                            font-weight: 600;
                        "
                    >
                        Demonstrar interesse
                    </a>

                </div>
            <?php endif; ?>


            <?php if (
                empty($livro['dono_telefone']) &&
                empty($livro['dono_email'])
            ): ?>

                <p class="modal-contato-vazio">
                    O dono ainda não cadastrou dados de contato.
                </p>

            <?php endif; ?>

        </div>
    </div>
</div>