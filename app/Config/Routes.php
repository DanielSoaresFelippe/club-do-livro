<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

$routes->post('cadastro', 'Usuarios::cadastrar');
$routes->post('usuarios/login', 'Usuarios::login');
$routes->get('usuarios/logout', 'Usuarios::logout');
 
$routes->get('usuarios/perfil', 'Usuarios::perfil');
$routes->get('usuarios/dados-perfil', 'Usuarios::dadosPerfil');
$routes->post('usuarios/atualizar-perfil', 'Usuarios::atualizarPerfil');
$routes->post('usuarios/logout', 'Usuarios::logout');
 
$routes->get('usuarios/historico', 'Usuarios::historico');
$routes->get('usuarios/favoritos', 'Usuarios::favoritos');

$routes->get('livro/detalhes/(:num)', 'Livro::detalhes/$1');

$routes->get('livro',                    'Livro::index');
$routes->get('livro/detalhes/(:num)',    'Livro::detalhes/$1');
$routes->get('livro/meus-livros',        'Livro::meusLivros');
$routes->get('livro/novo',               'Livro::novo');
$routes->post('livro/salvar',            'Livro::salvar');
$routes->get('livro/editar/(:num)',      'Livro::editar/$1');
$routes->post('livro/atualizar/(:num)',  'Livro::atualizar/$1');
$routes->post('livro/excluir/(:num)',    'Livro::excluir/$1');