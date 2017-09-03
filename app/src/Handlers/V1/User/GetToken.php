<?php
namespace App\Handlers\V1\User;

class GetToken
{
    /**
     * @param string $username
     * @param string $password
     * @param string $clientId
     * @return array
     */
    public function __invoke(string $username, string $password, string $clientId)
    {
        return [
            'access_token'  => 'ad29cf10a40ec04dae0e4e5f4cab2772df1b4734',
            'expires_in'    => 3600,
            'token_type'    => 'Bearer',
            'refresh_token' => 'cc330ca2a44effb705f67152dbd67077dba3849e',
        ];
    }
}
