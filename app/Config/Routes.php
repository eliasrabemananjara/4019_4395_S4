<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->get('/operateur/prefixes', 'OperateurController::prefixes');
$routes->post('/operateur/prefixes/ajouter', 'OperateurController::ajouter');
$routes->get('/operateur/baremes', 'OperateurController::baremes');
$routes->get('/operateur/baremes/edit/(:num)', 'OperateurController::editBareme/$1');
$routes->post('/operateur/baremes/update/(:num)', 'OperateurController::updateBareme/$1');
$routes->get('/operateur/ma-gain', 'OperateurController::gains');
$routes->get('/operateur/clients', 'OperateurController::clients');
