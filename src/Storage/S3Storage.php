<?php

declare(strict_types=1);

namespace App\Storage;

use Aws\Result;
use Aws\S3\S3Client;
use Psr\Http\Message\StreamInterface;

readonly class S3Storage
{
    public function __construct(
        private S3Client $client,
        private string $bucketName,
    ) {
    }

    public function upload(
        string $key,
        StreamInterface $contents
    ): string {
        $key = \ltrim($key, '/');
        $result = $this->client->putObject([
            'Bucket' => $this->bucketName,
            'Body' => $contents,
            'Key' => $key,
        ]);

        return $result->get('ObjectURL');
    }

    public function get(string $key): Result
    {
        return $this->client->getObject([
            'Bucket' => $this->bucketName,
            'Key' => $key,
        ]);
    }
}
