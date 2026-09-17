<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->post('/logout', 'Home::logout');

$routes->post('cadastro', 'Usuarios::cadastrar');
$routes->post('usuarios/login', 'Usuarios::login');
$routes->post('usuarios/recuperar-senha', 'Usuarios::solicitarRecuperacao');
$routes->get('usuarios/redefinir', 'Usuarios::redefinirSenha');
$routes->post('usuarios/redefinir', 'Usuarios::salvarNovaSenha');
$routes->get('usuarios/logout', 'Usuarios::logout');

$routes->get('usuarios/perfil', 'Usuarios::perfil', ['filter' => 'auth']);
$routes->get('usuarios/dados-perfil', 'Usuarios::dadosPerfil', ['filter' => 'auth']);
$routes->post('usuarios/atualizar-perfil', 'Usuarios::atualizarPerfil', ['filter' => 'auth']);
$routes->post('usuarios/logout', 'Usuarios::logout');

$routes->get('usuarios/historico', 'Usuarios::historico', ['filter' => 'auth']);
$routes->get('usuarios/favoritos', 'Usuarios::favoritos', ['filter' => 'auth']);

$routes->get('livro/detalhes/(:num)', 'Livro::detalhes/$1');

$routes->get('livro',                    'Livro::index');
$routes->get('livro/detalhes/(:num)',    'Livro::detalhes/$1');
$routes->get('favoritos', 'Favoritos::index');
$routes->post('livro/favoritar/(:num)', 'Favoritos::alternar/$1');

// Área exclusiva de colaboradores (cadastrar/gerenciar livros)
$routes->get('livro/meus-livros',        'Livro::meusLivros',  ['filter' => ['auth', 'colaborador']]);
$routes->get('livro/novo',               'Livro::novo',        ['filter' => ['auth', 'colaborador']]);
$routes->post('livro/salvar',            'Livro::salvar',      ['filter' => ['auth', 'colaborador']]);
$routes->get('livro/editar/(:num)',      'Livro::editar/$1',   ['filter' => ['auth', 'colaborador']]);
$routes->post('livro/atualizar/(:num)',  'Livro::atualizar/$1', ['filter' => ['auth', 'colaborador']]);
$routes->match(['get', 'post'], 'livro/excluir/(:num)', 'Livro::excluir/$1', ['filter' => ['auth', 'colaborador']]);

$routes->get('livro_meus', 'Livro::meusLivros', ['filter' => ['auth', 'colaborador']]);
$routes->get('novo', 'Livro::novo', ['filter' => ['auth', 'colaborador']]);
$routes->match(['get', 'post'], 'livro/enviarInteresse/(:num)', 'Livro::enviarInteresse/$1');