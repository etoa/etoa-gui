<?PHP

use EtoA\Core\Configuration\ConfigurationService;

include("image.inc.php");

define('NUM_LEVELS',25);

$w = 640;
$h = 400;
define('P_LEFT',20);
define('P_RIGHT',10);
define('P_TOP',10);
define('P_BOTTOM',20);
define('LEGEND_HEIGHT',40);

$maxRatio = 30;

$im = imagecreatetruecolor($w,$h);

$colWhite = imagecolorallocate($im,255,255,255);
$colBlack = imagecolorallocate($im,0,0,0);
$colLGrey = imagecolorallocate($im,230,230,230);
$colGrey = imagecolorallocate($im,120,120,120);
$colYellow = imagecolorallocate($im,255,255,0);
$colOrange = imagecolorallocate($im,255,100,0);
$colGreen = imagecolorallocate($im,0,255,0);
$colBlue = imagecolorallocate($im,150,150,240);
$colRed = imagecolorallocate($im,255,0,0);
$colViolett = imagecolorallocate($im,200,0,200);
$colRe = imagecolorallocate($im,200,0,200);

$lineCol = [];
$lineCol[0] = imagecolorallocate($im,0,255,0);
$lineCol[1] = imagecolorallocate($im,11,9,159);
$lineCol[2] = imagecolorallocate($im,150,0,255);
$lineCol[3] = imagecolorallocate($im,255,7,255);
$lineCol[4] = imagecolorallocate($im,255,150,00);
$lineCol[5] = imagecolorallocate($im,255,0,00);
$lineCol[6] = imagecolorallocate($im,255,200,00);

imagefilledrectangle($im,0,0,$w,$h,$colWhite);

$areaW = $w - P_LEFT - P_RIGHT;
$areaH = $h - P_TOP - P_BOTTOM - LEGEND_HEIGHT;
$areaOriginX = P_LEFT;
$areaOriginY = $h-LEGEND_HEIGHT-P_BOTTOM;
$stepX = floor($areaW /(NUM_LEVELS));

$cnt=1;
for ($i=$areaOriginX+$stepX;$i<=$areaOriginX+$areaW;$i+=$stepX)
{
    imageline($im, (int) $i,10, (int) $i,$h-LEGEND_HEIGHT-10,$colLGrey);
    imagestring($im, 2, (int) ($i-imagefontwidth(2)*strlen((string) $cnt)/2), $areaOriginY, (string) $cnt++, $colBlack);
}

ob_start();

for ($i=0;$i<=$maxRatio; $i+=$maxRatio/10)
{
    imagestring($im,2,P_LEFT-imagefontwidth(2)*strlen((string) $i)-2,$areaOriginY-($i/$maxRatio*$areaH), (string) $i,$colBlack);
}

$strx = P_LEFT;
$i = 0;
$res = dbquery("
SELECT
    *
FROM
    buildings
WHERE
    building_type_id=".BUILDING_POWER_CAT."
ORDER BY
    building_order
");
while ($arr = mysql_fetch_array($res))
{
    $startX = $areaOriginX;
    $startY = $areaOriginY;
for ($level=0;$level<=NUM_LEVELS;$level++)
{
        $costs1 = $arr['building_costs_metal']+$arr['building_costs_crystal']+$arr['building_costs_plastic']+$arr['building_costs_fuel']+$arr['building_costs_food'];
        $prod1 = $arr['building_prod_power'];
        $costsLvl = round($costs1 * pow($arr['building_build_costs_factor'],$level-1));
        $prodLvl = round($prod1 * pow($arr['building_production_factor'],$level-1));
        $ratio = round($costsLvl / $prodLvl,1);

        $newX = $areaOriginX+($stepX*$level);
        $newY = $areaOriginY-(($ratio/$maxRatio)*$areaH);
    imageline($im,$startX,$startY, (int) $newX, (int) $newY,$lineCol[$i%7]);
    $startX=$newX;
    $startY=$newY;
}

    imagestring($im,2,$strx,$h-LEGEND_HEIGHT,$arr['building_name'],$lineCol[$i%7]);
    $strx += (imagefontwidth(2)*strlen($arr['building_name']))+10;
    $i++;
}
$res = dbquery("
SELECT
    *
FROM
    ships
WHERE
    ship_prod_power>0
ORDER BY
    ship_order
");
$strx = P_LEFT;

/** @var ConfigurationService */
$config = $app['etoa.config.service'];

while ($arr = mysql_fetch_array($res))
{
    $costs1 = $arr['ship_costs_metal']+$arr['ship_costs_crystal']+$arr['ship_costs_plastic']+$arr['ship_costs_fuel']+$arr['ship_costs_food'];
    $prod1 = $arr['ship_prod_power'];
    $ratio = round($costs1 / $prod1,1);
    imageline($im,$areaOriginX,(int) ($areaOriginY-(($ratio/$maxRatio)*$areaH)),$areaOriginX+$areaW,(int) ($areaOriginY-(($ratio/$maxRatio)*$areaH)), $lineCol[$i%7]);

    $tpb1 = Planet::getSolarPowerBonus($config->param1Int('planet_temp'), $config->param1Int('planet_temp') + $config->getInt('planet_temp'));
    $ratio = round($costs1 / ($prod1+$tpb1),1);
    MDashedLine($im,$areaOriginX,$areaOriginY-(($ratio/$maxRatio)*$areaH),$areaOriginX+$areaW,$areaOriginY-(($ratio/$maxRatio)*$areaH),$lineCol[$i%7],$colWhite);

    $tpb2 = Planet::getSolarPowerBonus($config->param2Int('planet_temp') - $config->getInt('planet_temp'), $config->param2Int('planet_temp'));
    $ratio = round($costs1 / ($prod1+$tpb2),1);
    MDashedLine($im,$areaOriginX,$areaOriginY-(($ratio/$maxRatio)*$areaH),$areaOriginX+$areaW,$areaOriginY-(($ratio/$maxRatio)*$areaH),$lineCol[$i%7],$colWhite);

    imagestring($im,2,$strx,$h-LEGEND_HEIGHT/2,$arr['ship_name'],$lineCol[$i%7]);
    $strx += (imagefontwidth(2)*strlen($arr['ship_name']." ("));
    $i++;
}

$str = ob_get_clean();
imagestring($im,2,10,10,$str,$colBlack);

imageline($im,P_LEFT,$h-LEGEND_HEIGHT-P_BOTTOM,$w-P_RIGHT,$h-LEGEND_HEIGHT-P_BOTTOM,$colBlack);
imageline($im,P_LEFT,P_TOP,P_LEFT,$h-LEGEND_HEIGHT-P_BOTTOM,$colBlack);

echo imagepng($im);

dbclose();
