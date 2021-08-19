<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceSearch;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;

if (isset($_POST['search_query']) && $_POST['search_query'] != "") {
    $search = $_POST['search_query'];
    echo "<h1>Suche nach <i>" . $search . "</i></h1>";

    // Users
    /** @var UserRepository $userRepository */
    $userRepository = $app[UserRepository::class];
    $userNicks = $userRepository->searchUserNicknames(UserSearch::create()->nickOrEmailOrDualLike($search), 30);
    if (count($userNicks) > 0) {
        echo "<h2>Spieler</h2><ul>";
        foreach ($userNicks as $userId => $userNick) {
            echo "<li><a href=\"?page=user&amp;sub=edit&amp;id=" . $userId . "\">" . $userNick . "</a></li>";
        }
        echo "</ul>";
    }

    // Alliances
    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];
    $alliances = $allianceRepository->getAllianceNamesWithTags(AllianceSearch::create()->nameOrTagLike($search));
    if (count($alliances) > 0) {
        echo "<h2>Allianzen</h2><ul>";
        foreach ($alliances as $allianceId => $allianceNameWithTag) {
            echo "<li><a href=\"?page=alliances&amp;sub=edit&amp;alliance_id=" . $allianceId . "\">" . $allianceNameWithTag . "</a></li>";
        }
        echo "</ul>";
    }

    // Planets
    /** @var EntityRepository $entityRepository */
    $entityRepository = $app[EntityRepository::class];
    $planets = $entityRepository->searchEntityLabels(EntityLabelSearch::create()->likePlanetName($search), 30);
    if (count($planets) > 0) {
        echo "<h2>Planeten</h2><ul>";
        foreach ($planets as $planet) {
            echo "<li><a href=\"?page=galaxy&sub=edit&id=" . $planet->id . "\">" . $planet->toString() . "</a></li>";
        }
        echo "</ul>";
    }
} else {
    echo "<h1>Suche</h1>";
    echo error_msg("Kein Suchbegriff eingegeben!");
}
