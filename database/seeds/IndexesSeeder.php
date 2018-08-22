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
            'index'=>[
                'fields' => ['full_name']
            ],
            'type' => 'json',
            'name' => 'full-name-index'
        ];
        $response = $client->request('POST', '_index', [
            'json' => $payload
        ]);
    }
}
