<?php

declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class UserPropertiesRepository extends AbstractRepository
{
    public function addBlank(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('user_properties')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute();

        $this->createQueryBuilder()
            ->insert('user_properties')
            ->values([
                'id' => ':id',
            ])
            ->setParameters([
                'id' => $id,
            ])
            ->execute();
    }

    public function getOrCreateProperties(int $userId): UserProperties
    {
        $data = $this->getProperties($userId);

        if ($data === null) {
            $this->addBlank($userId);
            $data = $this->getProperties($userId);
        }

        return $data;
    }

    public function getProperties(int $userId): ?UserProperties
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('user_properties')
            ->where('id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new UserProperties($data) : null;
    }

    public function storeProperties(int $userId, UserProperties $properties): void
    {
        $this->createQueryBuilder()
            ->update('user_properties')
            ->where('id = :userId')
            ->set('css_style', ':cssStyle')
            ->set('image_url', ':imageUrl')
            ->set('image_ext', ':imageExt')
            ->set('planet_circle_width', ':planetCircleWidth')
            ->set('item_show', ':itemShow')
            ->set('item_order_ship', ':itemOrderShip')
            ->set('item_order_def', ':itemOrderDef')
            ->set('item_order_bookmark', ':itemOrderBookmark')
            ->set('item_order_way', ':itemOrderWay')
            ->set('image_filter', ':imageFilter')
            ->set('msgsignature', ':msgSignature')
            ->set('msgcreation_preview', ':msgCreationPreview')
            ->set('msg_preview', ':msgPreview')
            ->set('helpbox', ':helpBox')
            ->set('notebox', ':noteBox')
            ->set('msg_copy', ':msgCopy')
            ->set('msg_blink', ':msgBlink')
            ->set('spyship_id', ':spyShipId')
            ->set('spyship_count', ':spyShipCount')
            ->set('analyzeship_id', ':analyzeShipId')
            ->set('analyzeship_count', ':analyzeShipCount')
            ->set('exploreship_id', ':exploreShipId')
            ->set('exploreship_count', ':exploreShipCount')
            ->set('show_cellreports', ':showCellreports')
            ->set('havenships_buttons', ':havenShipsButtons')
            ->set('show_adds', ':showAdds')
            ->set('fleet_rtn_msg', ':fleetRtnMsg')
            ->set('small_res_box', ':smallResBox')
            ->set('startup_chat', ':startUpChat')
            ->set('chat_color', ':chatColor')
            ->set('keybinds_enable', ':enableKeybinds')
            ->setParameters([
                'userId' => $userId,
                'cssStyle' => $properties->cssStyle,
                'imageUrl' => $properties->imageUrl,
                'imageExt' => $properties->imageExt,
                'planetCircleWidth' => $properties->planetCircleWidth,
                'itemShow' => $properties->itemShow,
                'itemOrderShip' => $properties->itemOrderShip,
                'itemOrderDef' => $properties->itemOrderDef,
                'itemOrderBookmark' => $properties->itemOrderBookmark,
                'itemOrderWay' => $properties->itemOrderWay,
                'imageFilter' => $properties->imageFilter,
                'msgSignature' => $properties->msgSignature,
                'msgCreationPreview' => $properties->msgCreationPreview,
                'msgPreview' => $properties->msgPreview,
                'helpBox' => $properties->helpBox,
                'noteBox' => $properties->noteBox,
                'msgCopy' => $properties->msgCopy,
                'msgBlink' => $properties->msgBlink,
                'spyShipId' => $properties->spyShipId,
                'spyShipCount' => $properties->spyShipCount,
                'analyzeShipId' => $properties->analyzeShipId,
                'analyzeShipCount' => $properties->analyzeShipCount,
                'exploreShipId' => $properties->exploreShipId,
                'exploreShipCount' => $properties->exploreShipCount,
                'showCellreports' => $properties->showCellreports,
                'havenShipsButtons' => $properties->havenShipsButtons,
                'showAdds' => $properties->showAdds,
                'fleetRtnMsg' => (int) $properties->fleetRtnMsg,
                'smallResBox' => (int) $properties->smallResBox,
                'startUpChat' => (int) $properties->startUpChat,
                'chatColor' => $properties->chatColor,
                'enableKeybinds' => (int) $properties->enableKeybinds,
            ])
            ->execute();
    }

    /**
     * @return array<string, int>
     */
    public function getDesignStats(int $limit): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    css_style,
                    COUNT(id) cnt
                FROM
                    user_properties
                GROUP BY
                    css_style
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getImagePackStats(int $limit): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    image_url as name,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    image_url
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }

    /**
     * @return array<int, array{name: string, cnt: int}>
     */
    public function getImageExtensionStats(int $limit): array
    {
        $data = $this->getConnection()
            ->executeQuery(
                "SELECT
                    image_ext as name,
                    COUNT(id) as cnt
                FROM
                    user_properties
                GROUP BY
                    image_ext
                ORDER BY
                    cnt DESC
                LIMIT $limit;"
            )
            ->fetchAllAssociative();

        return array_map(fn ($arr) => [
            'name' => (string) $arr['name'],
            'cnt' => (int) $arr['cnt'],
        ], $data);
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(id)')
            ->from('user_properties')
            ->where($qb->expr()->notIn('id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchOne();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function deleteOrphaned(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_properties')
            ->where($qb->expr()->notIn('id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('user_properties')
            ->where('id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
