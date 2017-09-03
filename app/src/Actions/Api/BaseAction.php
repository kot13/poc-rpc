<?php
namespace App\Actions\Api;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Validator\Validator;
use App\JsonRpc\Server;
use App\JsonRpc\Exception;

abstract class BaseAction
{
    /**
     * @var array
     */
    protected static $methods = [];

    /**
     * @var Validator
     */
    protected $validator;

    /**
     * @var Server
     */
    protected $server;

    /**
     * BaseAction constructor.
     *
     * @param Validator $validator
     * @param Server $server
     */
    public function __construct(Validator $validator, Server $server)
    {
        $this->validator = $validator;
        $this->server    = $server;
    }

    /**
     * @param Request  $request
     * @param Response $response
     * @param          $args
     *
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response, $args)
    {
        $params = $request->getParams();

        if (!$this->server->isValidRequest($params)) {
            throw new Exception(Server::INVALID_REQUEST);
        }

        $method = explode('.', $params['method']);
        $method = 'App\Handlers\\'.static::who().'\\'.ucfirst($method[0]).'\\'.ucfirst($method[1]);

        if (!isset(static::$methods[$method])) {
            throw new Exception(Server::METHOD_NOT_FOUND, $params['id']);
        }

        $this->validator->validate($params['params'], static::$methods[$method]['params']);
        if (!$this->validator->passes()) {
            throw new Exception(Server::INVALID_PARAMS, $params['id'], $this->validator->getErrorString());
        }

        if (static::$methods[$method]['needAuth']) {
            // TODO: если необходима авторизация - проверить авторизованность
        }

        if (static::$methods[$method]['needCache']) {
            //TODO: если указана стратегия кеширования, то применить
        }

        $obj = new $method;
        $result = $obj($params['params']);

        $encode = $this->server->encodeSuccess($params['id'], $result);
        return $response->withJson($encode, 200);
    }
}