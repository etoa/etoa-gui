<?PHP

use EtoA\Alliance\Board\AllianceBoardCategoryRankRepository;
use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardPostRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use EtoA\Alliance\Board\Category;
use EtoA\Alliance\Board\Topic;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\User\UserRepository;

/** @var AllianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];
/** @var AllianceBoardCategoryRepository $allianceBoardCategoryRepository */
$allianceBoardCategoryRepository = $app[AllianceBoardCategoryRepository::class];
/** @var AllianceBoardTopicRepository $allianceBoardTopicRepository */
$allianceBoardTopicRepository = $app[AllianceBoardTopicRepository::class];
/** @var AllianceBoardPostRepository $allianceBoardPostRepository */
$allianceBoardPostRepository = $app[AllianceBoardPostRepository::class];
/** @var AllianceBoardCategoryRankRepository $allianceBoardCategoryRankRepository */
$allianceBoardCategoryRankRepository = $app[AllianceBoardCategoryRankRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

echo "<h1>Allianzforum</h1>";

// Prüfen ob User in Allianz ist
if ($cu->allianceId > 0) {
    // Prüfen ob Allianz existiert
    $alliance = $allianceRepository->getAlliance((int) $cu->allianceId);
    $allianceNames = $allianceRepository->getAllianceNames();
    if ($alliance !== null) {
        //Get Variablen überprüfen und IDs zuordnen
        $legal = true;
        $bnd_id = null;
        $alliance_bnd_id = null;
        if (isset($_GET['bnd']) && intval($_GET['bnd']) > 0) {
            $bid = intval($_GET['bnd']);

            $bres = dbquery("SELECT * FROM alliance_bnd WHERE (alliance_bnd_alliance_id1=" . $alliance->id . " || alliance_bnd_alliance_id2=" . $alliance->id . ") AND alliance_bnd_id=" . $bid . " AND alliance_bnd_level=2;");
            if (mysql_num_rows($bres) > 0) {
                $barr = mysql_fetch_array($bres);
                $bnd_id = $barr['alliance_bnd_id'];
                if ($barr['alliance_bnd_alliance_id2'] == $alliance->id) {
                    $alliance_bnd_id = $barr['alliance_bnd_alliance_id1'];
                } else {
                    $alliance_bnd_id = $barr['alliance_bnd_alliance_id2'];
                }

                $_GET['cat'] = 0;
            } else {
                $legal = false;
            }
        } else {
            $bid = 0;
        }

        // Eigenen Rang laden
        $user = $userRepository->getUser($cu->getId());
        if ($user !== null && $user->allianceId === $alliance->id) {
            $myRankId = $user->allianceRankId;
        } else
            $myRankId = 0;

        // Rechte laden
        $rightres = dbquery("SELECT * FROM alliance_rights ORDER BY right_desc;");
        $rights = array();
        $myRight = [];
        $rightIds = $allianceRankRepository->getAvailableRightIds($alliance->id, $myRankId);
        if (mysql_num_rows($rightres) > 0) {
            while ($rightarr = mysql_fetch_array($rightres)) {
                $rights[$rightarr['right_id']]['key'] = $rightarr['right_key'];
                $rights[$rightarr['right_id']]['desc'] = $rightarr['right_desc'];
                $myRight[$rightarr['right_key']] = in_array((int) $rightarr['right_id'], $rightIds , true);
            }
        }

        // Ränge laden
        $ranks = $allianceRankRepository->getRanks($alliance->id);
        $rank = array();
        foreach ($ranks as $r) {
            $rank[$r->id] = $r->name;
        }

        // Kategorien laden
        $myCat = [];
        $allianceCategories = $allianceBoardCategoryRepository->getCategories($alliance->id);
        $availableCategories = $allianceBoardCategoryRankRepository->getCategoriesForRank($alliance->id, $myRankId);
        $allianceCategoryMap = [];
        if (count($allianceCategories) > 0) {
            foreach ($allianceCategories as $category) {
                $allianceCategoryMap[$category->id] = $category;
                $myCat[$category->id] = in_array($category->id, $availableCategories, true);
            }
        }

        // Gründer prüfen
        if ($alliance->founderId == $cu->id)
            $isFounder = true;
        else
            $isFounder = false;

        // Allianz-User in Array laden
        $allianceUsers = $userRepository->getAllianceUsers($alliance->id);

        // Change avatar function
        echo "<script type=\"text/javascript\">";
        echo "function changeAvatar(elem) { document.getElementById('avatar').src='" . BOARD_AVATAR_DIR . "/'+elem.options[elem.selectedIndex].value;}";
        echo "function changeBullet(elem) { document.getElementById('bullet').src='" . BOARD_BULLET_DIR . "/'+elem.options[elem.selectedIndex].value;}";
        echo "</script>";


        // Board-Admin prüfen
        if (Alliance::checkActionRights('allianceboard', FALSE) || $isFounder)
            $isAdmin = true;
        else
            $isAdmin = false;



        //
        // Create new post in topic
        //
        if (isset($_GET['newpost']) && intval($_GET['newpost']) > 0 && $cu->id > 0 && $legal) {
            $npid = intval($_GET['newpost']);

            if (isset($alliance_bnd_id)) {
                $topic = $allianceBoardTopicRepository->getTopic($npid, $alliance_bnd_id);
            } else {
                $topic = $allianceBoardTopicRepository->getTopic($npid);
            }

            if ($topic !== null) {
                if (!$topic->closed) {
                    echo "<form action=\"?page=$page&amp;topic=" . $npid . "&bnd=" . $bid . "\" method=\"post\">";
                    if (isset($alliance_bnd_id)) {
                        echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;bnd=" . $topic->bndId . "\">" . $allianceNames[$alliance_bnd_id] . "</a> &gt; <a href=\"?page=$page&amp;topic=" . $npid . "\">" . $topic->subject . "</a> &gt; Neuer Beitrag</h2>";
                    } else {
                        echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=" . $topic->categoryId . "\">" . $allianceCategoryMap[$topic->id]->name . "</a> &gt; <a href=\"?page=$page&amp;topic=" . $npid . "\">" . $topic->subject . "</a> &gt; Neuer Beitrag</h2>";
                    }
                    tableStart();
                    echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"10\" cols=\"90\"></textarea></td></tr>";
                    tableEnd();
                    echo "<input type=\"submit\" name=\"submit\" value=\"Speichern\" /> &nbsp; ";
                } else
                    error_msg("Dieses Thema ist geschlossen!", 1);
            } else
                error_msg("Dieses Thema existiert nicht!");
            echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Beitrags abgebrochen werden?')) document.location='?page=$page&bnd=" . $bid . "&topic=" . $npid . "'\" /></form>";
        }

        //
        // Edit Post
        //
        elseif (isset($_GET['editpost']) && intval($_GET['editpost']) > 0 && $s) {
            $editPostId = intval($_GET['editpost']);

            echo "<h2>Beitrag bearbeiten</h2>";
            $post = $allianceBoardPostRepository->getPost($editPostId);
            if ($post !== null) {
                if ($cu->id == $post->userId || $isAdmin) {
                    echo "<form action=\"?page=$page&amp;bnd=" . $bid . "&topic=" . $post->topicId . "\" method=\"post\">";
                    echo "<input type=\"hidden\" name=\"post_id\" value=\"" . $post->id . "\" />";
                    tableStart();
                    echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"10\" cols=\"90\">" . stripslashes($post->text) . "</textarea></td></tr>";
                    tableEnd();
                    echo "<input type=\"submit\" value=\"Speichern\" name=\"post_edit\" /> &nbsp; ";
                } else
                    error_msg("Keine Berechtigung!");
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&bnd=" . $bid . "&topic=" . $post->topicId . "#" . $editPostId . "'\" /></form>";
        }

        //
        // Delete Post
        //
        elseif (isset($_GET['delpost']) && intval($_GET['delpost']) > 0 && $s) {
            $deletePostId = intval($_GET['delpost']);

            echo "<h2>Beitrag löschen</h2>";
            $post = $allianceBoardPostRepository->getPost($deletePostId);
            if ($post !== null) {
                if ($cu->id == $post->userId || $isAdmin) {
                    echo "<form action=\"?page=$page&amp;bnd=" . $bid . "&topic=" . $post->topicId . "\" method=\"post\">";
                    echo "<input type=\"hidden\" name=\"post_id\" value=\"" . $post->id . "\" />";
                    iBoxStart("Soll der folgende Beitrag wirklich gelöscht werden?");
                    echo text2html($post->text);
                    iBoxEnd();
                    echo "<input type=\"submit\" value=\"L&ouml;schen\" name=\"post_delete\" onclick=\"return confirm('Wirklich löschen?');\" /> &nbsp; ";
                } else
                    error_msg("Keine Berechtigung!");
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&bnd=" . $bid . "&topic=" . $post->topicId . "#" . $deletePostId . "' \" /></form>";
        }

        //
        // Show topic with its posts
        //
        elseif (isset($_GET['topic']) && intval($_GET['topic']) > 0 && $legal) {
            $tpid = intval($_GET['topic']);

            $topic = $allianceBoardTopicRepository->getTopic($tpid);
            if ($topic !== null) {
                if (($bnd_id === $topic->bndId && $isAdmin) || (isset($myCat[$topic->categoryId]) && ($isAdmin || $myCat[$topic->categoryId]))) {
                    if ($topic->bndId > 0) {
                        echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;bnd=" . $topic->bndId . "\">" . $allianceNames[$alliance_bnd_id] . "</a> &gt; " . $topic->subject . "</h2>";
                    } else {
                        echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=" . $topic->categoryId . "\">" . $allianceCategoryMap[$topic->categoryId]->name . "</a> &gt; " . $topic->subject . "</h2>";
                    }
                    if ($topic->closed) {
                        echo "<img src=\"images/closed.gif\" alt=\"closed\" style=\"width:15px;height:16px;\" /> <i>Dieses Thema ist geschlossen und es können keine weiteren Beiträge erstellt werden!</i><br/><br/>";
                    }

                    // Save new post
                    if (isset($_POST['submit']) && isset($_POST['post_text']) && $cu->id > 0 && !$topic->closed) {
                        $mid = $allianceBoardPostRepository->addPost($tpid, $_POST['post_text'], $cu->getId(), $cu->getNick());
                        $allianceBoardTopicRepository->updateTopicTimestamp($tpid);
                        success_msg("Beitrag gespeichert!");
                        echo "<script type=\"text/javascript\">document.location='?page=$page&bnd=" . $bid . "&topic=" . $topic->id . "#" . $mid . "';</script>";
                    } else
                        $allianceBoardTopicRepository->increaseTopicCount($tpid);

                    // Edit post
                    if (isset($_POST['post_edit']) && isset($_POST['post_text']) && isset($_POST['post_id']) && ($cu->id > 0 || $isAdmin)) {
                        if ($isAdmin)
                            $allianceBoardPostRepository->updatePost((int) $_POST['post_id'], $_POST['post_text']);
                        else
                            $allianceBoardPostRepository->updatePost((int) $_POST['post_id'], $_POST['post_text'], $cu->getId());
                        success_msg("&Auml;nderungen gespeichert!");
                        echo "<script type=\"text/javascript\">document.location='?page=$page&bnd=" . $bid . "&topic=" . $topic->id . "#" . $_POST['post_id'] . "';</script>";
                    }

                    // Delete post
                    if (isset($_POST['post_delete']) && isset($_POST['post_id']) && ($cu->id > 0 || $isAdmin)) {
                        if ($isAdmin)
                            $allianceBoardPostRepository->deletePost((int) $_POST['post_id']);
                        else
                            $allianceBoardPostRepository->deletePost((int) $_POST['post_id'], $cu->getId());

                        success_msg("Beitrag gelöscht");
                    }

                    $posts = $allianceBoardPostRepository->getPosts($tpid);
                    if (count($posts) > 0) {
                        tableStart($topic->subject);
                        foreach ($posts as $post) {
                            echo "<tr><th style=\"width:150px;\"><a name=\"" . $post->id . "\"></a><a href=\"?page=userinfo&amp;id=" . $post->userId . "\">" . $post->userNick . "</a><br/>";
                            show_avatar($allianceUsers[$post->userId]->avatar);
                            $cpost = $allianceBoardPostRepository->getUserAlliancePostCounts($alliance->id, $cu->getId());
                            echo "Beitr&auml;ge: " . $cpost . "<br/><br/>" . df($post->timestamp) . " Uhr";
                            if ($isAdmin || $post->userId == $cu->id)
                                echo "<br/><a href=\"?page=$page&amp;bnd=" . $bid . "&editpost=" . $post->id . "\"><img src=\"images/edit.gif\" alt=\"edit\" style=\"border:none\" /></a> <a href=\"?page=$page&amp;bnd=" . $bid . "&delpost=" . $post->id . "\"><img src=\"images/delete.gif\" alt=\"del\" style=\"border:none;\" /></a>";
                            echo "</th>";
                            echo "<td";
                            if (isset($urank) && $allianceUsers[$post->userId]->rank == count($urank) - 1)
                                echo " style=\"color:" . ADMIN_COLOR . "\"";

                            echo ">" . text2html($post->text);
                            if ($post->changed !== null)
                                echo "<br/><br/><span style=\"font-size:8pt;\">Dieser Beitrag wurde zuletzt geändert am " . date("d.m.Y", $post->changed) . " um " . date("H:i", $post->changed) . " Uhr.</span>";
                            if ($allianceUsers[$post->userId]->signature != "")
                                echo "<hr>" . text2html($allianceUsers[$post->userId]->signature);
                            echo "</td></tr>";
                        }
                        tableEnd();
                    } else {
                        $topic = $allianceBoardTopicRepository->getTopic($tpid);
                        if ($topic !== null) {
                            $allianceBoardTopicRepository->deleteTopic($tpid);
                            echo "<script>document.location='?page=$page&cat=" . $topic->categoryId . "';</script>
                                    Klicke <a href=\"?page=$page&cat=" . $topic->categoryId . "\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
                        } else {
                            echo "<script>document.location='?page=$page';</script>
                                    Klicke <a href=\"?page=$page\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
                        }
                    }
                    if ($cu->id > 0 && !$topic->closed)
                        echo "<input type=\"button\" value=\"Neuer Beitrag\" onclick=\"document.location='?page=$page&amp;bnd=" . $bid . "&newpost=" . $tpid . "'\" /> &nbsp; ";
                } else
                    error_msg("Kein Zugriff!");
            } else
                error_msg("Dieses Thema existiert nicht!");
            if ($topic->bndId > 0) {
                echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;bnd=" . $topic->bndId . "'\" />";
            } elseif ($topic !== null) {
                echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page&amp;cat=" . $topic->categoryId . "'\" />";
            }
        }

        //
        // Create new topic in category
        //
        elseif (isset($_GET['newtopic']) && intval($_GET['newtopic']) > 0 && $cu->id > 0 && $legal) {
            $ntid = intval($_GET['newtopic']);

            if ($bid > 0) {
                echo "<form action=\"?page=$page&amp;bnd=" . $bid . "\" method=\"post\">";
                echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;bnd=" . $bid . "\">" . $allianceNames[$alliance_bnd_id] . "</a> &gt; Neues Thema</h2>";
            } else {
                $category = $allianceBoardCategoryRepository->getCategory($ntid, $alliance->id);
                if ($category !== null) {
                    echo "<form action=\"?page=$page&amp;cat=" . $ntid . "\" method=\"post\">";
                    echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; <a href=\"?page=$page&amp;cat=" . $category->id . "\">" . $category->name . "</a> &gt; Neues Thema</h2>";
                } else
                    $legal = false;
            }
            if ($legal) {
                tableStart();
                echo "<tr><th>Titel:</th><td><input name=\"topic_subject\" type=\"text\" size=\"40\" /></td></tr>";
                echo "<tr><th>Text:</th><td><textarea name=\"post_text\" rows=\"6\" cols=\"80\"></textarea></td></tr>";
                tableEnd();
                echo "<input type=\"submit\" name=\"submit\" value=\"Speichern\" /> &nbsp; ";
            } else
                error_msg("Diese Kategorie existiert nicht!");
            if ($bid == 0) {
                echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Themas abgebrochen werden?')) document.location='?page=$page&amp;cat=" . $ntid . "'\" /></form>";
            } else {
                echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"if (confirm('Soll die Erstellung des Themas abgebrochen werden?')) document.location='?page=$page&amp;bnd=" . $bid . "'\" /></form>";
            }
        }


        //
        // Edit a topic
        //
        elseif (isset($_GET['edittopic']) && intval($_GET['edittopic']) > 0 && $s  && $legal) {
            $etid = intval($_GET['edittopic']);

            echo "<h2>Thema bearbeiten</h2>";
            $topic = $allianceBoardTopicRepository->getTopic($etid, $bid);
            if ($topic !== null) {
                if ($cu->id == $topic->userId || $isAdmin) {
                    echo "<form action=\"?page=$page&amp;bnd=" . $bid . "&cat=" . $topic->categoryId . "\" method=\"post\">";
                    echo "<input type=\"hidden\" name=\"topic_id\" value=\"" . $topic->id . "\" />";
                    echo "<input type=\"hidden\" name=\"topic_bnd_id\" value=\"" . $topic->bndId . "\" />";
                    tableStart();
                    echo "<tr><th>Titel:</th><td><input type=\"text\" name=\"topic_subject\" size=\"40\" value=\"" . $topic->subject . "\" /></td></tr>";
                    if ($isAdmin) {
                        echo "<tr><th>Top-Thema:</th><td><input name=\"topic_top\" type=\"radio\" value=\"1\"";
                        if ($topic->top) echo " checked=\"checked\"";
                        echo " /> Ja <input name=\"topic_top\" type=\"radio\" value=\"0\"";
                        if (!$topic->top) echo " checked=\"checked\"";
                        echo " /> Nein</td></tr>";
                        echo "<tr><th>Geschlossen:</th><td><input name=\"topic_closed\" type=\"radio\" value=\"1\"";
                        if ($topic->closed) echo " checked=\"checked\"";
                        echo " /> Ja <input name=\"topic_closed\" type=\"radio\" value=\"0\"";
                        if (!$topic->closed) echo " checked=\"checked\"";
                        echo " /> Nein</td></tr>";
                        if ($bid != 0) {
                            echo "<tr><th>Kategorie:</th><td>" . $allianceNames[$alliance_bnd_id] . "</td></tr>";
                        } else {
                            echo "<tr><th>Kategorie:</th><td><select name=\"topic_cat_id\">";
                            $categories = $allianceBoardCategoryRepository->getCategories($alliance->id);
                            foreach ($categories as $category) {
                                echo "<option value=\"" . $category->id . "\"";
                                if ($topic->categoryId === $category->id) echo " selected=\"selected\"";
                                echo ">" . $category->name . "</option>";
                            }
                            echo "</select></td></tr>";
                        }
                    }
                    tableEnd();
                    echo "<input type=\"submit\" name=\"topic_edit\" value=\"Speichern\" /> ";
                } else
                    error_msg("Keine Berechtigung!");
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&amp;bnd=" . $bid . "'\" /></form>";
        }

        //
        // Delete a topic and all it's posts
        //
        elseif (isset($_GET['deltopic']) && intval($_GET['deltopic']) > 0 && $isAdmin) {
            $dtid = intval($_GET['deltopic']);

            echo "<h2>Thema löschen</h2>";
            $topic = $allianceBoardTopicRepository->getTopic($dtid);
            if ($topic !== null) {
                echo "<form action=\"?page=$page&amp;bnd=" . $topic->bndId . "&amp;cat=" . $topic->categoryId . "\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"topic_id\" value=\"" . $topic->id . "\" />";
                echo "Soll der Beitrag <b>" . $topic->subject . "</b> und alle darin enthaltenen Posts gelöscht werden?";
                echo "<br/><br/><input type=\"submit\" name=\"topic_delete\" value=\"L&ouml;schen\" onclick=\"return confirm('Willst du das Thema \'" . $topic->subject . "\' wirklich löschen?');\" /> ";
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page&amp;bnd=" . $bid . "'\" /></form>";
        }

        //
        // Show topics in category
        //
        elseif (isset($_GET['cat']) && intval($_GET['cat']) > 0) {
            $cat = intval($_GET['cat']);

            if ($isAdmin || isset($myCat[$cat])) {
                $category = $allianceBoardCategoryRepository->getCategory($cat, $alliance->id);
                if ($category !== null) {
                    echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; " . ($category->name != "" ? stripslashes($category->name) : "Unbenannt") . "</h2>";

                    // Save new topic
                    if (isset($_POST['submit']) && isset($_POST['topic_subject']) && isset($_POST['post_text']) && $cu->id > 0) {
                        $mid = $allianceBoardTopicRepository->addTopic($_POST['topic_subject'], 0, $category->id, $cu->getId(), $cu->getNick());
                        $pmid = $allianceBoardPostRepository->addPost($mid, $_POST['post_text'], $cu->getId(), $cu->getNick());
                        echo "<script type=\"text/javascript\">document.location='?page=$page&topic=" . $mid . "#" . $pmid . "';</script>";
                    }
                    // Save edited topic
                    elseif (isset($_POST['topic_edit']) && isset($_POST['topic_subject']) && isset($_POST['topic_id']) && $_POST['topic_id'] > 0) {
                        $allianceBoardTopicRepository->updateTopic((int) $_POST['topic_id'], $_POST['topic_subject'], (int) $_POST['topic_bnd_id'], (int) $_POST['topic_cat_id'], (bool) $_POST['topic_top'], (bool) $_POST['topic_closed']);
                        echo "&Auml;nderungen gespeichert!<br/><br/>";
                        if ($_POST['topic_cat_id'] != $category->id)
                            echo "<script type=\"text/javascript\">document.location='?page=$page&amp;cat=" . $_POST['topic_cat_id'] . "';</script>";
                    }
                    // Delete topic
                    elseif (isset($_POST['topic_delete']) && isset($_POST['topic_id']) && $_POST['topic_id'] > 0) {
                        $allianceBoardTopicRepository->deleteTopic((int) $_POST['topic_id']);
                        success_msg("Thema gelöscht!");
                    }

                    $topics = $allianceBoardTopicRepository->getTopics($category->id);
                    if (count($topics) > 0) {
                        $topicIds = array_map(fn (Topic $topic) => $topic->id, $topics);
                        $postCounts = $allianceBoardTopicRepository->getTopicPostCounts($topicIds);
                        tableStart();
                        echo "<tr><th colspan=\"2\">Thema</th><th>Posts</th><th>Aufrufe</th><th>Autor</th><th>Letzer Beitrag</th>";
                        if ($isAdmin) {
                            echo "<th>Aktionen</th>";
                        }
                        echo "</tr>";
                        foreach ($topics as $topic) {
                            echo "<tr><td style=\"width:37px;\">";
                            if ($topic->top) echo "<img src=\"images/sticky.gif\" alt=\"top\" style=\"width:22px;height:15px;\" " . tm("Wichtiges Thema", "Dieses ist ein wichtiges Thema.") . "/>";
                            if ($topic->closed) echo "<img src=\"images/closed.gif\" alt=\"closed\" style=\"width:15px;height:16px;\" " . tm("Geschlossen", "Es können keine weiteren Beiträge zu diesem Thema geschrieben werden.") . " />";
                            echo "</td>";
                            echo "<td style=\"width:250px;\"><a href=\"?page=$page&amp;topic=" . $topic->id . "\"";

                            echo ">" . $topic->subject . "</a></td>";
                            echo "<td>" . $postCounts[$topic->id] . "</td>";
                            echo "<td>" . $topic->count . "</td>";
                            echo "<td>" . $allianceUsers[$topic->userId]->nick . "</td>";
                            $post = $allianceBoardPostRepository->getPosts($topic->id, 1)[0];
                            echo "<td><a href=\"?page=$page&amp;topic=" . $topic->id . "#" . $post->id . "\">" . df($post->timestamp) . "</a><br/>" . $post->userNick . "</td>";
                            if ($isAdmin || $cu->id == $topic->userId) {
                                echo "<td style=\"vertical-align:middle;text-align:center;\">
                                    <a href=\"?page=$page&edittopic=" . $topic->id . "\" title=\"Thema bearbeiten\">" . icon('edit') . "</a>";
                                if ($isAdmin)
                                    echo " <a href=\"?page=$page&deltopic=" . $topic->id . "\" title=\"Thema löschen \">" . icon('delete') . "</a>";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        tableEnd();
                    } else
                        error_msg("Es sind noch keine Themen vorhanden!");
                    if ($cu->id > 0)
                        echo "<input type=\"button\" value=\"Neues Thema\" onclick=\"document.location='?page=$page&newtopic=" . $cat . "'\" /> &nbsp; ";
                } else
                    error_msg("Kategorie existiert nicht!");
            } else
                error_msg("Kein Zugriff!3");
            echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
        }

        //
        // Show bnd topics in category
        //
        elseif ($bid > 0) {
            $cat = intval($_GET['cat']);

            if ($isAdmin || isset($myCat[$cat])) {
                if ($legal) {
                    echo "<h2><a href=\"?page=$page\">&Uuml;bersicht</a> &gt; " . $allianceNames[$alliance_bnd_id] . "</h2>";

                    // Save new topic
                    if (isset($_POST['submit']) && isset($_POST['topic_subject']) && isset($_POST['post_text']) && $cu->id > 0) {
                        $mid = $allianceBoardTopicRepository->addTopic($_POST['topic_subject'], $bid, 0, $cu->getId(), $cu->getNick());
                        $pmid = $allianceBoardPostRepository->addPost($mid, $_POST['post_text'], $cu->getId(), $cu->getNick());
                        echo "<script type=\"text/javascript\">document.location='?page=$page&bnd=" . $bid . "&topic=" . $mid . "#" . $pmid . "';</script>";
                    }
                    // Save edited topic
                    elseif (isset($_POST['topic_edit']) && isset($_POST['topic_subject']) && isset($_POST['topic_id']) && $_POST['topic_id'] > 0) {
                        $allianceBoardTopicRepository->updateTopic((int) $_POST['topic_id'], $_POST['topic_subject'], (int) $_POST['topic_bnd_id'], $cat, (bool) $_POST['topic_top'], (bool) $_POST['topic_closed']);
                        success_msg("&Auml;nderungen gespeichert!");
                        if ($_POST['topic_bnd_id'] != $bid)
                            echo "<script type=\"text/javascript\">document.location='?page=$page&amp;bnd=" . $_POST['topic_bnd_id'] . "';</script>";
                    }
                    // Delete topic
                    elseif (isset($_POST['topic_delete']) && isset($_POST['topic_id']) && $_POST['topic_id'] > 0) {
                        $allianceBoardTopicRepository->deleteTopic((int) $_POST['topic_id']);
                        success_msg("Thema gelöscht!");
                    }


                    $topics = $allianceBoardTopicRepository->getBndTopics($bid);
                    if (count($topics) > 0) {
                        $topicIds = array_map(fn (Topic $topic) => $topic->id, $topics);
                        $postCounts = $allianceBoardTopicRepository->getTopicPostCounts($topicIds);
                        tableStart();
                        echo "<tr><th colspan=\"2\">Thema</th><th>Posts</th><th>Aufrufe</th><th>Autor</th><th>Letzer Beitrag</th>";
                        if ($isAdmin) {
                            echo "<th>Aktionen</th>";
                        }
                        echo "</tr>";
                        foreach ($topics as $topic) {
                            echo "<tr><td style=\"width:37px;\">";
                            if ($topic->top) echo "<img src=\"images/sticky.gif\" alt=\"top\" style=\"width:22px;height:15px;\" " . tm("Wichtiges Thema", "Dieses ist ein wichtiges Thema.") . "/>";
                            if ($topic->closed) echo "<img src=\"images/closed.gif\" alt=\"closed\" style=\"width:15px;height:16px;\" " . tm("Geschlossen", "Es können keine weiteren Beiträge zu diesem Thema geschrieben werden.") . " />";
                            echo "</td>";
                            echo "<td style=\"width:250px;\"><a href=\"?page=$page&amp;bnd=" . $bid . "&topic=" . $topic->id . "\"";
                            echo ">" . $topic->subject . "</a></td>";
                            echo "<td>" . $postCounts[$topic->id] . "</td>";
                            echo "<td>" . $topic->count . "</td>";
                            echo "<td>" . $allianceUsers[$topic->userId]->nick . "</td>";
                            $post = $allianceBoardPostRepository->getPosts($topic->id, 1)[0];
                            echo "<td><a href=\"?page=$page&amp;topic=" . $topic->id . "#" . $post->id . "\">" . df($post->timestamp) . "</a><br/>" . $post->userNick . "</td>";
                            if ($isAdmin || $cu->id == $topic->userId) {
                                echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&bnd=" . $bid . "&edittopic=" . $topic->id . "'\" />";
                                if ($isAdmin)
                                    echo " <input type=\"button\" value=\"L&ouml;schen\" onclick=\"document.location='?page=$page&deltopic=" . $topic->id . "'\" />";
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        tableEnd();
                    } else
                        error_msg("Es sind noch keine Themen vorhanden!");
                    if ($cu->id > 0)
                        echo "<input type=\"button\" value=\"Neues Thema\" onclick=\"document.location='?page=$page&newtopic=" . $bid . "&bnd=" . $bid . "'\" /> &nbsp; ";
                } else
                    error_msg("Kategorie existiert nicht!");
            } else
                error_msg("Kein Zugriff!");
            echo "<input type=\"button\" value=\"Zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
        }


        //
        // New category
        //
        elseif (isset($_GET['action']) && $_GET['action'] == "newcat" && $isAdmin) {
            $d = opendir(BOARD_BULLET_DIR);
            $bullets = array();
            while ($f = readdir($d)) {
                if (is_file(BOARD_BULLET_DIR . "/" . $f) && !is_dir(BOARD_BULLET_DIR . "/" . $f) && $f != BOARD_DEFAULT_IMAGE) {
                    array_push($bullets, $f);
                }
            }
            sort($bullets);

            echo "<h2>Neue Kategorie</h2>";
            echo "<form action=\"?page=$page\" method=\"post\">";
            tableStart();
            echo "<tr><th>Name:</th><td><input type=\"text\" name=\"cat_name\" size=\"40\" /></td></tr>";
            echo "<tr><th>Beschreibung:</th><td><input type=\"text\" name=\"cat_desc\" size=\"40\" value=\"\" /></td></tr>";
            echo "<tr><th>Reihenfolge/Position:</th><td><input type=\"text\" size=\"1\" maxlenght=\"2\" name=\"cat_order\" value=\"" . count($allianceBoardCategoryRepository->getCategoryIds($alliance->id)) . "\" /></td></tr>";
            echo "<tr><th>Zugriff:</th><td>";
            foreach ($rank as $k => $v) {
                echo "<input type=\"checkbox\" name=\"cr[" . $k . "]\" value=\"1\" ";
                echo " /> " . $v . "</span><br/>";
            }
            echo "</td></tr>";
            echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";
            echo "<img src=\"" . BOARD_BULLET_DIR . "/" . BOARD_DEFAULT_IMAGE . "\" style=\"width:38px;height:35px;\" id=\"bullet\" />";
            echo "<br/>Symbol wählen: <select name=\"cat_bullet\" changeBullet=\"changeAvatar(this);\" onmousemove=\"changeBullet(this);\" onkeyup=\"changeBullet(this);\">";
            echo "<option value=\"" . BOARD_DEFAULT_IMAGE . "\">Standard-Symbol</option>";

            foreach ($bullets as $a) {
                echo "<option value=\"$a\"";
                echo ">$a</option>";
            }
            echo "</select></td></tr>";
            tableEnd();
            echo "<input type=\"submit\"name=\"cat_new\" value=\"Kategorie speichern\" /> ";
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
        }

        //
        // Edit a category
        //
        elseif (isset($_GET['editcat']) && intval($_GET['editcat']) > 0 && $isAdmin) {
            $ecid = intval($_GET['editcat']);

            echo "<h2>Kategorie bearbeiten</h2>";
            $category = $allianceBoardCategoryRepository->getCategory($ecid, $alliance->id);
            if ($category !== null) {
                $d = opendir(BOARD_BULLET_DIR);
                $bullets = array();
                while ($f = readdir($d)) {
                    if (is_file(BOARD_BULLET_DIR . "/" . $f) && !is_dir(BOARD_BULLET_DIR . "/" . $f) && $f != BOARD_DEFAULT_IMAGE) {
                        array_push($bullets, $f);
                    }
                }
                sort($bullets);

                echo "<form action=\"?page=$page\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"cat_id\" value=\"" . $category->id . "\" />";
                tableStart();
                echo "<tr><th>Name:</th><td><input type=\"text\" name=\"cat_name\" size=\"40\" value=\"" . $category->name . "\" /></td></tr>";
                echo "<tr><th>Beschreibung:</th><td><input type=\"text\" name=\"cat_desc\" size=\"40\" value=\"" . $category->description . "\" /></td></tr>";
                echo "<tr><th>Reihenfolge/Position:</th><td><input type=\"text\" size=\"1\" maxlenght=\"2\" name=\"cat_order\" value=\"" . $category->order . "\" /></td></tr>";
                echo "<tr><th>Zugriff:</th><td>";
                $categoryRankIds = $allianceBoardCategoryRankRepository->getRanksForCategories($category->id);
                foreach ($rank as $k => $v) {
                    echo "<input type=\"checkbox\" name=\"cr[" . $k . "]\" value=\"1\" ";
                    if (in_array($k, $categoryRankIds, true))
                        echo " checked=\"checked\" /><span style=\"color:#0f0;\">" . $v . "</span><br/>";
                    else
                        echo " /> <span style=\"color:#f50;\">" . $v . "</span><br/>";
                }
                echo "</td></tr>";
                echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";
                if ($category->bullet == "" || !is_file(BOARD_BULLET_DIR . "/" . $category->bullet)) $category->bullet = BOARD_DEFAULT_IMAGE;
                echo "<img src=\"" . BOARD_BULLET_DIR . "/" . $category->bullet . "\" style=\"width:38px;height:35px;\" id=\"bullet\" />";
                echo "<br/>Symbol ändern: <select name=\"cat_bullet\" onmousemove=\"changeBullet(this);\" onkeyup=\"changeBullet(this);\">";
                echo "<option value=\"" . BOARD_DEFAULT_IMAGE . "\">Standard-Symbol</option>";
                foreach ($bullets as $a) {
                    echo "<option value=\"$a\"";
                    if ($a == $category->bullet && $category->bullet != "") echo " selected=\"selected\"";
                    echo ">$a</option>";
                }
                echo "</select></td></tr>";

                tableEnd();
                echo "<input type=\"submit\" name=\"cat_edit\" value=\"Speichern\" /> ";
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
        }

        //
        //edit a bnd category
        elseif (isset($_GET['editbnd']) && intval($_GET['editbnd']) > 0 && $isAdmin) {
            $ebid = intval($_GET['editbnd']);

            echo "<h2>Kategorie bearbeiten</h2>";
            $res = dbquery("SELECT * FROM alliance_bnd WHERE (alliance_bnd_alliance_id1=" . $alliance->id . " || alliance_bnd_alliance_id2=" . $alliance->id . ") AND alliance_bnd_id=" . $ebid . ";");
            if (mysql_num_rows($res) > 0) {
                $arr = mysql_fetch_array($res);
                $d = opendir(BOARD_BULLET_DIR);
                $bullets = array();
                while ($f = readdir($d)) {
                    if (is_file(BOARD_BULLET_DIR . "/" . $f) && !is_dir(BOARD_BULLET_DIR . "/" . $f) && $f != BOARD_DEFAULT_IMAGE) {
                        array_push($bullets, $f);
                    }
                }
                sort($bullets);
                $alliance_bnd_id = 0;
                if ($arr['alliance_bnd_alliance_id2'] == $alliance->id) {
                    $alliance_bnd_id = $arr['alliance_bnd_alliance_id1'];
                } else {
                    $alliance_bnd_id = $arr['alliance_bnd_alliance_id2'];
                }

                echo "<form action=\"?page=$page\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"bnd_id\" value=\"" . $arr['alliance_bnd_id'] . "\" />";
                tableStart();
                echo "<tr><th>Name:</th><td>" . $allianceNames[$alliance_bnd_id] . "</td></tr>";
                echo "<tr><th>Beschreibung:</th><td>" . $arr['alliance_bnd_text'] . "</td></tr>";
                echo "<tr><th>Zugriff:</th><td>";

                $bndRankIds = $allianceBoardCategoryRankRepository->getRanksForBnd((int) $arr['alliance_bnd_id']);
                foreach ($rank as $k => $v) {
                    echo "<input type=\"checkbox\" name=\"cr[" . $k . "]\" value=\"1\" ";
                    if (in_array($k, $bndRankIds, true)) {
                        echo " checked=\"checked\" /><span style=\"color:#0f0;\">" . $v . "</span><br/>";
                    } else {
                        echo " /> <span style=\"color:#f50;\">" . $v . "</span><br/>";
                    }
                }

                echo "</td></tr>";
                /*echo "<tr><th style=\"width:110px;\">Symbol:</th><td>";
                    if ($arr['cat_bullet']=="" || !is_file(BOARD_BULLET_DIR."/".$arr['cat_bullet'])) $arr['cat_bullet']=BOARD_DEFAULT_IMAGE;
                    echo "<img src=\"".BOARD_BULLET_DIR."/".$arr['cat_bullet']."\" style=\"width:38px;height:35px;\" id=\"bullet\" />";
                    echo "<br/>Symbol ändern: <select name=\"cat_bullet\" changeBullet=\"changeAvatar(this);\" onmousemove=\"changeBullet(this);\" onkeyup=\"changeBullet(this);\">";
                    echo "<option value=\"".BOARD_DEFAULT_IMAGE."\">Standard-Symbol</option>";
                    foreach ($bullets as $a)
                    {
                            echo "<option value=\"$a\"";
                            if ($a==$arr['cat_bullet'] && $arr['cat_bullet']!="") echo " selected=\"selected\"";
                            echo ">$a</option>";
                    }
                    echo "</select></td></tr>";*/

                tableEnd();
                echo "<input type=\"submit\" name=\"cat_edit\" value=\"Speichern\" /> ";
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
        }


        //
        // Delete a forum category and all it's content
        //
        elseif (isset($_GET['delcat']) && intval($_GET['delcat']) > 0 && $isAdmin) {
            $dcid = intval($_GET['delcat']);

            echo "<h2>Kategorie löschen</h2>";
            $category = $allianceBoardCategoryRepository->getCategory($dcid, $alliance->id);
            if ($category !== null) {
                echo "<form action=\"?page=$page\" method=\"post\">";
                echo "<input type=\"hidden\" name=\"cat_id\" value=\"" . $category->id . "\" />";
                echo "Soll die Kategorie <b>" . $category->name . "</b> und alle darin enthaltenen Topics und Posts gelöscht werden?";
                echo "<br/><br/><input type=\"submit\" value=\"Löschen\" name=\"cat_delete\" value=\"save_edit\" onclick=\"return confirm('Willst du die Kategorie \'" . $category->name . "\' wirklich löschen?');\" /> ";
            } else
                error_msg("Datensatz nicht gefunden!");
            echo "<input type=\"button\" value=\"Abbrechen\" onclick=\"document.location='?page=$page'\" /></form>";
        }

        //
        // Show forum categories; this ist the default view
        //
        else {
            echo "<h2>Übersicht</h2>";

            if (count($rank) > 0) {

                if (isset($_POST['cat_new']) && isset($_POST['cat_name'])) {
                    $cid = $allianceBoardCategoryRepository->addCategory($_POST['cat_name'], $_POST['cat_desc'], (int) $_POST['cat_order'], $_POST['cat_bullet'], $alliance->id);
                    $newRanks = array_map(fn ($value) => (int) $value, $_POST['cr'] ?? []);
                    $allianceBoardCategoryRankRepository->replaceRanks($cid, 0, $newRanks);
                    success_msg("Neue Kategorie gespeichert!");
                } elseif (isset($_POST['cat_edit']) && isset($_POST['cat_name']) && isset($_POST['cat_id']) && intval($_POST['cat_id']) > 0) {
                    $catid = intval($_POST['cat_id']);
                    $allianceBoardCategoryRepository->updateCategory($catid, $_POST['cat_name'], $_POST['cat_desc'], (int) $_POST['cat_order'], $_POST['cat_bullet'], $alliance->id);

                    $newRanks = array_map(fn ($value) => (int) $value, $_POST['cr'] ?? []);
                    $allianceBoardCategoryRankRepository->replaceRanks($catid, 0, $newRanks);
                    success_msg("&Auml;nderungen gespeichert!");
                } elseif (isset($_POST['cat_edit']) && isset($_POST['bnd_id']) && intval($_POST['bnd_id']) > 0) {
                    $bndid = intval($_POST['bnd_id']);
                    $newRanks = array_map(fn ($value) => (int) $value, $_POST['cr'] ?? []);
                    $allianceBoardCategoryRankRepository->replaceRanks(0, $bndid, $newRanks);
                    success_msg("&Auml;nderungen gespeichert!");
                } elseif (isset($_POST['cat_delete']) && isset($_POST['cat_id']) && intval($_POST['cat_id']) > 0) {
                    $catid = intval($_POST['cat_id']);
                    $allianceBoardCategoryRepository->deleteCategory($catid, $alliance->id);
                    success_msg("Kategorie gelöscht!");
                }

                $categories = $allianceBoardCategoryRepository->getCategories($alliance->id);
                if (count($categories) > 0) {
                    $categoryIds = array_map(fn (Category $category) => $category->id, $categories);
                    $postCounts = $allianceBoardCategoryRepository->getCategoryPostCounts($categoryIds);
                    $topicCounts = $allianceBoardCategoryRepository->getCategoryTopicCounts($categoryIds);

                    tableStart();
                    echo "<tr><th colspan=\"2\">Kategorie</th><th>Posts</th><th>Topics</th><th>Letzer Beitrag</th>";
                    if ($isAdmin) {
                        echo "<th style=\"width:50px;\">Aktionen</th>";
                    }
                    echo "</tr>";
                    $accessCnt = 0;
                    foreach ($categories as $category) {
                        if ($isAdmin || isset($myCat[$category->id])) {
                            $accessCnt++;
                            $topic = $allianceBoardTopicRepository->getTopicWithLatestPost($category->id);
                            if ($topic !== null) {
                                $ps = "<a href=\"?page=$page&amp;topic=" . $topic->id . "#" . $topic->post->id . "\" " . tm($topic->subject . ", " . df($topic->timestamp), "Geschrieben von: <b>" . $topic->post->userNick . "</b>") . ">" . $topic->subject . "<br/>" . df($topic->timestamp) . "</a>";
                            } else
                                $ps = "-";
                            echo "<tr>";
                            if ($category->bullet == "" || !is_file(BOARD_BULLET_DIR . "/" . $category->bullet)) $category->bullet = BOARD_DEFAULT_IMAGE;
                            echo "<td style=\"width:40px;vertical-align:middle;\">
                                    <a href=\"?page=$page&amp;bnd=0&cat=" . $category->id . "\">
                                        <img src=\"" . BOARD_BULLET_DIR . "/" . $category->bullet . "\" style=\"width:40px;height:40px;\" />
                                    </a>
                                </td>";
                            echo "<td style=\"width:300px;\"";
                            if ($isAdmin) {
                                $rstr = "";
                                $categoryRankIds = $allianceBoardCategoryRankRepository->getRanksForCategories($category->id);
                                foreach ($rank as $k => $v) {
                                    if (in_array($k, $categoryRankIds, true)) {
                                        $rstr .= $v . ", ";
                                    }
                                }

                                if ($rstr != "") $rstr = substr($rstr, 0, strlen($rstr) - 2);
                                echo " " . tm("Admin-Info: " . $category->name, "<b>Position:</b> " . $category->order . "<br/><b>Zugriff:</b> " . $rstr) . "";
                            }
                            echo ">
                                <b><a href=\"?page=$page&amp;bnd=0&cat=" . intval($category->id) . "\">" . ($category->name != "" ? $category->name : "Unbenannt") . "</a></b>
                                <br/>" . text2html($category->description) . "</td>";
                            echo "<td>" . $postCounts[$category->id] . "</td>";
                            echo "<td>" . $topicCounts[$category->id] . "</td>";
                            echo "<td>$ps</td>";
                            if ($isAdmin) {
                                echo "<td style=\"vertical-align:middle;text-align:center;\">
                                        <a href=\"?page=$page&editcat=" . $category->id . "\">" . icon('edit') . "</a>
                                        <a href=\"?page=$page&delcat=" . $category->id . "\">" . icon('delete') . "</a>
                                    </td>";
                            }
                            echo "</tr>";
                        }
                    }
                    if ($accessCnt == 0)
                        echo "<tr><td colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
                    tableEnd();
                } else
                    error_msg("Keine Kategorien vorhanden!");
                if ($isAdmin)
                    echo "<br/><input type=\"button\" value=\"Neue Kategorie erstellen\" onclick=\"document.location='?page=$page&action=newcat'\" /> &nbsp; ";
                echo "<input type=\"button\" value=\"Zur Allianzseite\" onclick=\"document.location='?page=alliance'\" /><br/><br/>";


                //shows Bnd forums
                $res = dbquery("SELECT * FROM alliance_bnd WHERE (alliance_bnd_alliance_id1=" . $alliance->id . " || alliance_bnd_alliance_id2=" . $alliance->id . ") AND alliance_bnd_level=2 ORDER BY alliance_bnd_id");
                if (mysql_num_rows($res) > 0) {
                    $allianceBnds = [];
                    $allianceBndIds = [];
                    while ($arr = mysql_fetch_array($res)) {
                        $allianceBnds[] = $arr;
                        $allianceBndIds[] = (int) $arr['alliance_bnd_id'];
                    }

                    $topicCounts = $allianceBoardTopicRepository->getBndTopicCounts($allianceBndIds);
                    $postCounts = $allianceBoardTopicRepository->getBndPostCounts($allianceBndIds);

                    tableStart();
                    echo "<tr><th colspan=\"2\">Bündnisforen</th><th>Posts</th><th>Topics</th><th>Letzer Beitrag</th>";
                    if ($isAdmin) {
                        echo "<th>Aktionen</th>";
                    }
                    echo "</tr>";
                    $accessCnt = 0;
                    $alliance_bnd_id = 0;
                    foreach ($allianceBnds as $arr) {
                        if ($arr['alliance_bnd_alliance_id2'] == $alliance->id) {
                            $alliance_bnd_id = $arr['alliance_bnd_alliance_id1'];
                        } else {
                            $alliance_bnd_id = $arr['alliance_bnd_alliance_id2'];
                        }

                        if ($isAdmin || isset($myCat[$arr['alliance_bnd_id']])) {
                            $accessCnt++;
                            $topic = $allianceBoardTopicRepository->getTopicWithLatestPost(0, (int) $arr['alliance_bnd_id']);
                            if ($topic !== null) {
                                $ps = "<a href=\"?page=$page&amp;topic=" . $topic->id . "#" . $topic->post->id . "\" " . tm($topic->subject . ", " . df($topic->timestamp), "Geschrieben von: <b>" . $topic->post->userNick . "</b>") . ">" . $topic->subject . "<br/>" . df($topic->timestamp) . "</a>"; //ToDo User auch von anderen Allianzen
                            } else
                                $ps = "-";
                            echo "<tr>";
                            if (!isset($arr['cat_bullet']) || $arr['cat_bullet'] == "" || !is_file(BOARD_BULLET_DIR . "/" . $arr['cat_bullet'])) {
                                $arr['cat_bullet'] = BOARD_DEFAULT_IMAGE;
                            }
                            echo "<td style=\"width:40px;\"><img src=\"" . BOARD_BULLET_DIR . "/" . $arr['cat_bullet'] . "\" style=\"width:40px;height:40px;\" /></td>";
                            echo "<td style=\"width:300px;\"";
                            if ($isAdmin) {
                                $rstr = "";
                                $bndRankIds = $allianceBoardCategoryRankRepository->getRanksForBnd((int) $arr['alliance_bnd_id']);
                                foreach ($rank as $k => $v) {
                                    if (in_array($k, $bndRankIds, true)) {
                                        $rstr .= $v . ", ";
                                    }
                                }

                                if ($rstr != "") $rstr = substr($rstr, 0, strlen($rstr) - 2);
                                echo " " . tm("Admin-Info: " . stripslashes($allianceNames[$alliance_bnd_id]),/*"<b>Position:</b> ".$arr['cat_order']."<br/>*/ "<b>Zugriff:</b> " . $rstr) . "";
                            }
                            echo "><b><a href=\"?page=$page&amp;cat=0&bnd=" . $arr['alliance_bnd_id'] . "\"";
                            echo ">" . stripslashes($allianceNames[$alliance_bnd_id]) . "</a></b><br/>" . text2html($arr['alliance_bnd_text']) . "</td>";
                            echo "<td>" . $postCounts[intval($arr['alliance_bnd_id'])] . "</td>";
                            echo "<td>" . $topicCounts[intval($arr['alliance_bnd_id'])] . "</td>";
                            echo "<td>$ps</td>";
                            if ($isAdmin) {
                                echo "<td style=\"width:90px;\"><input type=\"button\" value=\"Bearbeiten\" onclick=\"document.location='?page=$page&editbnd=" . $arr['alliance_bnd_id'] . "'\" /><br/>
                                    </td>";
                            }
                            echo "</tr>";
                        }
                    }
                    if ($accessCnt == 0)
                        echo "<tr><td colspan=\"5\"><i>Du hast zu keiner Kategorie Zugriff!</i></td></tr>";
                    tableEnd();
                }
            } else {
                error_msg("Bevor das Forum benutzt werden kann müssen [page alliance action=ranks]Ränge[/page] erstellt werden!");
            }
        }
    } else
        error_msg("Die Allianz existiert nicht!");
} else {
    info_msg("Du bist in keiner Allianz und kannst darum das Allianzboard nicht nutzen!");
}
