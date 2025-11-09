<?php

namespace App\Jobs;

use App\Models\Beer;
use App\Models\BeerEmbedding;
use App\Services\EmbeddingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessBeer implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Beer $beer)
    {}

    /**
     * Execute the job.
     */
    public function handle(EmbeddingService $embeddingService): void
    {
        try {
            $data = $this->beer->toArray();
            $embedding = $embeddingService->generateEmbedding(json_encode($data));

            BeerEmbedding::create([
                'beer_id' => $this->beer->id,
                'text' => "teste",
                'metadata' => $data,
                'embedding' => $embedding->embeddings[0]->embedding,
            ]);
        } catch (\Exception $e) {
            dd($e);
        }

        dd($data);
    }
}
