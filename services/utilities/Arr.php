<?php

namespace app\services\utilities;

defined('BASEPATH') or exit('No direct script access allowed');

class Arr
{
    public static function toObject($array)
    {
        if (!is_array($array) && !is_object($array)) {
            return new \stdClass();
        }

        return json_decode(json_encode((object) $array));
    }

    public static function flatten(array $array)
    {
        $return = [];
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });

        return $return;
    }

    public static function inMultidimensional($array, $key, $val)
    {
        foreach ($array as $item) {
            if (isset($item[$key]) && $item[$key] == $val) {
                return true;
            }
        }

        return false;
    }

    public static function pluck($array, $key)
    {
        return array_map(function ($v) use ($key) {
            return is_object($v) ? $v->$key : $v[$key];
        }, $array);
    }

    public static function valueExistsByKey($array, $key, $val)
    {
        foreach ($array as $item) {
            if (isset($item[$key]) && $item[$key] == $val) {
                return true;
            }
        }

        return false;
    }

    public static function sortBy($array, $key, $keepIndex = false)
    {
        if (!is_array($array)) {
            return [];
        }

        $func = $keepIndex ? 'usort' : 'uasort';

        $func($array, function ($a, $b) use ($key) {
            return $a[$key] - $b[$key];
        });

        return $array;
    }
}
