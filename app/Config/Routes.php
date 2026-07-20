<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// --- Page d'accueil ---
$routes->get('/', 'Home::index');

// --- Client ---
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
