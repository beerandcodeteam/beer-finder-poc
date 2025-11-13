<?php

namespace App\Services;

use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

class EmbeddingService
{


    public function generateEmbedding(string $text)
    {
        $response = Prism::embeddings()
            ->using(Provider::OpenAI, 'text-embedding-3-large')
            ->fromInput($text)
            ->asEmbeddings();

        return $response;
    }
}
