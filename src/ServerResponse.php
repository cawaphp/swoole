<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\Swoole;

class ServerResponse extends \Cawa\Http\ServerResponse
{
    /**
     * @param \swoole_http_response $response
     */
    public function __construct(\swoole_http_response $response)
    {
        $this->swooleResponse = $response;
        parent::__construct();
    }

    /**
     * @var \swoole_http_response
     */
    private $swooleResponse;

    /**
     */
    private function sendHeaders()
    {
        if (headers_sent($file, $line)) {
            throw new \LogicException(sprintf("Headers is already sent in '%s:%s'", $file, $line));
        }

        $this->swooleResponse->status($this->statusCode);

        foreach ($this->cookies as $cookie) {
            $this->swooleResponse->cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpire(),
                $cookie->getPath(),
                $cookie->getDomain() ?? '',
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }

        foreach ($this->headers as $name => $value) {
            $this->swooleResponse->header($name, $value);
        }
    }

    /**
     * @return string
     */
    public function send()
    {
        $this->sendHeaders();
        if ($this->body) {
            $this->swooleResponse->write($this->body);
        }

        $this->swooleResponse->end();
    }
}
