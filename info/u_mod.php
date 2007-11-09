<?php
    define("MIN_UMOD_TIME",$conf['hmode_days']['v']);
    define("USER_INACTIVE_DELETE",$conf['user_inactive_days']['p1']);
    define("USER_INACTIVE_SHOW",$conf['user_inactive_days']['v']);
    define("USER_NOTLOGIN_DELETE",$conf['user_inactive_days']['v']);

    echo "<h2>Urlaubsmodus</h2>";
		helpNavi(array("Urlaubsmodus","u_mod"));
    infobox_start("Urlaubsmodus");
    echo "<div align=\"justify\">";
    echo "Wenn ein User seinen Account ".USER_INACTIVE_DELETE." Tage nicht ben&uuml;tzt, wird er vom System gel&ouml;scht! Ist man aus irgendeinem Grund verhindert zu spielen, gibt es die M&ouml;glichkeit in den Urlaubsmodus zu treten. Unter <a href=\"?page=userconfig\">Einstellungen</a> ist dieser aktivierbar. Einmal aktiviert, k&ouml;nnt ihr f&uuml;r ".MIN_UMOD_TIME." Tage nicht Spielen! Nach den abgelaufenen ".MIN_UMOD_TIME." Tagen habt ihr die Option den U-Mod zu deaktivieren und somit am Spiel wieder teilzunehmen. Tut ihr das nicht, ist der Account so lange im U-Mod bis ihr ihn deaktiviert.<br>
        Wenn ihr euch im Urlaubsmodus befindet, wird die Produktion still gelegt und ihr bekommt keine Ressourcen mehr.
        Ausserdem kann man den U-Mod erst aktivieren, wenn nichts mehr Gebaut wird, trefft daher eure Vorkehrungen fr&uuml;hzeitig!<br>
        User die sich im U-Mod befinden k&ouml;nnen nicht von anderen Spielern angegriffen werden!";
    echo "</div>";
    infobox_end();
?>
