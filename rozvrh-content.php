<?php
require "class/Loader.php";
Loader::registerClassAutoloader();
Conf::section(filter_input(INPUT_GET, 'section', FILTER_SANITIZE_URL) ?? '-');
if (Conf::section()) {
    echo Parser::json2html(@file_get_contents('data/' . Conf::section() . '-timetable.json'));
?>
    <script>
        function showTime() {
            var date = new Date();
            var h = date.getHours();
            var m = date.getMinutes();
            var s = date.getSeconds();
            h = h < 10 ? "0" + h : h;
            m = m < 10 ? "0" + m : m;
            s = s < 10 ? "0" + s : s;
            var time = h + ":" + m + ":" + s;
            document.getElementById("time").innerText = time;
            document.getElementById("time").textContent = time;
            setTimeout(showTime, 1000);
        }
        showTime();
    </script>
<?php
} else {
    echo '<p>Chyba výběru sekce.</p>';
}
