<?php

declare(strict_types=1);

namespace App\Application\Actions;

use Psr\Http\Message\ResponseInterface as Response;

use function json_encode;

use const JSON_PRETTY_PRINT;

readonly abstract class Action
{
    /**
     * @param Response $response
     * @param object|array|string|null $data
     * @param int $statusCode
     * @return Response
     */
    protected function response(
        Response $response,
        object|array|string|null $data = null,
        int $statusCode = 200
    ): Response {
        $payload = new ActionPayload($statusCode, $data);

        if ($data !== null) {
            if (!\is_string($data)) {
                $data = json_encode($payload, JSON_PRETTY_PRINT);
                $response = $response->withHeader('Content-Type', 'application/json');
            }
            $response->getBody()->write($data);
        }

        return $response->withStatus($payload->getStatusCode());
    }
}
