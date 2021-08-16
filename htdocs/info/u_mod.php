<?php

use EtoA\Core\Configuration\ConfigurationService;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

    echo "<h2>Urlaubsmodus</h2>";
		HelpUtil::breadCrumbs(array("Urlaubsmodus","u_mod"));
    iBoxStart("Urlaubsmodus");
    echo "<div align=\"justify\">";
    echo "Wenn ein User seinen Account ".$config->param1Int('user_inactive_days')." Tage nicht benutzt, wird er vom System gel&ouml;scht! Ist man aus irgendeinem Grund verhindert zu spielen, gibt es die M&ouml;glichkeit in den Urlaubsmodus zu treten. Unter <a href=\"?page=userconfig\">Einstellungen</a> ist dieser aktivierbar. Einmal aktiviert, k&ouml;nnt ihr f&uuml;r ".$config->getInt('hmode_days')." Tage nicht Spielen! Nach den abgelaufenen ".$config->getInt('hmode_days')." Tagen habt ihr die Option den U-Mod zu deaktivieren und somit am Spiel wieder teilzunehmen.
        Nach ".$config->param1Int('hmode_days')." Tagen Urlaubsmodus wird der Account inaktiv und kann von anderen Spielern angegriffen werden..<br>
        Wenn ihr euch im Urlaubsmodus befindet, wird die Produktion still gelegt und ihr bekommt keine Ressourcen mehr.
        Ausserdem kann man den U-Mod erst aktivieren, wenn keine Flotten zu euch fliegen.<br>
        User die sich im U-Mod befinden k&ouml;nnen nicht von anderen Spielern angegriffen werden!";
    echo "</div>";
    iBoxEnd();
?>
