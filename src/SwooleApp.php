<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Cawa\Swoole;

use Cawa\App\AbstractApp;
use Cawa\App\HttpApp;
use Cawa\App\HttpFactory;
use Cawa\Core\DI;

class SwooleApp extends HttpApp
{
    use HttpFactory;

    /**
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     */
    public function setHttp(\swoole_http_request $request, \swoole_http_response $response)
    {
        DI::set('Cawa\App\HttpFactory::response', null, new ServerResponse($response));
    }

    /**
     * Load route & request
     */
    public function init()
    {
        $explode = explode(':', $_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = array_shift($explode);
        parent::init();
    }

    /**
     * @return string
     */
    public static function end()
    {
        AbstractApp::end();

        $out = ob_get_clean();
        if ($out) {
            self::response()->setBody($out . self::response()->getBody());
        }

        return self::response()->send();
    }
}
