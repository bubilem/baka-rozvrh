<?php

/**
 * Parser class
 */
class Parser
{
    /**
     * Parse XML from file to JSON string
     *
     * @return string
     */
    public static function xml2json(): string
    {
        $lessonxml = new SimpleXMLElement('data/' . Conf::section() . '-timetable.xml', 0, true);
        $day = date("w") - 1;
        if ($day < 0 || $day > 4) {
            $day = 4; //friday            
        }
        $data = ['day' => $day, 'min-hour' => 99, 'max-hour' => 0, 'week' => '-'];
        foreach ($lessonxml->Timetables[0] as $class) {
            //echo $class->Entity->Abbrev . ' ';
            if (in_array($class->Entity->Abbrev, Conf::get("classes"))) {
                foreach ($class->Cells[0] as $cell) {
                    if ($day != intval($cell->DayIndex)) {
                        continue;
                    }
                    $hour = intval($cell->HourIndex) - 2;
                    if ($hour < $data['min-hour']) {
                        $data['min-hour'] = $hour;
                    }
                    if ($hour > $data['max-hour']) {
                        $data['max-hour'] = $hour;
                    }
                    foreach ($cell->Atoms[0] as $atom) {
                        $week = strval($atom->Cycles[0]->Cycle->Abbrev);
                        if ($data['week'] == '-') {
                            $data['week'] = $week;
                        } else if ($data['week'] !== $week) {
                            $data['week'] = '';
                        }
                        $data['classes'][strval($class->Entity->Abbrev)][$hour][] = [
                            'subject' => strval($atom->Subject->Abbrev),
                            'teacher' => strval($atom->Teacher->Abbrev),
                            'room' => strval($atom->Room->Abbrev),
                            'group' => strval($atom->Group->Abbrev),
                            'week' => $week
                        ];
                    }
                }
            }
        }
        return json_encode($data);
    }

    /**
     * Parse JSON to HTML timetable
     *
     * @param string $json
     * @return string
     */
    public static function json2html(string $json): string
    {
        $arr = json_decode($json, true);
        if (empty($arr['classes'])) {
            return '';
        }
        $days = ['PON', 'ÚTE', 'STŘ', 'ČTV', 'PÁT'];
        $weeks = ['S' => 'SUDÝ', 'L' => 'LICHÝ'];
        $actHour = self::actualHour();
        $html = '';
        $html .= '<tr>';
        $html .= '<td class="datetime"><div id="date">' . $days[$arr['day']] .  (isset($weeks[$arr['week']]) ? '&nbsp;' . $weeks[$arr['week']] : '') . '</div><div id="time"></div></td>';
        for ($hour = $arr['min-hour']; $hour <= $arr['max-hour']; $hour++) {
            $html .= '<td class="hour' . ($actHour['number'] == $hour ? ' ' . $actHour['state'] : '') . '">';
            $html .= '<div class="number">' . $hour . '</div>';
            $fromto = Conf::get("hours", $hour);
            $html .= '<div class="fromto"><div>' . $fromto[0] . '</div><div>-</div><div>' . $fromto[1] . '</div></div>';
            $html .= '</td>';
        }
        $html .= '</tr>' . "\n";
        foreach (Conf::get('classes') as $class) {
            $item = $arr['classes'][$class];
            $html .= '<tr>';
            $html .= '<td class="class">' . $class . '</td>';
            for ($hour = $arr['min-hour']; $hour <= $arr['max-hour']; $hour++) {
                $html .= '<td class="lessons' . ($actHour['number'] == $hour ? ' ' . $actHour['state'] : '') . '">';
                if (!empty($item[$hour])) {
                    foreach ($item[$hour] as $lesson) {
                        $html .= '<div class="lesson">' .
                            '<div class="subject">'  .  $lesson['subject'] . '<sup>'  .  ($lesson['group'] !== 'celá' ? $lesson['group'] : '') . ($arr['week'] ? '' : ' ' . $lesson['week']) . '</sup></div>' .
                            '<div class="teacher-room"><div>'  .  $lesson['teacher'] . '</div><div>'  .  $lesson['room'] . '</div></div>' .
                            '</div>';
                    }
                }
                $html .= '</td>';
            }
            $html .= '</tr>' . "\n";
        }
        return '<table class="timetable">' . "\n" . $html . '</table>' . "\n";
    }

    /**
     * Detect number of actual hour
     *
     * @return array ['number' => 0, 'state' => 'in-progress']
     */
    public static function actualHour(): array
    {
        $actualHour = ['number' => 0, 'state' => 'in-progress'];
        $hours = Conf::get('hours');
        foreach ($hours as $hour => $fromto) {
            if (self::minutes(date("H:i")) >= self::minutes($fromto[1])) {
                $actualHour['number'] = $hour + 1;
            }
        }
        if (self::minutes(date("H:i")) < self::minutes($hours[$actualHour['number']][0])) {
            $actualHour['state'] = 'preparation';
        }
        return $actualHour;
    }

    /**
     * convert time HH:MM to minutes count.
     *
     * @param string $time
     * @return int
     */
    public static function minutes(string $time): int
    {
        $hoursAndMinutes = explode(":", $time);
        return (intval($hoursAndMinutes[0]) * 60) + intval($hoursAndMinutes[1]);
    }
}
