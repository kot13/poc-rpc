<?php
namespace App\JsonRpc;

class Exception extends \Exception
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $data;

    /**
     * Exception constructor.
     *
     * @param int         $code
     * @param int|null    $id
     * @param string|null $data
     */
    public function __construct($code, $id = null, $data = null)
    {
        $this->code = $code;
        $this->id   = $id;
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function encodeError()
    {
        return Server::encodeError($this->code, $this->id, $this->data);
    }
}