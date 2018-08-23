<?php

use Illuminate\Database\Seeder;

class ViewsSeeder extends Seeder
{
    const DESIGN_DOC = 'bot';
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $client = app('couchdb');
        $this->createDesignDocument($client, self::DESIGN_DOC);
    }

    private function createDesignDocument($client, $ddoc)
    {
        // Check design document exists
        $ddocRev = null;
        try {
            $response = $client->request('GET', "_design/$ddoc");
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());

            $doc = json_decode($response->getBody(), true);
            if (isset($doc['_rev'])) $ddocRev = $doc['_rev'];
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->command->warn($e->getMessage());
            if ($e->getCode() != 404) {
                throw $e;
            }
        }

        $views = [];
        $views = array_merge($views, $this->buildSearchView());
        $views = array_merge($views, $this->buildDetailsView());

        $this->command->info(json_encode([
            '_id' => '_design/' . self::DESIGN_DOC,
            '_rev' => $ddocRev,
            'language' => 'javascript',
            'views' => $views
        ]));

        $payload = [
            '_id' => '_design/' . self::DESIGN_DOC,
            'language' => 'javascript',
            'views' => $views
        ];
        if ($ddocRev != null) {
            $payload[_rev] = $ddocRev;
        }
        $response = $client->request('PUT', "_design/$ddoc", [
            'json' => $payload
        ]);
        $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
        $this->command->info($response->getBody());
    }

    private function buildSearchView()
    {
        $mapFunc = file_get_contents(storage_path('seeder/views/search.map.js'););
        $reduceFunc = file_get_contents(storage_path('seeder/views/search.reduce.js'););
        $definition = [
            'search' => [
                'map' => $mapFunc,
                'reduce' => $reduceFunc
            ]
        ];
        return $definition;
    }

    private function buildDetailsView()
    {
        $mapFunc = file_get_contents(storage_path('seeder/views/details.js'));
        $definition = [
            'details' => [
                'map' => $mapFunc
            ]
        ];
        return $definition;
    }
}
