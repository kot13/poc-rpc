<?php
namespace App\JsonRpc;

class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $client = new \GuzzleHttp\Client('/api');
    }

    /**
     * @param string $method
     * @param array $params
     * @param string $version
     * @param string|null $accessToken
     * @return string
     */
    public function call(string $method, array $params, string $version = 'v1', string $accessToken = null)
    {
        $options = [
            'json' => [
                'jsonrpc' => '2.0',
                'method'  => $method,
                'params'  => $params,
                'id'      => 1,
            ],
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ];

        if ($accessToken) {
            $options['headers']['Authorization'] = $accessToken;
        }

        $response = $this->client->post('/'.$version, $options);
        $content  = $response->getBody()->getContents();

        return $content;
    }
}