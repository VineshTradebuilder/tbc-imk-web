<?php
namespace TBC\IMK\WEB;

class Helper {

    public static function dateFormat($date, $formate = 'F jS, Y') {
        if ($date) {
            $dt = new DateTime($date);
            return $dt->format($formate); // 10/27/2014
        }
    }
    
    private static function assets_path(){
        if( !defined("BASE_URL") ){
            throw new Exception("Please Define BASE_URL");
        }
        return BASE_URL. "/vendor/tbc/imk/src/assets/";
    }

    public static function getEnqueueScript(){
        $path = self::assets_path();
        $enqueue_scripts = [];
        $enqueue_scripts[] = $path. "scripts/script.js";
        $scriptStr = "";
        foreach( $enqueue_scripts as $script ) {
            $scriptStr.="<script src='$script' ></script>";
        }
        return $scriptStr;
    }

    public static function getEnqueueStyle(){
        $path = self::assets_path();
        $enqueue_styles = [];
        $enqueue_styles[] = $path. "css/style.css";
        $styleStr = "";
        foreach( $enqueue_styles as $style ) {
            $styleStr.="<link rel='stylesheet' href='$style' >";
        }
        return $styleStr;
    }

    public static function escape($string) {
        if (is_string($string))
            return htmlentities($string, ENT_QUOTES, 'UTF-8');
        return $string;
    }

    public static function currency($value, $decimals = 2) {
        return "$" . number_format($value, $decimals);
    }

    public static function humanize($datetime) {
        $created = new Carbon($datetime);
        $now = Carbon::now();
        return ($created->diff($now)->days < 1) ? 'today' : $created->diffForHumans($now);
    }

    static function limit_words($string, $word_limit) {
        $endStr = '';
        $words = explode(" ", $string);
        if (count($words) > $word_limit) {
            $endStr = '...';
        }
        return implode(" ", array_splice($words, 0, $word_limit)) . $endStr;
    }

    public static function input($arr, $field) {
        if (is_array($arr)) {
            return isset($arr[$field]) ? $arr[$field] : '';
        } else if (is_object($arr)) {
            return isset($arr->{$field}) ? $arr->{$field} : '';
        } else {
            return '';
        }
    }

    public static function get($str) {
        if (isset($_GET[$str])) {
            return $_GET[$str];
        } else if (isset($_POST[$str])) {
            return $_POST[$str];
        } else {
            return '';
        }
    }

    public static function getAll() {
        if (count($_GET)) {
            return array_filter($_GET);
        } else if (count($_POST)) {
            return array_filter($_POST);
        } else {
            return [];
        }
    }

    public static function dashesToCamelCase($string, $capitalizeFirstCharacter = false) {

        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    static function setQueryString($url, $key, $val) {
        $pUrl = parse_url($url);
        if (isset($pUrl['query']))
            parse_str($pUrl['query'], $pUrl['query']);
        else
            $pUrl['query'] = [];
        $pUrl['query'][$key] = $val;

        $scheme = isset($pUrl['scheme']) ? $pUrl['scheme'] . '://' : '';
        $host = isset($pUrl['host']) ? $pUrl['host'] : '';
        $path = isset($pUrl['path']) ? $pUrl['path'] : '';
        $path = count($pUrl['query']) > 0 ? $path . '?' : $path;

        return $scheme . $host . $path . http_build_query($pUrl['query']);
    }

    public static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function state_abbr($name, $get = 'abbr') {
        if ($get != 'name') {
            $name = strtolower($name);
            $name = ucwords($name);
        } else {
            $name = strtoupper($name);
        }
        $states = array(
            'Alabama' => 'AL',
            'Alaska' => 'AK',
            'Arizona' => 'AZ',
            'Arkansas' => 'AR',
            'California' => 'CA',
            'Colorado' => 'CO',
            'Connecticut' => 'CT',
            'Delaware' => 'DE',
            'Florida' => 'FL',
            'Georgia' => 'GA',
            'Hawaii' => 'HI',
            'Idaho' => 'ID',
            'Illinois' => 'IL',
            'Indiana' => 'IN',
            'Iowa' => 'IA',
            'Kansas' => 'KS',
            'Kentucky' => 'KY',
            'Louisiana' => 'LA',
            'Maine' => 'ME',
            'Maryland' => 'MD',
            'Massachusetts' => 'MA',
            'Michigan' => 'MI',
            'Minnesota' => 'MN',
            'Mississippi' => 'MS',
            'Missouri' => 'MO',
            'Montana' => 'MT',
            'Nebraska' => 'NE',
            'Nevada' => 'NV',
            'New Hampshire' => 'NH',
            'New Jersey' => 'NJ',
            'New Mexico' => 'NM',
            'New York' => 'NY',
            'North Carolina' => 'NC',
            'North Dakota' => 'ND',
            'Ohio' => 'OH',
            'Oklahoma' => 'OK',
            'Oregon' => 'OR',
            'Pennsylvania' => 'PA',
            'Rhode Island' => 'RI',
            'South Carolina' => 'SC',
            'South Dakota' => 'SD',
            'Tennessee' => 'TN',
            'Texas' => 'TX',
            'Utah' => 'UT',
            'Vermont' => 'VT',
            'Virginia' => 'VA',
            'Washington' => 'WA',
            'West Virginia' => 'WV',
            'Wisconsin' => 'WI',
            'Wyoming' => 'WY'
        );
        if ($get == 'name') {
            // in this case $name is actually the abbreviation of the state name and you want the full name
            $states = array_flip($states);
        }

        return $states[$name];
    }

    public static function un_slug($str, $capitalizeFirstCharacter = false) {
        $str = ucwords(str_replace("_", " ", $str));

        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    static function setHeader($code = NULL) {
        if (!function_exists('http_response_code')) {

            function http_response_code($code = NULL) {

                if ($code !== NULL) {

                    switch ($code) {
                        case 100: $text = 'Continue';
                            break;
                        case 101: $text = 'Switching Protocols';
                            break;
                        case 200: $text = 'OK';
                            break;
                        case 201: $text = 'Created';
                            break;
                        case 202: $text = 'Accepted';
                            break;
                        case 203: $text = 'Non-Authoritative Information';
                            break;
                        case 204: $text = 'No Content';
                            break;
                        case 205: $text = 'Reset Content';
                            break;
                        case 206: $text = 'Partial Content';
                            break;
                        case 300: $text = 'Multiple Choices';
                            break;
                        case 301: $text = 'Moved Permanently';
                            break;
                        case 302: $text = 'Moved Temporarily';
                            break;
                        case 303: $text = 'See Other';
                            break;
                        case 304: $text = 'Not Modified';
                            break;
                        case 305: $text = 'Use Proxy';
                            break;
                        case 400: $text = 'Bad Request';
                            break;
                        case 401: $text = 'Unauthorized';
                            break;
                        case 402: $text = 'Payment Required';
                            break;
                        case 403: $text = 'Forbidden';
                            break;
                        case 404: $text = 'Not Found';
                            break;
                        case 405: $text = 'Method Not Allowed';
                            break;
                        case 406: $text = 'Not Acceptable';
                            break;
                        case 407: $text = 'Proxy Authentication Required';
                            break;
                        case 408: $text = 'Request Time-out';
                            break;
                        case 409: $text = 'Conflict';
                            break;
                        case 410: $text = 'Gone';
                            break;
                        case 411: $text = 'Length Required';
                            break;
                        case 412: $text = 'Precondition Failed';
                            break;
                        case 413: $text = 'Request Entity Too Large';
                            break;
                        case 414: $text = 'Request-URI Too Large';
                            break;
                        case 415: $text = 'Unsupported Media Type';
                            break;
                        case 500: $text = 'Internal Server Error';
                            break;
                        case 501: $text = 'Not Implemented';
                            break;
                        case 502: $text = 'Bad Gateway';
                            break;
                        case 503: $text = 'Service Unavailable';
                            break;
                        case 504: $text = 'Gateway Time-out';
                            break;
                        case 505: $text = 'HTTP Version not supported';
                            break;
                        default:
                            exit('Unknown http status code "' . htmlentities($code) . '"');
                            break;
                    }

                    $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

                    header($protocol . ' ' . $code . ' ' . $text);

                    $GLOBALS['http_response_code'] = $code;
                } else {

                    $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);
                }

                return $code;
            }

        }
        http_response_code($code);
    }

}
