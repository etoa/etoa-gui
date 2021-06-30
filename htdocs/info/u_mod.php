<?php

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

    echo "<h2>Urlaubsmodus</h2>";
		HelpUtil::breadCrumbs(array("Urlaubsmodus","u_mod"));
    iBoxStart("Urlaubsmodus");
    echo "<div align=\"justify\">";
    echo "Wenn ein User seinen Account ".$config->param1Int('user_inactive_days')." Tage nicht benutzt, wird er vom System gel&ouml;scht! Ist man aus irgendeinem Grund verhindert zu spielen, gibt es die M&ouml;glichkeit in den Urlaubsmodus zu treten. Unter <a href=\"?page=userconfig\">Einstellungen</a> ist dieser aktivierbar. Einmal aktiviert, k&ouml;nnt ihr f&uuml;r ".MIN_UMOD_TIME." Tage nicht Spielen! Nach den abgelaufenen ".MIN_UMOD_TIME." Tagen habt ihr die Option den U-Mod zu deaktivieren und somit am Spiel wieder teilzunehmen.
        Nach ".MAX_UMOD_TIME." Tagen Urlaubsmodus wird der Account inaktiv und kann von anderen Spielern angegriffen werden..<br>
        Wenn ihr euch im Urlaubsmodus befindet, wird die Produktion still gelegt und ihr bekommt keine Ressourcen mehr.
        Ausserdem kann man den U-Mod erst aktivieren, wenn keine Flotten zu euch fliegen.<br>
        User die sich im U-Mod befinden k&ouml;nnen nicht von anderen Spielern angegriffen werden!";
    echo "</div>";
    iBoxEnd();
?>
