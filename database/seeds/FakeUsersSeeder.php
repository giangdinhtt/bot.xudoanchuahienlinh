<?php

use Faker\Factory;
use Illuminate\Database\Seeder;

class FakeUsersSeeder extends Seeder
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
            'code' => 'thieu_nhi_1'
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

    private $grades = [];
    private $courses = [];
    private $teachers = [];

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
        $client = app('couchdb');
        $this->fakeGrades($faker, $client);
        $this->fakeCourses($faker, $client);
        $this->fakeTeachers($faker, $client);
        $this->fakeStudents($faker, $client);
    }

    private function fakeGrades($faker, $client)
    {
        $this->command->info('Generating grades...');
        foreach (self::GRADES as $grade) {
            $this->grades[] = [
                '_id' => 'grade_' . $grade['id'],
                'object_type' => 'grade',
                'id' => 'grade_' . $grade['id'],
                'code' => $grade['code'],
                'name' => $grade['name'],
                'is_active' => 1
            ];
        }
        $this->bulkDocs($client, $this->grades);
        $this->command->info('Generate ' . count($this->grades) . ' grades completed');
    }

    private function fakeCourses($faker, $client)
    {
        $this->command->info('Generating courses...');
        $courseIndex = 0;
        foreach ($this->grades as $grade) {
            $numberOfCourses = $faker->numberBetween(2, 7);
            for ($i = 0; $i < $numberOfCourses; $i++) {
                $courseId = ++$courseIndex;
                $this->courses[] = [
                    '_id' => 'courses_' . $courseId,
                    'object_type' => 'course',
                    'id' => 'courses_' . $courseId,
                    'code' => $grade['code'] . '_' . $courseId,
                    'name' => $grade['name'] . '/' . $courseId,
                    'grade' => $grade['id']
                ];
            }
        }
        $this->bulkDocs($client, $this->courses);
        $this->command->info('Generate ' . count($this->courses) . ' courses completed');
    }

    private function fakeTeachers($faker, $client)
    {
        $this->command->info('Generating teachers...');
        $courseLen = sizeof($this->courses);
        $teachers = [];
        for ($i = 0; $i < 100; $i ++) {
            $id = $i + 1;
            $fullName = $faker->name();
            $fullName = str_replace(".","",$fullName);
            $courseId = $faker->numberBetween(0, $courseLen - 1);
            $course = $this->courses[$courseId];
            $teachers[] = [
                '_id' => (string) $id,
                'object_type' => 'teacher',
                'id' => $id,
                'first_name' => substr($fullName, 0, strpos($fullName, ' ')),
                'last_name' => substr($fullName, strpos($fullName, ' ') + 1, strlen($fullName)),
                'full_name' => $fullName,
                'name' => $fullName,
                'code' => (string) $faker->unique()->randomNumber($nbDigits = 9),
                'phone' => ($faker->boolean ? '090' : '0122') . $faker->numberBetween(1000001, 9999999),
                'gender' => $faker->boolean ? 1 : 0,
                'birthday' => $faker->date(),
                'email' => $faker->email,
                'facebook' => $faker->userName,
                'telegram_id' => null,
                'telegram' => $faker->userName,
                'course' => $course['id'],
                'grade' => $course['grade']
            ];
        }

        $batch = [];
        foreach($teachers as $user) {
            if (count($batch) < 1000) {
                $batch[] = $user;
            } else {
                $this->bulkDocs($client, $batch);
                $this->command->info('Generated ' . count($batch) . ' teachers');
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            $this->bulkDocs($client, $batch);
            $this->command->info('Generated ' . count($batch) . ' teachers');
        }
        $this->command->info('Generate ' . count($this->teachers) . ' teachers completed');
    }

    private function fakeStudents($faker, $client)
    {
        $this->command->info('Generating students...');
        $courseLen = sizeof($this->courses);
        $users = [];
        for ($i = 0; $i < 10000 ; $i ++) {
            $id = $i + 1;
            $fullName = $faker->name();
            $fullName = str_replace(".","",$fullName);
            $courseId = $faker->numberBetween(0, $courseLen - 1);
            $course = $this->courses[$courseId];
            $users[] = [
                '_id' => (string) $id,
                'object_type' => 'student',
                'id' => $id,
                'first_name' => substr($fullName, 0, strpos($fullName, ' ')),
                'last_name' => substr($fullName, strpos($fullName, ' ') + 1, strlen($fullName)),
                'full_name' => $fullName,
                'name' => $fullName,
                'code' => (string) $faker->unique()->randomNumber($nbDigits = 9),
                'phone' => ($faker->boolean ? '090' : '0122') . $faker->numberBetween(1000001, 9999999),
                'gender' => $faker->boolean ? 1 : 0,
                'birthday' => $faker->date(),
                'email' => $faker->email,
                'facebook' => $faker->userName,
                'telegram_id' => null,
                'telegram' => $faker->userName,
                'course' => $course['id'],
                'grade' => $course['grade'],
                'parent' => [
                    'mother' => [
                        'full_name' => str_replace(".","", $faker->name()),
                        'phone' => ($faker->boolean ? '090' : '0122') . $faker->numberBetween(1000001, 9999999),
                        'email' => $faker->email,
                        'telegram_id' => null,
                        'telegram' => $faker->userName
                    ],
                    'father' => [
                        'full_name' => str_replace(".","", $faker->name()),
                        'phone' => ($faker->boolean ? '090' : '0122') . $faker->numberBetween(1000001, 9999999),
                        'email' => $faker->email,
                        'telegram_id' => null,
                        'telegram' => $faker->userName
                    ],
                ]
            ];
        }

        $batch = [];
        foreach($users as $user) {
            if (count($batch) < 1000) {
                $batch[] = $user;
            } else {
                $this->bulkDocs($client, $batch);
                $this->command->info('Generated ' . count($batch) . ' students');
                $batch = [];
            }
        }
        if (count($batch) > 0) {
            $this->bulkDocs($client, $batch);
            $this->command->info('Generated ' . count($batch) . ' students');
        }

        $this->command->info('Generate ' . count($users) . ' students completed');
    }

    private function bulkDocs($client, $docs)
    {
        $response = $client->request('POST', '_bulk_docs', ['json' => [
            'docs' => $docs,
            //'new_edits' => false
        ]]);
        $this->command->info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
    }
}
