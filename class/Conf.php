<?php

/**
 * Simple JSON configuration class
 */
class Conf
{
    /**
     * School section
     *
     * @var string
     */
    private static $section = "";

    /**
     * Configuration data
     *
     * @var array
     */
    private static $data = [];

    public static function section($value = null)
    {
        if ($value === null) {
            return self::$section;
        } else {
            self::load();
            if (!empty($value) && isset(self::$data['section'][$value])) {
                self::$section = $value;
            }
        }
    }

    /**
     * Get all sections in array
     *
     * @return array
     */
    public static function sections(): array
    {
        return array_keys(self::$data['section']);
    }

    /**
     * Load data from JSON file
     *
     * @param string $filename
     * @return bool success
     */
    public static function load($filename = "conf.json"): bool
    {
        $content = file_get_contents($filename);
        if (!$content) {
            return false;
        }
        $data = json_decode($content, true);
        if (!$data || !is_array($data)) {
            return false;
        }
        self::$data = $data;
        return true;
    }

    /**
     * Get value from configuration data by keys
     *
     * @param string $key1
     * @param string $key2
     * @param string $key3
     * @return mixed
     */
    public static function get(string $key1, string $key2 = null)
    {
        if ($key2 !== null) {
            if (isset(self::$data[$key1][$key2])) {
                return self::$data[$key1][$key2];
            } else if (isset(self::$data['section'][self::$section][$key1][$key2])) {
                return self::$data['section'][self::$section][$key1][$key2];
            } else {
                return null;
            }
        } else if (isset(self::$data[$key1])) {
            return self::$data[$key1];
        } else if (isset(self::$data['section'][self::$section][$key1])) {
            return self::$data['section'][self::$section][$key1];
        } else {
            return null;
        }
    }
}
