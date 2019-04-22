<?php

/** @var \EtoA\Quest\QuestPresenter $questPresenter */
$questPresenter = $app['etoa.quest.presenter'];
echo '
    <h2>Quests</h2>

    <table width="100%" cellpadding="3" cellspacing="1" align="center">
        <tbody>
            <tr>
                <th valign="top" class="tbltitle">ID</th>
                <th valign="top" class="tbltitle">Titel</th>
                <th valign="top" class="tbltitle">Beschreibung</th>
                <th valign="top" class="tbltitle">Bedingung</th>
                <th valign="top" class="tbltitle">Tasks</th>
                <th valign="top" class="tbltitle">Belohnung</th>
            </tr>';
foreach ($app['cubicle.quests.quests'] as $quest) {
    echo '
        <tr>
            <td class="tbldata">' . $quest['id'].'</td>
            <td class="tbldata">' . $quest['title'].'</td>
            <td class="tbldata">' . $quest['description'].'</td>
            <td class="tbldata">' . (isset($quest['trigger']) ? $quest['trigger']['type'] . ' ' . $quest['trigger']['operator'] . ' ' . $quest['trigger']['value'] : '').'</td>
            <td class="tbldata">' . $quest['task']['type'] . ' ' . $quest['task']['operator'] . ' ' . $quest['task']['value'] . '</td>
            <td class="tbldata">' . implode("\n", $questPresenter->buildRewards($quest)) .'</td>
        </tr>';
}
echo '</tbody></table>';
