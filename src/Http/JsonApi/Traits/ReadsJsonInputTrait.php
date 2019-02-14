<?php

namespace App\Http\JsonApi\Traits;


use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * Read json payload with defaults.
 */
trait ReadsJsonInputTrait
{
    /**
     *
     * @param SymfonyRequest|GuzzleResponse|ResponseInterface $request
     * @param array $defaults properties of returned object if not overriden by request.
     * @param array $errors json-decode error
     *
     * @return object
     */
    public function readJson($request, $defaults = [], &$errors=[])
    {
        $content = '';
        if ($request instanceof GuzzleResponse) {
            $content = strval($request->getBody());
        }

        if ($request instanceof Response) {
            $content = $request->getContent(false);
        }

        if ($request instanceof SymfonyRequest) {
            $content = $request->getContent(false);
        }


        $pay = (array) jsonDecode($content, $errors);
        $res = (object) ($pay + $defaults);
        return $res;
    }


    /*
    protected function psr2foundation($resp) {
        $adapter = new class($resp) extends Request {
            public function __construct( $content = null ) {
                parent::__construct([], [], [], [], [], [], $content);
            }
            public function getContent($asResource = false) {
                return $asResource ? $this->content->getBody() : $this->content->getBody() . '';
            }
        };
        return $adapter;
    }

     * */

}



