<?php
/* SECTION EXISTS CHECK */
$section = filter_input(INPUT_GET, 'section', FILTER_SANITIZE_URL) ?? '-';
if ($section == '-') {
    messAndDie('no-section');
}

/* TIMETABLE TYPE */
$timetableType = filter_input(INPUT_GET, 'timetable-type', FILTER_SANITIZE_URL);
if (!in_array($timetableType, ['actual', 'permanent'])) {
    $timetableType = 'actual';
}

/* ALLOWED SECTION CHECK */
require "class/Loader.php";
Loader::registerClassAutoloader();
Conf::section($section);
if (!Conf::section()) {
    messAndDie('bad-section');
}

/* LOG LOAD */
$log = (new SyncLog())->load();

/* NO SYNC IF BUSSY */
if ($log->status() == 'bussy') {
    messAndDie('bussy', $log);
}

/* NO SYNC IF FRESH */
if ($log->xmlTime() + xmlUpdateSecInterval() > time()) {
    messAndDie('no-need-sync', $log);
} else {
    /* SYNC */
    $log->status("bussy")->save(); // BUSSY TIME
    $result = Loader::baka2xml($log, $timetableType); // XML LOADING
    if ($result == 'saved' || $log->jsonTime() + 120 < time()) {
        $json = Parser::xml2json(); // JSON GENERATING IF NEEDED
        if (!empty($json) && file_put_contents('data/' . Conf::section() . '-timetable.json', $json)) {
            /* NEW JSON GENERATED */
            $log->status("ready")->jsonTime(time())->save();
            messAndDie($result . " + json-sync", $log);
        }
    }
    $log->status("ready")->save();
    messAndDie($result, $log);
}

function messAndDie(string $status, SyncLog $log = null)
{
    $data = ["status" => $status];
    if ($log !== null) {
        $data['jsonTime'] = $log->jsonTime();
    }
    die(json_encode($data));
}

function getLastPoi(): string
{
    foreach (Conf::get('hours') as $hour => $fromto) {
        /*
        if (date("H:i") >= $fromto[1])) {
            
        }
        */
    }
    return "";
}

function xmlUpdateSecInterval(): int
{
    $weekDay = intval(date("w"));
    if ($weekDay >= 1 && $weekDay <= 5) {
        $hour = intval(date("G"));
        if ($hour > 7 && $hour <= 10) {
            return 300;
        } else if ($hour > 10 && $hour <= 14) {
            return 900;
        }
        return 3600;
    }
    return 60; //7200;
}
