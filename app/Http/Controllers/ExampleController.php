<?php

namespace App\Http\Controllers;

use App\Helpers\StringHelper;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Place your BotMan logic here.
     */
    public function handle(Request $request)
    {
        \Log::info($request->all());
        $q = $request->input('q');
        $client = app('couchdb');
        $response = $client->request('GET', "_design/bot/_view/search", [
            'query' => [
                'key' => StringHelper::quote($q)
            ]
        ]);
        \Log::info($response->getStatusCode() . ' - ' . $response->getReasonPhrase());
        \Log::info($response->getBody());
        $payload = json_decode($response->getBody(), true);
        $result = $this->parse($payload);
        return $result;
    }

    private function parse($payload)
    {
        if (!$payload) {
            return $payload;
        }

        /*
         * {
         *      rows: {
         *          key: null,
         *          value: {
         *              count: 161,
         *              ids: [],
         *              grades: {
         *                  grade_1: {
         *                      count: 2,
         *                      courses: {
         *                          courses_4: {
         *                              count: 2,
         *                              ids: ["2349", "8859"]
     *                              }
         *                      }
         *                  }
         *              }
         *          }
         *      }
         * }
         */
        $rows = $payload['rows'];
        if (!$rows || !count($rows)) {
            return [];
        }

        $studentResults = [];   // individual students
        $courseResults = [];    // course that contains only 1 student matched
        $gradeResults = [];     // grades that contains many students matched

        $summary = $rows[0]['value'];
        $total = $summary['count'];
        $grades = $summary['grades'];
        foreach (array_keys($grades) as $gradeId) {
            $grade = $grades[$gradeId];
            $grade['id'] = $gradeId;
            $gradeTotal = (int) $grade['count'];
            $courses = $grade['courses'];

            if ($gradeTotal > 1) {
                // matched students came from many courses
                if (count(array_keys($courses)) > 1) {
                    $gradeResults[] = $grade;
                } else {
                    // all matched students came from 1 course
                    $courseId = array_keys($courses)[0];
                    $course = $courses[$courseId];
                    $course['id'] = $courseId;
                    $courseResults[] = $course;
                }
                continue;
            }

            // At this time, grade only have 1 matched student
            foreach (array_keys($courses) as $courseId) {
                $course = $courses[$courseId];
                $courseTotal = $course['count'];
                $studentIds = $course['ids'];
                $studentResults[] = $studentIds[0];
            }
        }

        return [
            'students' => $studentResults,
            'courses' => $courseResults,
            'grades' => $gradeResults
        ];
    }
}
