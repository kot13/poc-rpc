<?php
namespace App\Handlers\V2\User;

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
     * @param string $phone
     * @param string $password
     * @return array
     */
    public function __invoke(string $phone, string $password)
    {
        return [
            'access_token'  => 'ad29cf10a40ec04dae0e4e5f4cab2772df1b4734',
            'expires_in'    => 3600,
            'token_type'    => 'Bearer',
            'refresh_token' => 'cc330ca2a44effb705f67152dbd67077dba3849e',
        ];
    }
}
