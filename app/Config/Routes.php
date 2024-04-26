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
  $routes->post("allCards", "CharacterCard::viewAll", ['filter' => 'authFilter']);
  $routes->post("getCard", "CharacterCard::view", ['filter' => 'authFilter']);
  $routes->options('addCard', 'CharacterCard::options');
  $routes->options('allCards', 'CharacterCard::options');
  $routes->options('getCard', 'CharacterCard::options');
});