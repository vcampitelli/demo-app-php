<?php

declare(strict_types=1);

namespace App\Application\Actions\Product;

use App\Application\Actions\Action;
use App\Models\Product;
use App\Repository\ProductRepositoryInterface;
use App\Storage\S3Storage;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;

readonly class CreateProductAction extends Action
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
        private S3Storage $storage,
    ) {
    }

    /**
     * Cadastra um produto
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody();

        if (empty($body['id_category'])) {
            throw new HttpBadRequestException($request, 'Categoria não informada');
        }
        if (empty($body['name'])) {
            throw new HttpBadRequestException($request, 'Nome não informado');
        }
        if (empty($body['sku'])) {
            throw new HttpBadRequestException($request, 'SKU não informado');
        }
        if (empty($body['price'])) {
            throw new HttpBadRequestException($request, 'Preço não informado');
        }

        $product = new Product(
            id: null,
            idCategory: (int) $body['id_category'],
            name: $body['name'],
            sku: $body['sku'],
            price: (float) $body['price'],
            image: null,
            active: true,
        );
        $product = $this->productRepository->save($product);

        $product = $this->saveRandomImageTo($product);

        return $this->response($response, $product);
    }

    protected function saveRandomImageTo(Product $product): Product
    {
        $client = new \GuzzleHttp\Client();
        $request = $client->get('https://prd.place/400');

        $key = "images/{$product->id}.png";
        $this->storage->upload($key, $request->getBody());

        $product = $product->withImage("/products/{$product->id}/image");

        return $this->productRepository->save($product);
    }
}
