<?php
namespace App\JsonRpc;

class Server
{
    /**
     * Protocol version
     */
    const JSON_RPC_VERSION = '2.0';

    /**
     * Error code - parse error
     */
    const PARSE_ERROR = -32700;

    /**
     * Error code - invalid request
     */
    const INVALID_REQUEST = -32600;

    /**
     * Error code - method not found
     */
    const METHOD_NOT_FOUND = -32601;

    /**
     * Error code - invalid params
     */
    const INVALID_PARAMS = -32602;

    /**
     * Error code - internal server error
     */
    const INTERNAL_ERROR = -32603;

    /**
     * Error code - wrong credentials
     */
    const WRONG_CREDENTIALS = -32401;

    /**
     * Error code - access denied
     */
    const ACCESS_DENIED = -32403;

    /**
     * Error code - resource not found
     */
    const RESOURCE_NOT_FOUND = -32404;

    /**
     * Error messages
     */
    const ERROR_MESSAGES = [
        self::PARSE_ERROR        => 'Parse error',
        self::INVALID_REQUEST    => 'Invalid Request',
        self::METHOD_NOT_FOUND   => 'Method not found',
        self::INVALID_PARAMS     => 'Invalid params',
        self::INTERNAL_ERROR     => 'Internal error',
        self::WRONG_CREDENTIALS  => 'Wrong credentials',
        self::ACCESS_DENIED      => 'Access denied',
        self::RESOURCE_NOT_FOUND => 'Resource not found',
    ];

    /**
     * @param $request
     *
     * @return bool
     */
    public function isValidRequest($request)
    {
        if (!isset($request['jsonrpc']) || $request['jsonrpc'] !== '2.0') {
            return false;
        }

        if (!isset($request['id'])) {
            return false;
        }

        if (!isset($request['method'])) {
            return false;
        }

        return true;
    }

    /**
     * @param int|null $id
     * @param array $result
     *
     * @return array
     */
    public function encodeSuccess($id = null, $result)
    {
        return [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'result'  => $result,
        ];
    }

    /**
     * @param int         $code
     * @param int|null    $id
     * @param string|null $data
     *
     * @return array
     */
    public static function encodeError($code, $id = null, $data = null)
    {
        $result = [
            'jsonrpc' => '2.0',
            'id'      => $id,
            'error'   => [
                'code'    => $code,
                'message' => self::getErrorMessage($code),
            ],
        ];

        if (!is_null($data) && !empty($data)) {
            $result['error']['data'] = $data;
        }

        return $result;
    }

    /**
     * @param $code
     *
     * @return string
     */
    private static function getErrorMessage($code)
    {
        $message = self::ERROR_MESSAGES[$code] ?? self::ERROR_MESSAGES[self::INTERNAL_ERROR];

        return $message;
    }
}