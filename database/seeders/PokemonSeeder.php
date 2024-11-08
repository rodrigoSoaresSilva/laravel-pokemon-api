<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;

class PokemonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $apiUrl = 'https://pokeapi.co/api/v2/pokemon';

        // Set the result limit per page
        $limit = 250;
        $offset = 0;
        $total = 1025; // Total number of pokemons

        // Loop to make requests while there are records to fetch
        while ($offset < $total) {
            // Adjust the limit to avoid exceeding the total
            $currentLimit = min($limit, $total - $offset);
            
            // Make the request for the current page
            $response = $this->fetchPokemonData($apiUrl, $currentLimit, $offset);

            if ($response->successful()) {
                // Get Pokémon data
                $pokemons = $response->json()['results'];

                // Iterate over the data and insert it into the database
                foreach ($pokemons as $pokemonData) {
                    if (!is_null($pokemonData['url'])) {
                        $response = Http::withOptions([
                            'verify' => false,
                        ])->get($pokemonData['url']);

                        $pokemon = $response->json();

                        $weight = $pokemon['weight'] / 10;
                        $height = $pokemon['height'] * 10;

                        Pokemon::Create([
                            'name' => $pokemon['name'],
                            'type' => $this->getConcatenatedTypes($pokemon['types']),
                            'weight' => $weight,
                            'height' => $height,
                            'image' => $this->getFrontDefault($pokemon['sprites']),
                        ]);
                    }
                }

                // Increment the offset to fetch the next page
                $offset += $limit;
            } else {
                $this->command->error("Failed to access API on page with offset $offset.");
                break;
            }
        }
    }

     /**
     * Encapsulated function to fetch Pokémon data from API.
     *
     * @param string $url
     * @param int|null $limit
     * @param int|null $offset
     * @return \Illuminate\Http\Client\Response|null
     */
    private function fetchPokemonData(string $url, int $limit = null, int $offset = null)
    {
        $queryParams = array_filter([
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return Http::withOptions(['verify' => false])->get($url, $queryParams);
    }

    /**
     * Function to get the front_default value according to specified logic.
     *
     * @param array $sprites
     * @return string|null
     */
    private function getFrontDefault(array $sprites)
    {
        // Check if showdown's front_default exists and is not null
        if (isset($sprites['other']['showdown']['front_default']) && !is_null($sprites['other']['showdown']['front_default'])) {
            return $sprites['other']['showdown']['front_default'];
        }

        // Otherwise, return official-artwork's front_default if it exists
        return $sprites['other']['official-artwork']['front_default'] ?? $sprites['front_default'];
    }

    /**
     * Function to get concatenated type names separated by commas.
     *
     * @param array $types
     * @return string
     */
    private function getConcatenatedTypes(array $types): string
    {
        // Extract the 'name' from each type and join with commas
        return implode(', ', array_map(function ($type) {
            return $type['type']['name'];
        }, $types));
    }
}
