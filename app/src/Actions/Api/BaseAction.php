<?php
namespace App\Actions\Api;

use Slim\Http\Request;
use Slim\Http\Response;
use App\Validator\Validator;

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
     * BaseAction constructor.
     *
     * @param Validator $validator
     */
    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
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

        // TODO: проверить RPC запрос

        $method = explode('.', $params['method']);
        $method = 'App\Handlers\\'.static::who().'\\'.ucfirst($method[0]).'\\'.ucfirst($method[1]);

        if (!isset(static::$methods[$method])) {
            throw new \Exception('Ohohoh');
        }

        $this->validator->validate($params['params'], static::$methods[$method]['params']);
        if (!$this->validator->passes()) {
            throw new \Exception('Ahahah');
        }

        if (static::$methods[$method]['needAuth']) {
            // TODO: если необходима авторизация - проверить авторизованность
        }

        //TODO: если указана стратегия кеширования, то применить

        $obj = new $method;
        $result = $obj($params['params']);

        // TODO: отрендерить в JSON-RPC 2.0
        return $response->withJson($result, 200);
    }

    static function who()
    {
        return 'V1';
    }
}