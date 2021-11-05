<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Spatie\ArrayToXml\ArrayToXml;

class Utils {
    public static function dbConnect(){
        \DB::$user = CONFIG_DB_USER;
        \DB::$password = CONFIG_DB_PASSWORD;
        \DB::$dbName = CONFIG_DB_NAME;
    }

    public static function setResponse(Request $request, Response $response,Array $data,int $status = 200,String $rootName = 'Root') : Response {
        $contentType = $request->getHeaderLine('Accept');

        if (strstr($contentType, 'application/json')) {
            $payload = json_encode($data);

            $response->getBody()->write($payload);
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus($status);
        }

        $payload = ArrayToXml::convert($data,$rootName);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/xml')
            ->withStatus($status);
    }
}