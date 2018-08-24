<?php

namespace App\Helpers;

class StringHelper
{

    /**
     * Creates a slug to be used for pretty URLs
     *
     * @param $string
     * @return mixed
     */
    public static function getSlug($string)
    {
        $string = self::standardize($string);
        $string = self::unaccents($string);
        $string = self::cleanUpSpecialChars($string);
        return strtolower($string);
    }

    /**
     * @param $string
     * @return string
     */
    public static function cleanUpSpecialChars($string)
    {
        //$string = preg_replace( array("`[^a-zA-Z0-9\$_+*'()]`i","`[-]+`") , "-", $string);
        $string = preg_replace(array("`\W`i", "`[-]+`"), "-", $string);
        return trim($string, '-');
    }

    /**
     * @param $string
     * @return string
     */
    public static function standardize($string)
    {
        return preg_replace('/\s+/', ' ', $string);
    }

    /**
     * @param $string
     * @return string
     */
    public static function deaccent($string)
    {
        $from = array("à", "ả", "ã", "á", "ạ", "ă", "ằ", "ẳ", "ẵ", "ắ", "ặ", "â", "ầ", "ẩ", "ẫ", "ấ", "ậ", "đ", "è", "ẻ", "ẽ", "é", "ẹ", "ê", "ề", "ể", "ễ", "ế", "ệ", "ì", "ỉ", "ĩ", "í", "ị", "ò", "ỏ", "õ", "ó", "ọ", "ô", "ồ", "ổ", "ỗ", "ố", "ộ", "ơ", "ờ", "ở", "ỡ", "ớ", "ợ", "ù", "ủ", "ũ", "ú", "ụ", "ư", "ừ", "ử", "ữ", "ứ", "ự", "ỳ", "ỷ", "ỹ", "ý", "ỵ", "À", "Ả", "Ã", "Á", "Ạ", "Ă", "Ằ", "Ẳ", "Ẵ", "Ắ", "Ặ", "Â", "Ầ", "Ẩ", "Ẫ", "Ấ", "Ậ", "Đ", "È", "Ẻ", "Ẽ", "É", "Ẹ", "Ê", "Ề", "Ể", "Ễ", "Ế", "Ệ", "Ì", "Ỉ", "Ĩ", "Í", "Ị", "Ò", "Ỏ", "Õ", "Ó", "Ọ", "Ô", "Ồ", "Ổ", "Ỗ", "Ố", "Ộ", "Ơ", "Ờ", "Ở", "Ỡ", "Ớ", "Ợ", "Ù", "Ủ", "Ũ", "Ú", "Ụ", "Ư", "Ừ", "Ử", "Ữ", "Ứ", "Ự", "Ỳ", "Ỷ", "Ỹ", "Ý", "Ỵ");
        $to   = array("a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "a", "d", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "e", "i", "i", "i", "i", "i", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "o", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "u", "y", "y", "y", "y", "y", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "A", "D", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "E", "I", "I", "I", "I", "I", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "O", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "U", "Y", "Y", "Y", "Y", "Y");
        return str_replace($from, $to, $string);
    }

    /**
     * Decamelize a string
     * Ex: AbcXyz => abc_xyz
     *
     * @param $input
     * @return string
     */
    public static function decamelize($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /**
     * Replace all special characters with space
     *
     * @param $keyword
     * @return string
     */
    public static function escapeSearchTerm($keyword)
    {
        $search  = array('\\', '/', '+', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '"', "'", '~', '*', '?', ':');
        $replace = array(' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', '\"', ' ', ' ', ' ', ' ', ' ');

        return str_replace($search, $replace, $keyword);
    }

    /**
     * @param $number
     * @return mixed
     */
    public static function formatCurrency($number)
    {
        $format = new \NumberFormatter('vi_VN', \NumberFormatter::CURRENCY);
        return $format->formatCurrency($number, "VND");
    }

    /**
     * @param $string
     * @param $len
     * @param $dots
     * @return mixed
     */
    public static function truncate($string, $len = 30, $dots = true)
    {

        $string = strip_tags($string);
        $retVal = $string;

        /*
         * get current encoding:
         * "auto" is expanded to: ASCII,JIS,UTF-8,EUC-JP,SJIS
         */

        $encoding = mb_detect_encoding($string, "auto");

        // leng of string in current encoding
        $strlen = mb_strlen($string, $encoding);

        $delta = $strlen - $len;
        if ($delta > 0) {
            // trim it by length in current encoding
            $shortText = mb_substr($string, 0, $len, $encoding);

            // find the last break word
            $breakPos    = $len;
            $breakPatten = array(" ", ",", ".", ":", "_", "-", "+");
            foreach ($breakPatten as $id => $breakKey) {
                if (mb_strrpos($shortText, $breakKey, $encoding)) {
                    if ($id == 0) {
                        $breakPos = mb_strrpos($shortText, $breakKey, $encoding);
                    } else {
                        $breakPos = ($breakPos > mb_strrpos($shortText, $breakKey, $encoding)) ? $breakPos : mb_strrpos($shortText, $breakKey, $encoding);
                    }
                }
            }

            //remove break word
            $shortText = mb_substr($shortText, 0, $breakPos, $encoding);

            if ($dots) {
                $shortText .= "...";
            }

            $retVal = $shortText;
        }

        return $retVal;
    }

    /**
     * @param $sentence
     * @param $wordCount
     * @return mixed
     */
    public static function substring($sentence, $wordCount)
    {
        preg_match('/(?:[^\s,\.;\?\!]+(?:[\s,\.;\?\!]+|$)){0,' . $wordCount . '}/', $sentence, $matches);
        return $matches[0];
    }

    /**
     * @param $string
     */
    public static function isUTF8($string)
    {
        return (utf8_encode(utf8_decode($string)) == $string);
    }
}
