<?php

/**
 * Monta a URL de um asset estático (CSS/JS em public/) com cache-busting
 * automático baseado no mtime do próprio arquivo — a query string só muda
 * quando o arquivo muda de verdade no disco, então o navegador reaproveita
 * o cache entre deploys sem precisar de Ctrl+F5, e não perde cache à toa
 * entre requisições em que nada mudou.
 */
function assetUrl(string $path): string
{
    $fullPath = FCPATH . $path;
    $version  = is_file($fullPath) ? filemtime($fullPath) : time();

    return base_url($path) . '?v=' . $version;
}