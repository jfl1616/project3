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

$routes->addControllerOnly(new Route("{token}/{gameId}/stream", array(
   '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::stream'
)));

$routes->addControllerOnly(new Route("{token}/getOnlineUser", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::getOnlineUser'
)));

$routes->addControllerOnly(new Route("{token}/getIncomingChallenge", array (
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::getIncomingChallenge'
)));
$routes->addControllerOnly(new Route("{token}/startGame", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::startGame'
)));
$routes->addControllerOnly(new Route("{token}/logout", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::logout'
)));
$routes->addAjax(new Route("{token}/addMessage", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::addMessage'
)));

$routes->addAjax(new Route("{token}/updateUserLastActivity", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::updateUserLastActivity'
)));
$routes->addAjax(new Route("{token}/sendChallenge", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::sendChallenge'
)));
$routes->addAjax(new Route("{token}/responseChallenge", array(
    '_controller' => '\Bolzen\Src\Controller\Lobby\LobbyController::responseChallenge'
)));
/*********************************
 * CHALLENGE
 **********************************/
$routes->add("FourConnect/fourconnect", new Route('{token}/{gameId}/FourConnect', array(
    '_controller' => '\Bolzen\Src\Controller\FourConnect\FourConnectController::FourConnect'
)));
$routes->add("FourConnect/fourconnect1", new Route('fourconnect1', array(
    '_controller' => '\Bolzen\Src\Controller\FourConnect\FourConnectController::FourConnect'
)));
$routes->addAjax(new Route("{token}/click", array(
    '_controller' => '\Bolzen\Src\Controller\FourConnect\FourConnectController::click'
)));
$routes->addAjax(new Route("{token}/{gameId}/switchChipColor", array(
    '_controller' => "\Bolzen\Src\Controller\FourConnect\FourConnectController::switchChipColor"
)));
$routes->addAjax(new Route("{token}/{gameId}/updateGameResult", array(
    '_controller' => "\Bolzen\Src\Controller\FourConnect\FourConnectController::updateGameResult"
)));
$routes->addControllerOnly(new Route("{token}/{gameId}/playerMove", array(
    '_controller' => '\Bolzen\Src\Controller\FourConnect\FourConnectController::playerMove'
 )));
$routes->addControllerOnly(new Route("{token}/{gameId}/gameStatus", array(
    '_controller' => '\Bolzen\Src\Controller\FourConnect\FourConnectController::gameStatus'
)));

###############################
# Do not modify below
##############################
return $routes->getRouteCollection();
