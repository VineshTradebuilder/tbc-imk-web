<?php

namespace TBC\IMK\WEB;

class Input {

    public static function get($item) {
        if (isset($_POST[$item])) {
            return Helper::escape($_POST[$item]);
        } else if (isset($_GET[$item])) {
            return Helper::escape($_GET[$item]);
        }
        return '';
    }

}
