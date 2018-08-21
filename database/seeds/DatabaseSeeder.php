<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        $client = app('couchdb_base');
        $database = env('COUCHDB_DATABASE');
        $response = null;
        try {
            $response = $client->request('GET', $database);
            $this->command->info($response->getBody());
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->command->warn($e->getMessage());
            if ($e->getCode() != 404) {
                throw $e;
            }
            $response = $client->request('PUT', $database);
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());
            // Verify database created
            $response = $client->request('GET', $database);
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());
        }

        $auth = env('COUCHDB_AUTH_DATABASE');
        try {
            $response = $client->request('GET', $auth);
            $this->command->info($response->getBody());
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $this->command->warn($e->getMessage());
            if ($e->getCode() != 404) {
                throw $e;
            }
            $response = $client->request('PUT', $auth);
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());
            // Verify database created
            $response = $client->request('GET', $auth);
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());
        }

        $this->call('ViewsSeeder');
    }
}
