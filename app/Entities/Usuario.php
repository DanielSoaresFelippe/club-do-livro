<?php

namespace App\Entities;

use ArrayAccess;
use CodeIgniter\Entity\Entity;

/**
 * Usuário do Clube do Livro.
 *
 * - $usuario->foto_url  devolve sempre uma imagem válida: a foto enviada ou o avatar padrão.
 *   As Views usam só isso, sem if/else.
 * - Implementa ArrayAccess para que o código existente que usa $usuario['nome'] continue
 *   funcionando (e $usuario['foto_url'] também passa pelo getter).
 */
class Usuario extends Entity implements ArrayAccess
{
    public const AVATAR_PADRAO = 'assets/img/avatar-padrao.png';
    public const PASTA_FOTOS   = 'uploads/perfil/';

    /**
     * Accessor: $usuario->foto_url / $usuario['foto_url']
     */
    public function getFotoUrl(): string
    {
        $foto = $this->attributes['foto_perfil'] ?? null;

        // Sem foto no banco, ou o arquivo sumiu do servidor: usa o avatar padrão
        if (empty($foto) || !is_file(FCPATH . self::PASTA_FOTOS . $foto)) {
            return base_url(self::AVATAR_PADRAO);
        }

        return base_url(self::PASTA_FOTOS . $foto);
    }

    /**
     * Respostas JSON (ex.: modal de perfil): inclui foto_url e nunca expõe a senha.
     */
    public function jsonSerialize(): array
    {
        $dados = $this->toArray();
        unset($dados['senha']);
        $dados['foto_url'] = $this->foto_url;

        return $dados;
    }

    // --- ArrayAccess ---------------------------------------------------------

    public function offsetExists(mixed $offset): bool
    {
        return $this->__isset((string) $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->__get((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->__set((string) $offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->__unset((string) $offset);
    }
}
