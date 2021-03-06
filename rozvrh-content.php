<?php
require "class/Loader.php";
Loader::registerClassAutoloader();
Conf::section(filter_input(INPUT_GET, 'section', FILTER_SANITIZE_URL) ?? '-');
if (Conf::section()) {
    echo Parser::json2html(@file_get_contents('data/' . Conf::section() . '-timetable.json'));
?>
    <script>
        setInterval(function() {
            let date = new Date();
            let h = date.getHours();
            let m = date.getMinutes();
            let s = date.getSeconds();
            h = h < 10 ? "0" + h : h;
            m = m < 10 ? "0" + m : m;
            s = s < 10 ? "0" + s : s;
            document.getElementById("time").innerText = h + ":" + m + ":" + s;
        }, 1000);
        <?php
        $arr = [];
        foreach (Conf::get("hours") as $fromto) {
            $from = explode(":", $fromto[0]);
            $to = explode(":", $fromto[1]);
            $arr[] = [$from[0] * 60 + $from[1], $to[0] * 60 + $to[1]];
        }
        echo "var hours = " . json_encode($arr) . ";\n";
        ?>
        var lastActualHour = -1;
        var lastPreparationHour = -1;
        setInterval(function() {
            var date = new Date();
            var actualTime = date.getHours() * 60 + date.getMinutes();
            var actualHour = -1;
            var hour = 0;
            for (const startstop of hours) {
                if (startstop[0] <= actualTime && actualTime < startstop[1]) {
                    actualHour = hour;
                    break;
                }
                hour++;
            }
            if (actualHour != -1 && lastActualHour != actualHour) {
                for (let td of document.getElementsByClassName("hour")) {
                    td.classList.remove("preparation");
                }
                for (let td of document.getElementsByClassName("lessons")) {
                    td.classList.remove("preparation");
                }
                if (actualHour != -1) {
                    for (let td of document.getElementsByClassName("hour-" + actualHour)) {
                        td.classList.add("in-progress");
                    }
                }
                console.log("Act: " + actualHour + ", LastAct: " + lastActualHour);
                lastActualHour = actualHour;
            } else {
                var hour = 0;
                var preparationHour = -1;
                var preStartstop = -1;
                for (const startstop of hours) {
                    if (preStartstop != -1 && preStartstop[1] <= actualTime && actualTime < startstop[0]) {
                        preparationHour = hour;
                        break;
                    }
                    preStartstop = startstop;
                    hour++;
                }
                if (preparationHour != -1 && lastPreparationHour != preparationHour) {
                    for (let td of document.getElementsByClassName("hour")) {
                        td.classList.remove("in-progress");
                    }
                    for (let td of document.getElementsByClassName("lessons")) {
                        td.classList.remove("in-progress");
                    }
                    if (preparationHour != -1) {
                        for (let td of document.getElementsByClassName("hour-" + preparationHour)) {
                            td.classList.add("preparation");
                        }
                    }
                    console.log("Prep: " + preparationHour + ", LastPrep: " + lastPreparationHour);
                    lastPreparationHour = preparationHour;
                }
            }
        }, 2000);
    </script>
<?php
} else {
    echo '<p>Chyba v??b??ru sekce.</p>';
}
