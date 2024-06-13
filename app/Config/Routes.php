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
  $routes->options('addCard', 'CharacterCard::options');

  $routes->post("putCard", "CharacterCard::put", ['filter' => 'authFilter']);
  $routes->options('putCard', 'CharacterCard::options');

  $routes->post("allCards", "CharacterCard::viewAll", ['filter' => 'authFilter']);
  $routes->options('allCards', 'CharacterCard::options');

  $routes->post("getCard", "CharacterCard::view", ['filter' => 'authFilter']);
  $routes->options('getCard', 'CharacterCard::options');

  $routes->post('verifyToken', 'TokenController::verify');
  $routes->options('verifyToken', 'TokenController::options');
  
  $routes->post("deleteCard", "CharacterCard::delete", ['filter' => 'authFilter']);
  $routes->options('deleteCard', 'CharacterCard::options');
});