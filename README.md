# Demo

Essa é uma aplicação que uso como demo em alguns cursos e palestras.

## Instalação

1. Copie o arquivo `.env.dist` para `.env`
2. Instale o [Docker Compose](https://docs.docker.com/compose/install/)
3. Execute:
    ```shell
    docker compose up -d
    ```
4. Acesse [localhost:8080](http://localhost:8080) em seu navegador

## Endpoints

- Importe o arquivo [`postman_collection.json`](./postman_collection.json) no Postman ou em outro aplicativo de testes de API que você possuir

| Método | Endpoint               | Descrição                                   |
|:------:|------------------------|---------------------------------------------|
|  GET   | `/`                    | Hello, world                                |
|  GET   | `/products`            | Lista os produtos                           |
|  GET   | `/products/{id}`       | Visualiza o produto com ID `{id}`           |
|  GET   | `/products/{id}/image` | Visualiza a imagem do produto com ID `{id}` |
|  POST  | `/products`            | Cadastra um produto                         |
