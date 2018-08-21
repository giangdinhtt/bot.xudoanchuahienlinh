<?php

use Illuminate\Database\Seeder;

class IndexesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = app('couchdb');
        $response = null;
        $payload = [
            'index'=>[],
            'type' => 'json',
            'name' => 'first-name-index'
        ];
        $response = $client->request('POST', '_index', [
            'body' => json_encode($payload)
        ]);
    }
}
