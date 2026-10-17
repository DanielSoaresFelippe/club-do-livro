<?php
/**
 * @var int $meu_id
 * @var array $destinatario
 * @var array $historico
 * @var int $destinatario_id
 * @var array $usuarioLogado
 */
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chat - Clube do Livro</title>
    <link rel="stylesheet" href="<?= assetUrl('assets/styles/chat.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.socket.io/4.7.5/socket.io.min.js"></script>
</head>

<body>

<div class="app-wrapper">
    <div class="sidebar">
        <!-- Painel da esquerda -->
        <div class="my-profile-header">
            <div class="my-profile-info">
                <div class="my-avatar">
                    <?php if (!empty($usuarioLogado['foto_perfil']) &&$usuarioLogado['foto_perfil'] !== 'default.png'): ?>
                        <img src="<?= base_url('uploads/perfil/' . $usuarioLogado['foto_perfil']) ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($usuarioLogado['nome'] ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="my-name">
                    <?= esc($usuarioLogado['nome'] ?? 'Meu Perfil') ?> <span style="font-size:11px; color:#f076af; display:block;">(Você)</span>
                </div>
            </div>
            <a href="<?= base_url('/') ?>" class="btn-voltar" title="Voltar para a página inicial" aria-label="Voltar">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        </div>

        <div class="sidebar-header">Conversas</div>

        <div class="lista-conversas">
            <?php if (!empty($conversas)): ?>
                <?php foreach ($conversas as$c): ?>
                <a href="<?= base_url('chat/' . $c['id_conversa']) ?>" 
                class="item-conversa <?= (isset($idConversaSelecionada) && $c['id_conversa'] == $idConversaSelecionada) ? 'ativo' : '' ?>"
                onclick="event.preventDefault(); abrirConversa(<?= $c['id_outro_usuario'] ?>, '<?= esc($c['nome_outro_usuario']) ?>', '<?= esc($c['foto_perfil']) ?>', <?= $c['id_conversa'] ?>);">
                    
                    <div class="avatar">
                        <?php if (!empty($c['foto_perfil']) && $c['foto_perfil'] !== 'default.png'): ?>
                            <img src="<?= base_url('uploads/perfil/' . $c['foto_perfil']) ?>" style="width:100%; height:100%; object-fit:cover;">
                        <?php else: ?>
                            <?= strtoupper(substr($c['nome_outro_usuario'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="info">
                        <h4><?= esc($c['nome_outro_usuario']) ?></h4>
                        <p><small>Livro: <?= esc($c['livro_titulo']) ?></small></p>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="padding: 20px; text-align: center; color: #667781; font-size: 14px;">Nenhuma conversa iniciada.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Painel da direita -->
    <div class="chat-panel">
        <div id="chat-ativo" style="display: none; height: 100%; flex-direction: column;">
            <div class="chat-header">
                <div class="avatar" id="header-avatar">
                    <?php if (!empty($fotoPerfilOutro) && $fotoPerfilOutro !== 'default.png'): ?>
                        <img src="<?= base_url('uploads/perfil/' . $fotoPerfilOutro) ?>" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                    <?php else: ?>
                        <?= strtoupper(substr($nomeOutroUsuario ?? 'U', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="info">
                    <div class="contato-nome" id="header-nome" style="font-size: 15px; font-weight: 600; color: #111b21;">
                        <?= esc($nomeOutroUsuario ?? 'Selecione uma conversa') ?>
                    </div>
                </div>
            </div>

            <div class="chat-messages" id="mensagens"></div>

            <form class="chat-input" id="chat-form">
                <input type="text" id="campo-mensagem" placeholder="Digite uma mensagem..." autocomplete="off" required>
                <button type="submit">Enviar</button>
            </form>
        </div>

        <div id="chat-placeholder" class="placeholder-chat">
            <h2>Clube do Livro</h2>
            <p>Selecione uma conversa ao lado para começar a enviar mensagens.</p>
        </div>
    </div>
</div>

<script src="http://localhost:3000/socket.io/socket.io.js"></script>
<script>
    const MEU_ID = <?= $meu_id ?>;
    let DESTINATARIO_ID_ATUAL = <?= $destinatario_id ?? 'null' ?>;
    let ID_CONVERSA_ATUAL = <?= $conversaAtiva['id_conversa'] ?? 'null' ?>;

    const SOCKET_URL = `http://${window.location.hostname}:3000`;
    const socket = io(SOCKET_URL);

    socket.on('connect', () => {
        socket.emit('registrar_usuario', MEU_ID);
    });

    socket.on('receber_mensagem', (dados) => {
        if (dados.id_conversa == ID_CONVERSA_ATUAL) {
            adicionarMensagemNaTela({
                id_mensagem: dados.id_mensagem,
                id_remetente: dados.remetente_id,
                texto: dados.conteudo,
                hora: dados.criado_em
            });
        }
    });

    socket.on('atualizar_sidebar_conversas', (dados) => {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const novaListaConversas = doc.querySelector('.lista-conversas');
                const listaAtual = document.querySelector('.lista-conversas');
                
                if (novaListaConversas && listaAtual) {
                    listaAtual.innerHTML = novaListaConversas.innerHTML;
                }
            })
            .catch(err => {
                console.error('Erro ao atualizar a sidebar:', err);
            });
    });

    const placeholder = document.getElementById('chat-placeholder');
    const chatAtivo = document.getElementById('chat-ativo');
    const headerNome = document.getElementById('header-nome');
    const headerAvatar = document.getElementById('header-avatar');
    const containerMensagens = document.getElementById('mensagens');

    function abrirConversa(destinatarioId, nomeUsuario, fotoPerfil, idConversa = null) {
        DESTINATARIO_ID_ATUAL = destinatarioId;
        ID_CONVERSA_ATUAL = idConversa;

        if (idConversa && idConversa !== 'null') {
            window.history.pushState({}, '', '<?= base_url('chat/') ?>' + idConversa);
        } else {
            window.history.pushState({}, '', '<?= base_url('chat') ?>');
        }

        document.getElementById('chat-placeholder').style.display = 'none';
        document.getElementById('chat-ativo').style.display = 'flex';

        const headerNome = document.getElementById('header-nome');
        headerNome.textContent = nomeUsuario;

        const headerAvatar = document.getElementById('header-avatar');
        if (fotoPerfil && fotoPerfil !== 'default.png') {
            headerAvatar.innerHTML = `<img src="<?= base_url('uploads/perfil/') ?>${fotoPerfil}" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">`;
        } else {
            headerAvatar.textContent = nomeUsuario.charAt(0).toUpperCase();
        }

        carregarMensagens(destinatarioId);
    }

    document.addEventListener('DOMContentLoaded', () => {
        <?php if (!empty($conversaAtiva) && !empty($destinatario_id)): ?>
            const nomeContato = "<?= esc($nomeOutroUsuario ?? 'Contato') ?>";
            const fotoContato = "<?= esc($fotoPerfilOutro ?? 'default.png') ?>";
            const idConversa = <?= $conversaAtiva['id_conversa'] ?? 'null' ?>;
            
            abrirConversa(<?= $destinatario_id ?>, nomeContato, fotoContato, idConversa);
        <?php endif; ?>
    });

    function carregarMensagens(destinatarioId) {
        containerMensagens.innerHTML = '<p style="text-align:center; color:#888; padding:20px;">Carregando mensagens...</p>';

        fetch(`<?= base_url('chat/carregarConversa/') ?>${destinatarioId}?id_conversa=${ID_CONVERSA_ATUAL}`)
            .then(response => response.json())
            .then(data => {
                containerMensagens.innerHTML = '';

                if (data.id_conversa) {
                    ID_CONVERSA_ATUAL = data.id_conversa;
                }

                if (data.mensagens && data.mensagens.length > 0) {
                    data.mensagens.forEach(msg => {
                        adicionarMensagemNaTela({
                            id_mensagem: msg.id_mensagem,
                            id_remetente: msg.id_remetente || msg.remetente_id,
                            texto: msg.texto || msg.conteudo,
                            hora: msg.data_envio || msg.criado_em
                        });
                    });
                } else {
                    containerMensagens.innerHTML = '<p id="mensagem-vazia" style="text-align:center; color:#888; padding:20px;">Nenhuma mensagem ainda. Diga oi!</p>';
                }

                containerMensagens.scrollTop = containerMensagens.scrollHeight;
            })
            .catch(err => {
                console.error(err);
                containerMensagens.innerHTML = '<p style="text-align:center; color:red; padding:20px;">Erro ao carregar mensagens.</p>';
            });
    }

    function adicionarMensagemNaTela(msg) {
        const mensagensDiv = document.getElementById('mensagens');
        
        const ehMinha = (msg.id_remetente == MEU_ID);
        
        const div = document.createElement('div');
        div.className = `mensagem ${ehMinha ? 'minha-mensagem' : 'mensagem-outro'}`;
        div.id = `msg-${msg.id_mensagem}`; 
        
        let horaFormatada = 'Agora';
        if (msg.hora) {
            if (msg.hora.includes(' ')) {
                const partes = msg.hora.split(' ');
                horaFormatada = partes[1].substring(0, 5);
            } else {
                horaFormatada = msg.hora.substring(0, 5);
            }
        }
        
        let html = `
            <span class="texto-msg">${msg.texto}</span>
            <span class="hora-msg">${horaFormatada}</span>
        `;
        
        // if (ehMinha && msg.id_mensagem) {
        //     html += `
        //         <button type="button" class="btn-deletar-msg" onclick="deletarMensagem(${msg.id_mensagem})" title="Apagar mensagem">
        //             🗑️
        //         </button>
        //     `;
        // }
        
        div.innerHTML = html;
        mensagensDiv.appendChild(div);
        mensagensDiv.scrollTop = mensagensDiv.scrollHeight;
    }

    // function deletarMensagem(idMensagem) {
    //     if (!confirm('Deseja realmente apagar esta mensagem?')) return;

    //     fetch(`<?= base_url('chat/deletar-mensagem') ?>/${idMensagem}`, {
    //         method: 'POST',
    //         headers: {
    //             'X-Requested-With': 'XMLHttpRequest'
    //         }
    //     })
    //     .then(res => res.json())
    //     .then(data => {
    //         if (data.sucesso) {
    //             socket.emit('deletar_mensagem', {
    //                 id_mensagem: idMensagem,
    //                 id_conversa: ID_CONVERSA_ATUAL
    //             });
    //         } else {
    //             alert('Erro ao apagar mensagem.');
    //         }
    //     });
    // }

    // socket.on('mensagem_deletada', (data) => {
    //     const el = document.getElementById(`msg-${data.id_mensagem}`);
    //     if (el) {
    //         el.remove();
    //     }
    // });

    const chatForm = document.getElementById('chat-form');
    const campoMensagem = document.getElementById('campo-mensagem');

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const conteudo = campoMensagem.value.trim();

        if (!conteudo || !DESTINATARIO_ID_ATUAL) {
            return;
        }

        const formData = new FormData();
        formData.append('destinatario_id', DESTINATARIO_ID_ATUAL);
        formData.append('id_conversa', ID_CONVERSA_ATUAL);
        formData.append('conteudo', conteudo);

        fetch('<?= base_url('chat/enviarMensagem') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'sucesso') {
                if (data.mensagem && data.mensagem.id_conversa) {
                    const novoIdConversa = data.mensagem.id_conversa;
                    
                    if (!ID_CONVERSA_ATUAL || ID_CONVERSA_ATUAL === 'null') {
                        ID_CONVERSA_ATUAL = novoIdConversa;
                        window.history.pushState({}, '', '<?= base_url('chat/') ?>' + novoIdConversa);
                    }
                }

                adicionarMensagemNaTela({
                    id_mensagem: data.mensagem.id_mensagem,
                    id_remetente: MEU_ID,
                    texto: conteudo,
                    hora: data.mensagem.criado_em
                });
                
                socket.emit('enviar_mensagem', {
                    id_mensagem: data.mensagem.id_mensagem,
                    id_conversa: ID_CONVERSA_ATUAL,
                    remetente_id: MEU_ID,
                    destinatario_id: DESTINATARIO_ID_ATUAL,
                    conteudo: conteudo,
                    criado_em: data.mensagem.criado_em
                });

                socket.emit('notificar_nova_conversa', {
                    destinatario_id: DESTINATARIO_ID_ATUAL,
                    id_conversa: ID_CONVERSA_ATUAL,
                    remetente_id: MEU_ID
                });

                campoMensagem.value = '';
                campoMensagem.focus();
            } else {
                alert('Erro ao enviar mensagem: ' + (data.erro || 'Tente novamente.'));
            }
        })
        .catch(err => {
            console.error('Erro no envio:', err);
            alert('Erro de conexão ao enviar mensagem.');
        });
    });
</script>

</body>
</html>