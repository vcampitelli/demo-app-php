<?php

declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Application\Actions\Action;
use App\Repository\ProductRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

readonly class ListProductAction extends Action
{
    public function __construct(private ProductRepositoryInterface $productRepository)
    {
    }

    /**
     * Lista os produtos
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $products = $this->productRepository->findAll();

        return $this->response($response, $products);
    }
}
