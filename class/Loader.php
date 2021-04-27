<?php

/**
 * Loader class
 */
class Loader
{
    /**
     * Register class autoloader
     *
     * @return void
     */
    public static function registerClassAutoloader()
    {
        spl_autoload_register(function (string $className) {
            if (file_exists("class/$className.php")) {
                require "class/$className.php";
            } else {
                die("Unable to load class $className.");
            }
        });
    }

    /**
     * Load xml from Bakaláři to local file
     *
     * @return string status message
     */
    public static function baka2xml(SyncLog $log, string $timetableType = 'actual'): string
    {
        ini_set('max_execution_time', 0);
        date_default_timezone_set("Europe/Prague");
        $url = Conf::get("baka-url") . Conf::get("baka-url-$timetableType-timetable");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERPWD, Conf::get("baka-user") . ":" . Conf::get("baka-pass"));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        $content = curl_exec($ch);
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status_code == 200) {
            $newHash = hash("sha256", $content);
            $log->xmlTime(time());
            if ($log->hash() == $newHash) {
                return "no-change";
            }
            if (file_put_contents('data/' . Conf::section() . '-timetable.xml', $content)) {
                $log->hash($newHash);
                return "saved";
            } else {
                return "fail-saving";
            }
        }
        return "fail-$status_code";
    }
}
