<?php
/**
 * Mild Framework component
 *
 * @author Mochammad Riyadh Ilham Akbar Pasya
 * @link https://github.com/mildphp/mild
 * @copyright 2018
 * @license https://github.com/mildphp/mild/blob/master/LICENSE (MIT Licence)
 */
namespace Mild\Http;

use RuntimeException;

class JsonResponse extends Response
{

    /**
     * JsonResponse constructor.
     *
     * @param $data
     * @param int $statusCode
     * @param int $options
     * @param array $headers
     */
    public function __construct($data, int $statusCode = 200, $options = 0, array $headers = [])
    {
        $headers['Content-Type'] = ['application/json;charset=utf-8'];
        $stream = new Stream(fopen('php://temp', 'r+'));
        $stream->write(json_encode($data, $options));
        $stream->rewind();
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException(json_last_error_msg(), json_last_error());
        }
        parent::__construct($statusCode, $headers, $stream);
    }
}