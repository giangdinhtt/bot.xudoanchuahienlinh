<?php

use Illuminate\Database\Seeder;
use App\Helpers\StringHelper;

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
        $fields = [
            'object_type',
            'full_name', 
            'code', 
            'phone', 
            'email', 
            'facebook', 
            'telegram', 
            'telegram_id', 
            'course', 
            'grade', 
            'parent.mother.phone',
            'parent.mother.email',
            'parent.mother.telegram',
            'parent.mother.telegram_id',
            'parent.father.phone',
            'parent.father.email',
            'parent.father.telegram',
            'parent.father.telegram_id',
        ];
        $payload = [
            'index'=>[
                'fields' => null
            ],
            'name' => null,
            'type' => 'json'
        ];
        foreach ($fields as $field) {
            $payload['index']['fields'] = [$field];
            $payload['name'] = StringHelper::getSlug($field) . '-index';
            $response = $client->request('POST', '_index', [
                'json' => $payload
            ]);
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());
        }
    }
}
