<?php

namespace App\Jobs;

use App\Models\Beer;
use App\Models\BeerEmbedding;
use App\Services\EmbeddingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;

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

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4.1-mini')
                ->withSystemPrompt(view('prompts.brewer'))
                ->withPrompt($this->beer->toJson())
                ->withClientOptions(['timeout' => 9999])
                ->asText();

            $this->beer->update(['brewer_tips' => $response->text]);

            $embedding = $embeddingService->generateEmbedding($response->text);

            BeerEmbedding::create([
                'beer_id' => $this->beer->id,
                'text' => $response->text,
                'metadata' => $this->beer->toArray(),
                'embedding' => $embedding->embeddings[0]->embedding,
            ]);
        } catch (\Exception $e) {
            dd($e);
        }

    }
}
