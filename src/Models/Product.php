<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

use function strlen;
use function trim;

readonly class Product implements ModelInterface
{
    public ?int $id;
    public int $idCategory;
    public string $name;
    public string $sku;
    public float $price;
    public bool $active;
    public string|null $image;

    /**
     * @param int|null $id
     * @param int $idCategory
     * @param string $name
     * @param string $sku
     * @param float $price
     * @param bool $active
     * @param string|null $image
     */
    public function __construct(
        ?int $id,
        int $idCategory,
        string $name,
        string $sku,
        float $price,
        ?string $image,
        bool $active,
    ) {
        if (($id !== null) && ($id < 1)) {
            throw new InvalidArgumentException('ID do produto inválido');
        }
        if ($idCategory < 1) {
            throw new InvalidArgumentException('ID da categoria inválido');
        }

        $name = $this->validateName($name);

        $sku = $this->validateSKu($sku);

        if ($price <= 0) {
            throw new InvalidArgumentException('Preço inválido');
        }

        $this->id = $id;
        $this->idCategory = $idCategory;
        $this->name = $name;
        $this->sku = $sku;
        $this->price = $price;
        $this->image = $image;
        $this->active = $active;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'id_category' => $this->idCategory,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'image' => $this->image,
            'active' => $this->active,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function withId(?int $id): ModelInterface
    {
        return $this->build(id: $id);
    }
    public function withIdCategory(int $idCategory): Product
    {
        return $this->build(idCategory: $idCategory);
    }

    public function withName(string $name): Product
    {
        return $this->build(name: $name);
    }

    public function withSku(string $sku): Product
    {
        return $this->build(sku: $sku);
    }

    public function withPrice(float $price): Product
    {
        return $this->build(price: $price);
    }

    public function withImage(string $image): Product
    {
        return $this->build(image: $image);
    }

    public function withActive(bool $active): Product
    {
        return $this->build(active: $active);
    }

    protected function build(
        ?int $id = null,
        ?int $idCategory = null,
        ?string $name = null,
        ?string $sku = null,
        ?float $price = null,
        ?string $image = null,
        ?bool $active = null
    ): Product {
        return new Product(
            id: $id ?? $this->id,
            idCategory: $idCategory ?? $this->idCategory,
            name: $name ?? $this->name,
            sku: $sku ?? $this->sku,
            price: $price ?? $this->price,
            image: $image ?? $this->image,
            active: $active ?? $this->active,
        );
    }

    private function validateName(string $name): string
    {
        $name = trim($name);
        if (empty($name)) {
            throw new InvalidArgumentException('Nome do produto não informado');
        }
        if (strlen($name) < 3) {
            throw new InvalidArgumentException('O nome do produto deve conter ao menos 3 letras');
        }
        if (strlen($name) > 30) {
            throw new InvalidArgumentException('O nome do produto deve conter até 30 letras');
        }

        return $name;
    }

    private function validateSKu(string $sku): string
    {
        $sku = trim($sku);
        if (empty($sku)) {
            throw new InvalidArgumentException('SKU não informado');
        }
        if (strlen($sku) != 10) {
            throw new InvalidArgumentException('O SKU deve conter 10 dígitos');
        }
        return $sku;
    }
}
