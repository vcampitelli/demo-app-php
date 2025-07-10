<?php

declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Application\Actions\Action;
use App\Repository\ProductRepositoryInterface;
use App\Storage\S3Storage;
use Aws\S3\Exception\S3Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

readonly class ViewProductImageAction extends Action
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private S3Storage $storage,
    ) {
    }

    /**
     * Retorna os dados de um produto
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        if ($id <= 0) {
            throw new HttpBadRequestException($request, 'ID do produto inválido');
        }

        $product = $this->productRepository->findById($id);
        if ($product === null) {
            return $this->response(
                $response,
                null,
                404,
            );
        }

        try {
            $result = $this->storage->get("images/{$product->id}.png");
        } catch (S3Exception $e) {
            if ($e->getStatusCode() === 404) {
                return $this->response(
                    $response,
                    null,
                    204,
                );
            }
            throw $e;
        }

        /** @var \Psr\Http\Message\StreamInterface|null $body */
        $body = $result->get('Body');
        if ($body === null) {
            return $this->response(
                $response,
                null,
                204,
            );
        }

        return $this->response(
            $response,
            $body->getContents(),
            200,
        )->withHeader('Content-type', 'image/png');
    }
}
