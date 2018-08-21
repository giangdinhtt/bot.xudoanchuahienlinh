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
        $definition = [
            'search' => [
                'map' => "function (doc) {\n  if (doc.full_name == undefined) return;\n  if (doc.code) emit(doc.code, 1);\n  if (doc.phone) emit(doc.phone, 1);\n  if (doc.email) {\n    emit(doc.email, 1);\n    var emailParts = doc.email.split('@');\n    if (emailParts.length > 1) emit(emailParts[0], 1);\n  }\n  emit(doc._id, 1);\n  emit(doc.id, 1);\n  var fullName = doc.full_name;\n  var arr = fullName.split(' ');\n  var len = arr.length;\n  var keys = [];\n  for (i = 0; i < len; i ++) {\n      var temp = arr[i];\n      keys.push(temp);\n      for (j = i + 1; j < len; j ++) {\n          temp += ' ' + arr[j];\n          keys.push(temp);\n      }\n  }\n  for (i = 0; i < keys.length; i ++) {\n    emit(keys[i], 1);\n  }\n}"
            ]
        ];
        return $definition;
    }

    private function buildDetailsView()
    {
        $definition = [
            'details' => [
                'map' => "function (doc) {\n  emit(doc._id, 1);\n}"
            ]
        ];
        return $definition;
    }
}
