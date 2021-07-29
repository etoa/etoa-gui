<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Alliance\Board\AllianceBoardCategoryRankRepository;
use EtoA\Alliance\Board\AllianceBoardCategoryRepository;
use EtoA\Alliance\Board\AllianceBoardPostRepository;
use EtoA\Alliance\Board\AllianceBoardTopicRepository;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class AllianceServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function register(Container $pimple): void
    {
        $pimple[AllianceRepository::class] = function (Container $pimple): AllianceRepository {
            return new AllianceRepository($pimple['db']);
        };

        $pimple[AlliancePointsRepository::class] = function (Container $pimple): AlliancePointsRepository {
            return new AlliancePointsRepository($pimple['db']);
        };

        $pimple[AllianceStatsRepository::class] = function (Container $pimple): AllianceStatsRepository {
            return new AllianceStatsRepository($pimple['db']);
        };

        $pimple[AllianceHistoryRepository::class] = function (Container $pimple): AllianceHistoryRepository {
            return new AllianceHistoryRepository($pimple['db']);
        };

        $pimple[AllianceBuildingRepository::class] = function (Container $pimple): AllianceBuildingRepository {
            return new AllianceBuildingRepository($pimple['db']);
        };
        $pimple[AllianceTechnologyRepository::class] = function (Container $pimple): AllianceTechnologyRepository {
            return new AllianceTechnologyRepository($pimple['db']);
        };

        $pimple[AllianceApplicationRepository::class] = function (Container $pimple): AllianceApplicationRepository {
            return new AllianceApplicationRepository($pimple['db']);
        };

        $pimple[AllianceSpendRepository::class] = function (Container $pimple): AllianceSpendRepository {
            return new AllianceSpendRepository($pimple['db']);
        };

        $pimple[AllianceNewsRepository::class] = function (Container $pimple): AllianceNewsRepository {
            return new AllianceNewsRepository($pimple['db']);
        };

        $pimple[AlliancePollRepository::class] = function (Container $pimple): AlliancePollRepository {
            return new AlliancePollRepository($pimple['db']);
        };

        $pimple[AllianceRankRepository::class] = function (Container $pimple): AllianceRankRepository {
            return new AllianceRankRepository($pimple['db']);
        };

        $pimple[AllianceBoardCategoryRepository::class] = function (Container $pimple): AllianceBoardCategoryRepository {
            return new AllianceBoardCategoryRepository($pimple['db']);
        };

        $pimple[AllianceBoardTopicRepository::class] = function (Container $pimple): AllianceBoardTopicRepository {
            return new AllianceBoardTopicRepository($pimple['db']);
        };

        $pimple[AllianceBoardPostRepository::class] = function (Container $pimple): AllianceBoardPostRepository {
            return new AllianceBoardPostRepository($pimple['db']);
        };

        $pimple[AllianceBoardCategoryRankRepository::class] = function (Container $pimple): AllianceBoardCategoryRankRepository {
            return new AllianceBoardCategoryRankRepository($pimple['db']);
        };
    }
}
