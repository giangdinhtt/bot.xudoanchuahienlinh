<?php

use GuzzleHttp\Client;
use Faker\Factory;
use Illuminate\Database\Seeder;

class UsersTableFakeSeeder extends Seeder
{
    const GRADES = [
        [
            'id' => 1,
            'name' => 'Chiên Con',
            'code' => 'chien_con'
        ],
        [
            'id' => 2,
            'name' => 'Ấu Nhi 1',
            'code' => 'au_nhi_1'
        ],
        [
            'id' => 3,
            'name' => 'Ấu Nhi 2',
            'code' => 'au_nhi_2'
        ],
        [
            'id' => 4,
            'name' => 'Ấu Nhi 3',
            'code' => 'au_nhi_3'
        ],
        [
            'id' => 5,
            'name' => 'Thiếu Nhi 1',
            'code' => 'thiếu_nhi_1'
        ],
        [
            'id' => 6,
            'name' => 'Thiếu Nhi 2',
            'code' => 'thieu_nhi_2'
        ],
        [
            'id' => 7,
            'name' => 'Thiếu Nhi 3',
            'code' => 'thieu_nhi_3'
        ],
        [
            'id' => 8,
            'name' => 'Nghĩa Sĩ 1',
            'code' => 'nghia_si_1'
        ],
        [
            'id' => 9,
            'name' => 'Nghĩa Sĩ 2',
            'code' => 'nghia_si_2'
        ],
        [
            'id' => 10,
            'name' => 'Nghĩa Sĩ 3',
            'code' => 'nghia_si_3'
        ],
        [
            'id' => 11,
            'name' => 'Hiệp Sĩ 1',
            'code' => 'hiep_si_1'
        ],
        [
            'id' => 12,
            'name' => 'Hiệp Sĩ 2',
            'code' => 'hiep_si_2'
        ],
        [
            'id' => 13,
            'name' => 'Dự Trưởng',
            'code' => 'du_truong'
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $faker->addProvider(new Faker\Provider\vi_VN\Person($faker));
        $faker->addProvider(new Faker\Provider\vi_VN\PhoneNumber($faker));
        $users = [];
        $gradeLen = sizeof(self::GRADES);
        for ($i = 0; $i < 10000 ; $i ++) {
            $id = $i + 1;
            $fullName = $faker->name();
            $fullName = str_replace(".","",$fullName);
            $gradeId = $faker->numberBetween(0, $gradeLen - 1);
            $grade = self::GRADES[$gradeId];
            $levelId = $faker->numberBetween(1, 5);
            $users[] = [
                'id' => $id,
                'first_name' => substr($fullName, 0, strpos($fullName, ' ')),
                'last_name' => substr($fullName, strpos($fullName, ' ') + 1, strlen($fullName)),
                'full_name' => $fullName,
                'name' => $fullName,
                'phone' => ($faker->boolean ? '090' : '0122') . $faker->numberBetween(1000001, 9999999),
                'gender' => $faker->boolean ? 1 : 0,
                'birthday' => $faker->date(),
                'email' => $faker->email,
                'facebook' => $faker->userName,
                'level' => [
                    'id' => $faker->numberBetween(1, 70),
                    'name' => $grade['name'] . '/' . $levelId,
                    'code' => $grade['code'] . '_' . $levelId,
                    'grade' => [
                        'id' => $gradeId,
                        'name' => $grade['name'],
                        'code' => $grade['code']
                    ]
                ],
                'grade' => [
                    'id' => $gradeId,
                    'name' => $grade['name'],
                    'code' => $grade['code']
                ]
            ];
        }
        ;
        $baseUrl = sprintf('http://%s:%s@%s/', urlencode(env('COUCHDB_USERNAME')), urlencode(env('COUCHDB_PASSWORD')), env('COUCHDB_HOST'));
        $client = new Client(['base_uri' => $baseUrl]);
        $headers = ['Content-type' => 'application/json'];
        $response = null;
        try {
            $response = $client->request('GET', 'fake_users');
        } catch (\Exception $e) {
            if ($response == null || $response->getStatusCode() != 404) {
                throw $e;
            }
            $response = $client->request('PUT', 'fake_users', ['headers' => $headers]);
        }
        $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
        $this->command->info($response->getBody());

        foreach ($users as $user) {
            $response = $client->request('PUT', 'fake_users/' . $user['id'], ['json' => $user]);
            $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
            $this->command->info($response->getBody());
        }
        $this->command->info(json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
