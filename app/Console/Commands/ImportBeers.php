<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBeer;
use App\Models\Beer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportBeers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-beers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jsonPath = "/var/www/html/public/beers.json";

        $jsonData = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($jsonData)) {
            die("Erro ao ler o JSON\n");
        }

        foreach ($jsonData as $item) {
            $beer = Beer::create([
                'name'            => $item['name'] ?? null,
                'tagline'         => $item['tagline'] ?? null,
                'description'     => $item['description'] ?? null,
                'first_brewed_date' => Carbon::canBeCreatedFromFormat($item['first_brewed'], 'm/Y') ?
                    Carbon::createFromFormat('m/Y', $item['first_brewed'])
                    : null,
                'abv'             => $item['abv'] ?? 1,
                'ibu'             => (int) $item['ibu'] ?? 1,
                'ebc'             => $item['ebc'] ?? 1,
                'ph'              => $item['ph'] ?? 1,
                'volume'          => (int) number_format($item['volume']['value'], 0),
                'ingredients'     => json_encode($item['ingredients'] ?? []),
                'brewer_tips'     => $item['brewers_tips'] ?? null,
            ]);


            dispatch(new ProcessBeer($beer));
        }

        echo "Importação concluída!\n";
    }
}
