<?php

use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Core\Configuration\ConfigurationService;

class Townhall
{
    /**
     * Generate an rss file
     * containing the latest
     * townhall news
     */
    static function genRss()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $rssValue = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n";
        $rssValue .= "<rss version=\"2.0\">\r\n";

        // Build the channel tag
        $rssValue .= "	<channel>\r\n";
        $rssValue .= "		<title>EtoA Rathaus " . $config->get('roundname') . "</title>\r\n";
        $rssValue .= "		<link>http://www.etoa.ch</link>\r\n";
        $rssValue .= "		<description>Rathaus der EtoA " . $config->get('roundname') . "</description>\r\n";
        $rssValue .= "		<language>de</language>\r\n";

        // Build the image tag
        $rssValue .= "		<image>\r\n";
        $rssValue .= "			<title>EtoA Rathaus</title>\r\n";
        $rssValue .= "			<url>http://www.etoa.ch/images/game_logo.gif</url>\r\n";
        $rssValue .= "			<link>http://www.etoa.ch</link>\r\n";
        $rssValue .= "		</image>\r\n";

        /** @var AllianceNewsRepository $allianceNewsRepository */
        $allianceNewsRepository = $app[AllianceNewsRepository::class];
        $publicNews = $allianceNewsRepository->getNewsEntries(0);
        // The records were retrieved OK, let's start building the item tags
        foreach ($publicNews as $news) {
            $rssValue .= "		<item>\r\n";
            $rssValue .= "			<title>" . text2html($news->title) . "</title>\r\n";
            $rssValue .= "			<description>" . text2html(substr($news->text, 0, 100)) . "</description>\r\n";
            $rssValue .= "			<link>http://www.etoa.ch</link>\r\n";
            $rssValue .= "		</item>\r\n";
        }

        $rssValue .= "	</channel>\r\n";
        $rssValue .= "</rss>";

        $d = fopen(RSS_TOWNHALL_FILE, "w");
        fwrite($d, $rssValue);
        fclose($d);
    }
}
