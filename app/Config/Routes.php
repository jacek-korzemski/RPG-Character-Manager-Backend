<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group("api", function ($routes) {
  $routes->post("register", "Register::index");
  $routes->post("login", "Login::index");
  $routes->post("addCard", "CharacterCard::add", ['filter' => 'authFilter']);
  $routes->post("putCard", "CharacterCard::put", ['filter' => 'authFilter']);
  $routes->post("allCards", "CharacterCard::viewAll", ['filter' => 'authFilter']);
  $routes->post("getCard", "CharacterCard::view", ['filter' => 'authFilter']);
  $routes->post('verifyToken', 'TokenController::verify');
  $routes->options('addCard', 'CharacterCard::options');
  $routes->options('allCards', 'CharacterCard::options');
  $routes->options('getCard', 'CharacterCard::options');
  $routes->options('putCard', 'CharacterCard::options');
  $routes->options('verifyToken', 'TokenController::options');
});