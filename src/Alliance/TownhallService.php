<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;

class TownhallService
{
    /** Townhall-RSS-File, relative to the project directory */
    private const RSS_TOWNHALL_FILE = 'var/rss/townhall.rss';

    public function __construct(
        private readonly ConfigurationService   $config,
        private readonly AllianceNewsRepository $allianceNewsRepository,
        private readonly string                 $projectDir,
    ) {
    }

    /**
     * Generate an rss file
     * containing the latest
     * townhall news
     */
    public function genRss(): void
    {
        $rssValue = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
        $rssValue .= "<rss version=\"2.0\">\r\n";

        // Build the channel tag
        $rssValue .= "	<channel>\r\n";
        $rssValue .= "		<title>EtoA Rathaus " . $this->config->get('roundname') . "</title>\r\n";
        $rssValue .= "		<link>http://www.etoa.ch</link>\r\n";
        $rssValue .= "		<description>Rathaus der EtoA " . $this->config->get('roundname') . "</description>\r\n";
        $rssValue .= "		<language>de</language>\r\n";

        // Build the image tag
        $rssValue .= "		<image>\r\n";
        $rssValue .= "			<title>EtoA Rathaus</title>\r\n";
        $rssValue .= "			<url>http://www.etoa.ch/images/game_logo.gif</url>\r\n";
        $rssValue .= "			<link>http://www.etoa.ch</link>\r\n";
        $rssValue .= "		</image>\r\n";

        $publicNews = $this->allianceNewsRepository->getNewsEntries();
        // The records were retrieved OK, let's start building the item tags
        foreach ($publicNews as $news) {
            $rssValue .= "		<item>\r\n";
            $rssValue .= "			<title>" . BBCodeUtils::toHTML($news->getTitle()) . "</title>\r\n";
            $rssValue .= "			<description>" . BBCodeUtils::toHTML(substr($news->getText(), 0, 100)) . "</description>\r\n";
            $rssValue .= "			<link>http://www.etoa.ch</link>\r\n";
            $rssValue .= "		</item>\r\n";
        }

        $rssValue .= "	</channel>\r\n";
        $rssValue .= "</rss>";

        $file = $this->projectDir . '/' . self::RSS_TOWNHALL_FILE;
        $dir = dirname($file);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($file, $rssValue);
    }
}
