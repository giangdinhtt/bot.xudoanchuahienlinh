<?php

use Illuminate\Database\Seeder;

class AuthViewsSeeder extends Seeder
{
    const DESIGN_DOC = 'bot';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = app('couchdb_auth');
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
        $views = array_merge($views, $this->buildUsersView());

        $payload = [
            '_id' => '_design/' . $ddoc,
            'language' => 'javascript',
            'views' => $views
        ];

        if ($ddocRev != null) {
            $payload['_rev'] = $ddocRev;
        }

        $this->command->info(json_encode($payload));

        $response = $client->request('PUT', "_design/$ddoc", [
            'json' => $payload
        ]);
        $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
        $this->command->info($response->getBody());
    }

    private function buildUsersView()
    {
        $mapFunc = file_get_contents(storage_path('seeder/views/auth_users.map.js'));
        $definition = [
            'users' => [
                'map' => $mapFunc
            ]
        ];
        return $definition;
    }
}
