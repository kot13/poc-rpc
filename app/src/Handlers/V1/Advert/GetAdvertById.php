<?php
namespace App\Handlers\V1\Advert;

use App\JsonRpc\Exception;
use App\JsonRpc\Server;
use GuzzleHttp\Client;
use Slim\Container;

class GetAdvertById
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
     * @param string $advertId
     * @return array
     * @throws \Exception
     */
    public function __invoke(string $advertId)
    {
        $res = $this->amruApi->post('', [
            'headers' => [
                'X-API-KEY'    => 'blizzard-entertainment',
                'X-API-CLIENT' => 'desktop_site',
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'advert.getAdvertById',
                'params' => [
                    'advertId' => $advertId,
                ],
                'id' => 1,
            ],
        ]);

        $content = $res->getBody()->getContents();
        $content = json_decode($content, true);

        if (!isset($content['result']['advert']) || count($content['result']['advert']) == 0) {
            throw new Exception(Server::RESOURCE_NOT_FOUND);
        }

        return $content['result']['advert'];
    }
}
