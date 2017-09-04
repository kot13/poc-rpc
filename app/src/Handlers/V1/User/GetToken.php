<?php
namespace App\Handlers\V1\User;

use App\JsonRpc\Exception;
use App\JsonRpc\Server;
use GuzzleHttp\Client;
use Slim\Container;

class GetToken
{
    /**
     * @var Client
     */
    private $amruApi;

    /**
     * GetToken constructor.
     * @param Container $c
     */
    public function __construct(Container $c)
    {
        $this->amruApi = $c->get('amru-api');
    }

    /**
     * @param string $username
     * @param string $password
     * @param string $clientId
     * @return array
     * @throws \Exception
     */
    public function __invoke(string $username, string $password, string $clientId)
    {
        $res = $this->amruApi->post('', [
            'headers' => [
                'X-API-KEY'    => 'blizzard-entertainment',
                'X-API-CLIENT' => 'desktop_site',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'user.getToken',
                'params' => [
                    'username' => $username,
                    'password' => $password,
                    'clientId' => $clientId,
                ],
                'id' => 1,
            ],
        ]);

        $content = $res->getBody()->getContents();
        $content = json_decode($content, true);

        if (!isset($content['result']) || isset($content['result']['error'])) {
            throw new Exception(Server::WRONG_CREDENTIALS);
        }

        return $content['result'];
    }
}
