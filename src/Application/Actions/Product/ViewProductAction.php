<?php

declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Application\Actions\Action;
use App\Repository\ProductRepositoryInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

readonly class ViewProductAction extends Action
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
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

        return $this->response(
            $response,
            $product,
            ($product === null) ? 404 : 200
        );
    }
}
