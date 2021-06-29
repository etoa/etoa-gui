<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceBoardRepository extends AbstractRepository
{
    /**
     * @return array<int>
     */
    public function findCategoryIdsForAlliance(int $allianceId): array
    {
        $data = $this->createQueryBuilder()
            ->select('cat_id')
            ->from('allianceboard_cat')
            ->where('cat_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchFirstColumn();

        return array_map(fn ($row) => (int) $row, $data);
    }

    public function removeCategoriesForAlliance(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('allianceboard_cat')
            ->where('cat_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }

    public function removeCategoryRanksForCategory(int $categoryId): void
    {
        $this->createQueryBuilder()
            ->delete('allianceboard_catranks')
            ->where('cr_cat_id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->execute();
    }

    /**
     * @return array<int>
     */
    public function findTopicIdsForCategory(int $categoryId): array
    {
        $data = $this->createQueryBuilder()
            ->select('topic_id')
            ->from('allianceboard_topics')
            ->where('topic_cat_id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->execute()
            ->fetchFirstColumn();

        return array_map(fn ($row) => (int) $row, $data);
    }

    public function removeTopicsForCategory(int $categoryId): void
    {
        $this->createQueryBuilder()
            ->delete('allianceboard_topics')
            ->where('topic_cat_id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->execute();
    }

    /**
     * @return array<int>
     */
    public function findTopicIdsForDiplomacy(int $diplomacyId): array
    {
        $data = $this->createQueryBuilder()
            ->select('topic_id')
            ->from('allianceboard_topics')
            ->where('topic_bnd_id = :diplomacyId')
            ->setParameter('diplomacyId', $diplomacyId)
            ->execute()
            ->fetchFirstColumn();

        return array_map(fn ($row) => (int) $row, $data);
    }

    public function removeTopicsForDiplomacy(int $diplomacyId): void
    {
        $this->createQueryBuilder()
            ->delete('allianceboard_topics')
            ->where('topic_bnd_id = :diplomacyId')
            ->setParameter('diplomacyId', $diplomacyId)
            ->execute();
    }

    public function removePostsForTopic(int $topicId): void
    {
        $this->createQueryBuilder()
            ->delete('allianceboard_posts')
            ->where('post_topic_id = :topicId')
            ->setParameter('topicId', $topicId)
            ->execute();
    }
}
