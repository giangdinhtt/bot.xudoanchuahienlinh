<?php

namespace App\Helpers;

class CouchDbHelper
{
    /**
     * Quote a string with double quotes
     *
     * @param $string
     * @return string
     */
    public static function queryView($ddoc='bot', $view, $key=null, $startKey=null, $endKey=null)
    {
        $client = app('couchdb');
        $query = [];
        if (!empty($key)) {
            $query['key'] = StringHelper::quote($key);
        }
        if (!empty($startKey)) {
            $query['startKey'] = StringHelper::quote($startKey);
        }
        if (!empty($endKey)) {
            $query['endKey'] = StringHelper::quote($endKey);
        }
        $response = $client->request('GET', "_design/$ddoc/_view/$view", ['query' => $query]);
        $payload = $response->getBody();
        return json_decode($payload, true);
    }
}
