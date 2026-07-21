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

$routes->get('/client',           'ConnectionClient::index',     ['as' => 'client.form']);
$routes->post('/client/connect',  'ConnectionClient::connect',   ['as' => 'client.connect']);
$routes->get('/client/dashboard', 'ConnectionClient::dashboard', ['as' => 'client.dashboard']);
$routes->get('/client/logout',    'ConnectionClient::logout',    ['as' => 'client.logout']);
$routes->post('/client/depot',    'DepotClient::process',        ['as' => 'client.depot']);
$routes->get('/client/retrait',   'RetraitClient::index',        ['as' => 'client.retrait']);
$routes->post('/client/retrait',  'RetraitClient::process',      ['as' => 'client.retrait.process']);
$routes->get('/client/transfert',   'TransfertClient::index',        ['as' => 'client.transfert']);
$routes->post('/client/transfert',  'TransfertClient::process',      ['as' => 'client.transfert.process']);
$routes->get('/client/historique',  'HistoriqueClient::index',       ['as' => 'client.historique']);
$routes->get('/client/epargne',  'EpargneClient::index',       ['as' => 'client.epargne']);
$routes->post('/client/epargne',  'EpargneClient::process',       ['as' => 'client.epargne.process']);
