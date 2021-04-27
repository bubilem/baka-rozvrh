<?php
require "class/Loader.php";
Loader::registerClassAutoloader();
Conf::section(filter_input(INPUT_GET, 'section', FILTER_SANITIZE_URL) ?? '-');
?>
<!DOCTYPE html>
<html lang="cs" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Rozvrh hodin</title>
    <link rel="stylesheet" href="css/style.min.css" />
</head>

<body>
    <main>
        <h1>Rozvrh hodin pro VOŠ, SPŠ a SOŠ Varnsdorf p.o.</h1>
        <nav>
            <?php
            foreach (Conf::sections() as $section) {
                echo '<h2>' . strtoupper($section) . '</h2>';
                echo '<ul>';
                echo '<li><a href="rozvrh.html?section=' . $section . '">Rozvrh hodin</a></li>';
                echo '<li><a href="sync.php?section=' . $section . '&timetable-typen=actual">Synchronizace z Bakalářů (aktuální rozvrh)</a></li>';
                echo '<li><a href="sync.php?section=' . $section . '&timetable-type=permanent">Synchronizace z Bakalářů (stálý rozvrh)</a></li>';
                echo '</ul>';
            }
            ?>
        </nav>
    </main>
</body>

</html>