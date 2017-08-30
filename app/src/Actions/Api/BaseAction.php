<?php
namespace App\Actions\Api;

use Slim\Http\Request;
use Slim\Http\Response;

abstract class BaseAction
{
    protected static $methods = [];

    public function __construct()
    {
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $params = $request->getParams();

        // TODO: проверить RPC запрос

        $method = explode('.', $params['method']);
        $method = 'App\Handlers\\'.static::who().'\\'.ucfirst($method[0]).'\\'.ucfirst($method[1]);

        if (!isset(static::$methods[$method])) {
            throw new \Exception('Ohohoh');
        }

        // TODO: отвалидировать параметры

        if (static::$methods[$method]['needAuth']) {
            // TODO: если необходима авторизация - проверить авторизованность
        }

        $obj = new $method;
        $result = $obj($params['params']);

        // TODO: отрендерить в JSON-RPC 2.0
        return $response->withJson($result, 200);
    }

    abstract static function who();
}