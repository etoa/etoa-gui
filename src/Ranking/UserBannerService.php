<?php

declare(strict_types=1);

namespace EtoA\Ranking;

use EtoA\Core\Configuration\ConfigurationService;
use GdImage;

class UserBannerService
{
    private ConfigurationService $config;

    private string $userBannerBackgroundImage = "images/userbanner/userbanner1.png";
    private string $userBannerFont = "images/userbanner/calibri.ttf";

    public function __construct(
        ConfigurationService $config
    ) {
        $this->config = $config;
    }

    public function createUserBanner(): void
    {
        $dir = USERBANNER_DIR;
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $createdFiles = array();

        $res = dbquery("
            SELECT
                u.user_nick,
                a.alliance_tag,
                a.alliance_name,
                r.race_name,
                u.user_points,
                u.user_id,
                u.admin,
                u.user_ghost,
                u.user_rank
            FROM
                users u
            LEFT JOIN
                alliances a
                ON u.user_alliance_id=a.alliance_id
            LEFT JOIN
                races r
                On u.user_race_id=r.race_id
            ");
        while ($arr = mysql_fetch_assoc($res)) {
            if ($arr['admin'] == 1) {
                $pt = "  -  Game-Admin";
            } elseif ($arr['user_ghost'] == 1) {
                $pt = "";
            } else {
                $pt = "  -  " . nf($arr['user_points']) . " Punkte, Platz " . $arr['user_rank'] . "";
            }
            $text = $this->config->get('roundname') . $pt;

            $im = $this->createUserBannerImage(
                $arr['user_nick'],
                $arr['alliance_tag'],
                $arr['alliance_name'],
                $arr['race_name'],
                $text
            );

            $file = $this->getUserBannerPath((int) $arr['user_id']);
            if (file_exists($file)) {
                unlink($file);
            }
            imagepng($im, $file);
            chmod($file, 0777);
            imagedestroy($im);
            $createdFiles[] = $file;
        }

        // Remove old banner images
        $dh = opendir($dir);
        while ($f = readdir($dh)) {
            $fp = $dir . '/' . $f;
            if (is_file($fp) && !in_array($fp, $createdFiles, true)) {
                unlink($fp);
            }
        }
        closedir($dh);
    }

    public function getUserBannerPath(int $userId): string
    {
        return USERBANNER_DIR . '/' . md5('user' . $userId) . '.png';
    }

    /** @return resource|GdImage */
    private function createUserBannerImage(
        string $nick,
        ?string $allianceTag,
        ?string $allianceName,
        string $race,
        string $text
    ) {
        $font = realpath(RELATIVE_ROOT . $this->userBannerFont);
        $backgroundImage = RELATIVE_ROOT . $this->userBannerBackgroundImage;

        $im = imagecreatefrompng($backgroundImage);

        $colBlack = imagecolorallocate($im, 0, 0, 0);
        $colGrey = imagecolorallocate($im, 120, 120, 120);
        $colYellow = imagecolorallocate($im, 255, 255, 0);
        $colOrange = imagecolorallocate($im, 255, 100, 0);
        $colWhite = imagecolorallocate($im, 255, 255, 255);
        $colGreen = imagecolorallocate($im, 0, 255, 0);
        $colBlue = imagecolorallocate($im, 150, 150, 240);
        $colViolett = imagecolorallocate($im, 200, 0, 200);
        $colRe = imagecolorallocate($im, 200, 0, 200);

        $nsize = imagettfbbox(16, 0, $font, $nick);

        // Nick
        imagettftext($im, 16, 0, 6, 21, $colBlack, $font, $nick);
        imagettftext($im, 16, 0, 5, 20, $colWhite, $font, $nick);

        // Race
        imagettftext($im, 11, 0, $nsize[2] - $nsize[0] + 16, 21, $colBlack, $font, $race);
        imagettftext($im, 11, 0, $nsize[2] - $nsize[0] + 15, 20, $colWhite, $font, $race);

        // Alliance
        if (filled($allianceTag)) {
            imagettftext($im, 9, 0, 9, 39, $colBlack, $font, "<" . $allianceTag . "> " . $allianceName);
            imagettftext($im, 9, 0, 8, 38, $colWhite, $font, "<" . $allianceTag . "> " . $allianceName);
        }

        // Text
        if (filled($text)) {
            imagettftext($im, 9, 0, 9, 54, $colBlack, $font, $text);
            imagettftext($im, 9, 0, 8, 53, $colWhite, $font, $text);
        }

        return $im;
    }
}
