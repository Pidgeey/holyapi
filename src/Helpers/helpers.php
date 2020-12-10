<?php

use Slim\Psr7\Response;

/**
 * Return view
 *
 * @param string $path
 *
 * @return void
 */
function view(string $path): void
{
    require("../resources/views/$path.php");
}

/**
 * Return a response at json format
 *
 * @param $payload
 * @param int $code
 *
 * @return \Slim\Psr7\Response
 */
function jsonResponse($payload, int $code = 200): Response
{
    $response = new Response();

    $response->getBody()->write(json_encode($payload));
    return $response->withHeader('Content-Type', 'application/json')
        ->withStatus($code);
}
