<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MemberController extends Controller
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

    public function unaccents($string) {
        $from = array("à", "ả", "ã", "á", "ạ", "ă", "ằ", "ẳ", "ẵ", "ắ", "ặ", "â", "ầ", "ẩ", "ẫ", "ấ", "ậ", "đ", "è", "ẻ", "ẽ", "é", "ẹ", "ê", "ề", "ể", "ễ", "ế", "ệ", "ì", "ỉ", "ĩ", "í", "ị", "ò", "ỏ", "õ", "ó", "ọ", "ô", "ồ", "ổ", "ỗ", "ố", "ộ", "ơ", "ờ", "ở", "ỡ", "ớ", "ợ", "ù", "ủ", "ũ", "ú", "ụ", "ư", "ừ", "ử", "ữ", "ứ", "ự", "ỳ", "ỷ", "ỹ", "ý", "ỵ", "À", "Ả", "Ã", "Á", "Ạ", "Ă", "Ằ", "Ẳ", "Ẵ", "Ắ", "Ặ", "Â", "Ầ", "Ẩ", "Ẫ", "Ấ", "Ậ", "Đ", "È", "Ẻ", "Ẽ", "É", "Ẹ", "Ê", "Ề", "Ể", "Ễ", "Ế", "Ệ", "Ì", "Ỉ", "Ĩ", "Í", "Ị", "Ò", "Ỏ", "Õ", "Ó", "Ọ", "Ô", "Ồ", "Ổ", "Ỗ", "Ố", "Ộ", "Ơ", "Ờ", "Ở", "Ỡ", "Ớ", "Ợ", "Ù", "Ủ", "Ũ", "Ú", "Ụ", "Ư", "Ừ", "Ử", "Ữ", "Ứ", "Ự", "Ỳ", "Ỷ", "Ỹ", "Ý", "Ỵ");
        $to = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "d", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "D", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y");
        return preg_replace('/\s+/', ' ', str_replace($from, $to, $string));
    }

    public function find(Request $request) {
        $file_path = storage_path('members.json');
        $json = file_get_contents($file_path);
        $persons = json_decode($json, true);

        if (!$request->has('q') || empty($request->input('q'))) {
            return response('Not found', 404);
        }
        $q = strtolower($request->input('q'));
        $q = $this->unaccents($q);
        \Log::warning($q);
        $results = [];
        foreach ($persons as $person) {
            $name = strtolower($this->unaccents($person['name']));
            \Log::info($name);
            if (strpos($name, $q) !== false) {
                $results[] = $person;
            }
        }
        return $results;
    }
}
