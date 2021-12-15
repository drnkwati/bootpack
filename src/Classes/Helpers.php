<?php
namespace Drnkwati\Bootpack\Classes;

class Helpers
{
    /**
     * Get a string between two substrings.
     *
     * @author Erik Campobadal Fores <soc@erik.cat>
     * @copyright 2017 erik.cat
     * @param string $var1
     * @param string $var2
     * @param string $pool
     * @return string
     */
    public static function getBetween($var1, $var2, $pool)
    {
        $temp1 = strpos($pool, $var1) + strlen($var1);

        $result = substr($pool, $temp1, strlen($pool));

        $dd = strpos($result, $var2);

        if ($dd == 0) {
            $dd = strlen($result);
        }

        return substr($result, 0, $dd);
    }

    /**
     * Copies the directory to a given location.
     *
     * @author Erik Campobadal Fores <soc@erik.cat>
     * @copyright 2017 erik.cat
     * @param string $src
     * @param string $dst
     * @return void
     */
    public static function copyDir($src, $dst)
    {
        $dir = opendir($src);

        @mkdir($dst);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    static::copyDir($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }

        closedir($dir);
    }

    /**
     * Replace a string from a file.
     *
     * @author Erik Campobadal Fores <soc@erik.cat>
     * @copyright 2017 erik.cat
     * @param string $find
     * @param string $replace
     * @param string $file_path
     * @return void
     */
    public static function strReplaceFile($find, $replace, $file_path)
    {
        file_put_contents($file_path, str_replace($find, $replace, file_get_contents($file_path)));
    }

    /**
     * Replace a string from each file in a given directory.
     *
     * @author Erik Campobadal Fores <soc@erik.cat>
     * @copyright 2017 erik.cat
     * @param string $find
     * @param string $replace
     * @param string $path
     * @return void
     */
    public static function massStrReplaceFile($find, $replace, $path)
    {
        $dir = opendir($path);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($path . '/' . $file)) {
                    static::massStrReplaceFile($find, $replace, $path . '/' . $file);
                } else {
                    static::strReplaceFile($find, $replace, $path . '/' . $file);
                }
            }
        }

        closedir($dir);
    }
}
