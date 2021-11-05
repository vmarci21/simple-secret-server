<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use App\Utils;
use App\Secrets;

require __DIR__.'/config.php';
require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();
Utils::dbConnect();

// Home page
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

// Get one secret by hash
$app->get('/secret/{hash}', function (Request $request, Response $response, $args) {
    $secrets = Secrets::getInstance();
    $secret = $secrets->getSecretByHash($args['hash']);

    if(!$secret){
        return Utils::setResponse($request,$response,['Error'=>'Secret not found'],404);
    }
    return Utils::setResponse($request,$response,$secret,200,'Secret');
});

// Create new secret
$app->post('/secret', function (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();

    if(!isset($data['secret']) or !isset($data['expireAfter']) or !isset($data['expireAfterViews'])){
        return Utils::setResponse($request,$response,['Error'=>'Invalid input'],405);
    }

    $secrets = Secrets::getInstance();
    $secret = $secrets->addSecret($data['secret'],$data['expireAfter'],$data['expireAfterViews']);

    if(!$secret){
        return Utils::setResponse($request,$response,['Error'=>'Invalid input'],405);
    }
    return Utils::setResponse($request,$response, $secret,200,'Secret');
});


$app->addBodyParsingMiddleware();
$app->run();