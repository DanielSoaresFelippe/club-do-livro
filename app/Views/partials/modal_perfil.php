<div class="modal-overlay" id="modalPerfil">
    <div class="modal-perfil">
        <button type="button" class="modal-fechar" id="fecharModalPerfil">&times;</button>
        <h2>Editar perfil</h2>

        <img id="modalFotoPreview" class="modal-foto-preview" src="" alt="Foto de perfil">

        <span id="btnTrocarFoto" class="modal-foto-btn">
            Trocar foto de perfil
        </span>

        <form id="formPerfil" enctype="multipart/form-data">
            <label for="perfilNome">Nome</label>
            <input type="text" id="perfilNome" name="nome" required>
            <div class="modal-erro" data-erro-de="nome"></div>

            <label for="perfilEmail">E-mail</label>
            <input type="email" id="perfilEmail" name="email" required>
            <div class="modal-erro" data-erro-de="email"></div>

            <label for="perfilTelefone">Telefone</label>
            <input type="text" id="perfilTelefone" name="telefone" maxlength="15" inputmode="numeric" placeholder="(00) 00000-0000">
            <div class="modal-erro" data-erro-de="telefone"></div>

            <label for="perfilTipo">Quero</label>
            <select id="perfilTipo" name="tipo" required>
                <option value="cliente">Ler / comprar livros</option>
                <option value="colaborador">Vender / trocar livros</option>
            </select>
            <div class="modal-erro" data-erro-de="tipo"></div>

            <label for="perfilSenha"> Nova senha (deixe em branco para manter a atual) </label>
            <div class="perfil-password-wrap">
                <input type="password" id="perfilSenha" name="senha">
                <button type="button" class="perfil-password-toggle" id="togglePerfilSenha" aria-label="Mostrar senha">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            <div class="modal-erro" data-erro-de="senha"></div>

            <input type="file" id="perfilFoto" name="foto_perfil" accept="image/jpeg,image/png,image/webp" hidden>
            <div class="modal-erro" data-erro-de="foto_perfil"></div>

            <button type="submit" class="modal-salvar">Salvar alterações</button>
            <div class="modal-msg-sucesso" id="perfilMsgSucesso">Dados atualizados com sucesso!</div>
        </form>
    </div>
</div>