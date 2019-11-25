<?php
use Symfony\Component\Routing\Route;
use Bolzen\Core\RouteCollection\RouteCollection;

$config = $container->get('config');
$accessControl = $container->get('accessControl');
$routes = new RouteCollection($config, $accessControl);

####################################
# Do not modify the line above
# Your Routes goes here
##################################

/*********************************
 * LOGIN / REGISTRATION
 **********************************/

$routes->add('Login/login', new Route("login", array(
   '_controller' => '\Bolzen\Src\Controller\Login\LoginController::login'
)));


$routes->addAjax(new Route('{token}/login', array(
    '_controller' => '\Bolzen\Src\Controller\Login\LoginController::login'
)));

$routes->addAjax(new Route('{token}/register', array(
    '_controller' => '\Bolzen\Src\Controller\Registration\RegistrationController::register'
)));


/*********************************
 * ACTIVATION / RESEND TOKENS
 **********************************/
$routes->add("Activation/activation", new Route("activation/{token}", array(
    '_controller' =>'\Bolzen\Src\Controller\Activation\ActivationController::activate',
    'token'=>'default'
)));

$routes->addAjax(new Route("{token}/resend", array(
    '_controller' =>'\Bolzen\Src\Controller\Activation\ActivationController::resend'
)));

/*********************************
 * LOBBY
 **********************************/
$routes->add("Lobby/lobby", new Route("{token}/lobby",array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::lobby'
)));

$routes->addControllerOnly(new Route("index", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::redirectToLobby'
)));

$routes->addControllerOnly(new Route("{token}/stream", array(
   '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::stream'
)));

$routes->addControllerOnly(new Route("{token}/getOnlineUser", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::getOnlineUser'
)));
$routes->addAjax(new Route("{token}/addMessage", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::addMessage'
)));

$routes->addAjax(new Route("{token}/updateUserLastActivity", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::updateUserLastActivity'
)));



###############################
# Do not modify below
##############################
return $routes->getRouteCollection();
