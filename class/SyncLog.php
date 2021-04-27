<?php

/**
 * Sync log class
 */
class SyncLog
{
    private static $items = ['status', 'jsonTime', 'xmlTime', 'hash'];
    private $data;

    public function __construct()
    {
        foreach (self::$items as $item) {
            $this->data[$item] = strpos($item, 'Time') !== false ? 0 : "";
        }
    }

    public function load()
    {
        $contents = @file_get_contents("data/" . Conf::section() . ".json");
        if ($contents) {
            $this->data = json_decode($contents, true);
        }
        return $this;
    }

    public function save()
    {
        file_put_contents("data/" . Conf::section() . ".json", json_encode($this->data));
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (in_array($name, self::$items)) {
            if (isset($arguments[0])) {
                $this->data[$name] = strpos($name, 'Time') !== false ? intval($arguments[0]) : $arguments[0];
            } else {
                return isset($this->data[$name]) ? $this->data[$name] : null;
            }
        }
        return $this;
    }
}
