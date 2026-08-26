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