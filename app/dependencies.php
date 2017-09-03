<?php
// DIC configuration

$container = $app->getContainer();

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$container['view'] = function($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());

    return $view;
};

// Validator
$container['validator'] = function($c) {
    $validator = new App\Validator\Validator();

    return $validator;
};

$container['server'] = function($c) {
    $server = new App\JsonRpc\Server();

    return $server;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));

    return $logger;
};

$container['errorHandler'] = function($c) {
    return function ($request, $response, Exception $exception) use ($c) {
        if ($exception instanceof App\JsonRpc\Exception) {
            $encode = $exception->encodeError();
        } else {
            $details = $c['settings']['displayErrorDetails'] ? $exception->getMessage() : 'Internal server error';
            $encode  = $c['server']->encodeError(App\JsonRpc\Server::INTERNAL_ERROR, null, $details);
        }

        return $c['response']->withJson($encode, 200);
    };
};

$container['notFoundHandler'] = function($c) {
    return function ($request, $response) use ($c) {
        throw new App\JsonRpc\Exception(App\JsonRpc\Server::METHOD_NOT_FOUND);
    };
};

$container['notAllowedHandler'] = function($c) {
    return function ($request, $response, $methods) use ($c) {
        throw new App\JsonRpc\Exception(App\JsonRpc\Server::METHOD_NOT_FOUND);
    };
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------
$container[App\Actions\HomeAction::class] = function($c) {
    return new App\Actions\HomeAction($c->get('view'), $c->get('logger'));
};

$container[App\Actions\Api\V1::class] = function($c) {
    return new App\Actions\Api\V1($c->get('validator'), $c->get('server'));
};

$container[App\Actions\Api\V2::class] = function($c) {
    return new App\Actions\Api\V2($c->get('validator'), $c->get('server'));
};