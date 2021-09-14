<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntitySearch;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\GalaxyMap;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserUniverseDiscoveryService;
use Symfony\Component\HttpFoundation\Request;

include("image.inc.php");

define('IMG_DIR',"images/imagepacks/Discovery");

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var CellRepository $cellRepo */
$cellRepo = $app[CellRepository::class];

/** @var EntityRepository $entityRepo */
$entityRepo = $app[EntityRepository::class];

/** @var StarRepository $starRepo */
$starRepo = $app[StarRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var UserUniverseDiscoveryService $userUniverseDiscoveryService */
$userUniverseDiscoveryService = $app[UserUniverseDiscoveryService::class];

/** @var Request */
$request = Request::createFromGlobals();

$sx_num = $config->param1Int('num_of_sectors');
$sy_num = $config->param2Int('num_of_sectors');
$cx_num = $config->param1Int('num_of_cells');
$cy_num = $config->param2Int('num_of_cells');
$p_num_min = $config->param2Int('num_planets');
$p_num_max = $config->param2Int('num_planets');

$size = min(isset($_GET['size']) ? intval($_GET['size']) : GalaxyMap::WIDTH, 3000);

$legend = isset($_GET['legend']);
$legendHeight = $legend ? GalaxyMap::LEGEND_HEIGHT : 0;

define('GALAXY_IMAGE_SCALE', $size /((($sx_num-1)*10)+$cx_num));

$w = $size;
$h = $sy_num*$cy_num*GALAXY_IMAGE_SCALE + $legendHeight;
$im = imagecreatetruecolor($w,$h);

$colBlack = imagecolorallocate($im,0,0,0);
$colGrey = imagecolorallocate($im,120,120,120);
$colYellow = imagecolorallocate($im,255,255,0);
$colOrange = imagecolorallocate($im,255,100,0);
$colWhite = imagecolorallocate($im,255,255,255);
$colGreen = imagecolorallocate($im,0,255,0);
$colBlue = imagecolorallocate($im,150,150,240);
$colViolett = imagecolorallocate($im,200,0,200);
$colRe = imagecolorallocate($im,200,0,200);

$admin = isset($s) && $s instanceof AdminSession;

if (isset($_SESSION) || $admin)
{
    if (isset($_SESSION))
    {
        $s = $_SESSION;
    }
    if ($admin || (isset($s['user_id']) && $s['user_id'] > 0))
    {
        $user = null;
        if ($admin && isset($_GET['user']))
        {
            $user = $userRepository->getUser($request->query->getInt('user'));
        }
        else if (!$admin && isset($s))
        {
            $user = $userRepository->getUser(intval($s['user_id']));
        }

        $starImageSrc = imagecreatefrompng(IMG_DIR."/stars/star4_small.png");
        $starImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
        imagecopyresampled($starImage,$starImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($starImageSrc),imagesy($starImageSrc));

        $nebulaImageSrc = imagecreatefrompng(IMG_DIR."/nebulas/nebula2_small.png");
        $nebulaImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
        imagecopyresampled($nebulaImage,$nebulaImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($nebulaImageSrc),imagesy($nebulaImageSrc));

        $asteroidImageSrc = imagecreatefrompng(IMG_DIR."/asteroids/asteroids1_small.png");
        $asteroidImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
        imagecopyresampled($asteroidImage,$asteroidImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($asteroidImageSrc),imagesy($asteroidImageSrc));

        $spaceImageSrc = imagecreatefrompng(IMG_DIR."/space/space1_small.png");
        $spaceImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
        imagecopyresampled($spaceImage,$spaceImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($spaceImageSrc),imagesy($spaceImageSrc));

        $wormholeImageSrc = imagecreatefrompng(IMG_DIR."/wormholes/wormhole1_small.png");
        $wormholeImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
        imagecopyresampled($wormholeImage,$wormholeImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($wormholeImageSrc),imagesy($wormholeImageSrc));

        $persistentWormholeImageSrc = imagecreatefrompng(IMG_DIR."/wormholes/wormhole_persistent1_small.png");
        $persistentWormholeImage = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
        imagecopyresampled($persistentWormholeImage,$persistentWormholeImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($persistentWormholeImageSrc),imagesy($persistentWormholeImageSrc));

        $unexploredImages = array();
        for ($i=1;$i<7;$i++) {
            $unexploredImageSrc = imagecreatefrompng(IMG_DIR."/unexplored/fog$i.png");
            $unexploredImages[$i] = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($unexploredImages[$i],$unexploredImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($unexploredImageSrc),imagesy($unexploredImageSrc));
        }

        $fogborderImages = array();
        for ($i=1;$i<16;$i++) {
            $fogborderImageSrc = imagecreatefrompng(IMG_DIR."/unexplored/fogborder$i.png");
            $fogborderImages[$i] = imagecreatetruecolor(GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
            imagecopyresampled($fogborderImages[$i],$fogborderImageSrc,0,0,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($fogborderImageSrc),imagesy($fogborderImageSrc));
        }

        if (isset($_GET['type']) && $_GET['type']=="alliance")
        {
            $col = [];
            for ($x=1;$x<=$p_num_max;$x++)
            {
                $col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
            }
            $cells = $cellRepo->getCellPopulationForUserAlliance((int) $_SESSION['user_id']);
            foreach ($cells as $cell)
            {
                $x = ((($cell->sx - 1) * $cx_num + $cell->cx) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                $y = $h - $legendHeight + GALAXY_IMAGE_SCALE - ((($cell->sy - 1) * $cy_num + $cell->cy) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                imagefilledellipse($im, $x, $y, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$cell->count]);
            }
            if ($legend) {
                imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
                imagefilledellipse ($im,80,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[$p_num_max]);
                imagefilledellipse ($im,135,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[floor($p_num_max/2)]);
                imagefilledellipse ($im,205,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[3]);
            }
        }
        elseif (isset($_GET['type']) && $_GET['type']=="own")
        {
            $col = [];
            for ($x=1;$x<=$p_num_max;$x++)
            {
                $col[$x] = imagecolorallocate($im,105+(150/$p_num_max*$x),105+(150/$p_num_max*$x),0);
            }

            $cells = $cellRepo->getCellPopulationForUser((int) $_SESSION['user_id']);
            foreach ($cells as $cell)
            {
                $x = ((($cell->sx - 1) * $cx_num + $cell->cx) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                $y = $h - $legendHeight + GALAXY_IMAGE_SCALE - ((($cell->sy - 1) * $cy_num + $cell->cy) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                imagefilledellipse($im, $x, $y, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[$cell->count]);
            }
            if ($legend) {
                imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
                imagefilledellipse ($im,80,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[$p_num_max]);
                imagefilledellipse ($im,135,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[floor($p_num_max/2)]);
                imagefilledellipse ($im,205,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[3]);
            }
        }
        elseif (isset($_GET['type']) && $_GET['type']=="populated")
        {
            $col = [];
            for ($x=1;$x<=$p_num_max;$x++)
            {
                $col[$x] = imagecolorallocate($im,(255/$p_num_max*$x),(255/$p_num_max*$x),0);
            }
            $cells = $cellRepo->getCellPopulation();
            foreach ($cells as $cell)
            {
                $x = ((($cell->sx - 1) * $cx_num + $cell->cx) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                $y = $h - $legendHeight + GALAXY_IMAGE_SCALE - ((($cell->sy - 1) * $cy_num + $cell->cy) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                imagefilledellipse($im, $x, $y, GalaxyMap::DOT_RADIUS * 2, GalaxyMap::DOT_RADIUS * 2, $col[max(3, $cell->count)]);
            }
            if ($legend) {
                imagestring($im,3,10,$h-$legendHeight+10,"Legende:    Viel    Mittel    Wenig",$colWhite);
                imagefilledellipse ($im,80,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[$p_num_max]);
                imagefilledellipse ($im,135,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[floor($p_num_max/2)]);
                imagefilledellipse ($im,205,$h-$legendHeight+10+GalaxyMap::DOT_RADIUS*2,GalaxyMap::DOT_RADIUS*2,GalaxyMap::WIDTH*2,$col[3]);
            }
        }
        else
        {
            $entities = $entityRepo->searchEntities(EntitySearch::create()->pos(0));
            if (count($entities) > 0)
            {
                foreach ($entities as $entity)
                {
                    $x = ((($entity->sx - 1) * $cx_num + $entity->cx) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                    $y = $h - $legendHeight + GALAXY_IMAGE_SCALE - ((($entity->sy - 1) * $cy_num + $entity->cy) * GALAXY_IMAGE_SCALE) - (GALAXY_IMAGE_SCALE / 2);
                    $xe = $x - (GALAXY_IMAGE_SCALE / 2);
                    $ye = $y - (GALAXY_IMAGE_SCALE / 2);

                    $sx = $entity->sx;
                    $sy = $entity->sy;
                    $xcoords = $entity->cx;
                    $ycoords = $entity->cy;

                    if (($admin && $user === null) || $userUniverseDiscoveryService->discovered($user, (($entity->sx - 1) * $cx_num) + $entity->cx,(($entity->sy - 1) * $cy_num) + $entity->cy))
                    {
                        if ($entity->code == EntityType::STAR)
                        {
                            $star = $starRepo->find($entity->id);
                            $starImageSrc = imagecreatefrompng(IMG_DIR."/stars/star".$star->typeId."_small.png");
                            imagecopyresampled($im,$starImageSrc,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,imagesx($starImageSrc),imagesy($starImageSrc));
                        }
                        elseif ($entity->code == EntityType::WORMHOLE)
                        {
                            $wh = new Wormhole($entity->id);
                            if ($wh->isPersistent())
                            {
                                imagecopyresampled($im,$persistentWormholeImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                            } else {
                                imagecopyresampled($im,$wormholeImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                            }
                        }
                        elseif ($entity->code == EntityType::ASTEROID)
                        {
                            imagecopyresampled($im,$asteroidImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                        }
                        elseif ($entity->code == EntityType::NEBULA)
                        {
                            imagecopyresampled($im,$nebulaImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                        }
                        elseif ($entity->code == EntityType::EMPTY_SPACE || $entity->code == EntityType::MARKET)
                        {
                            imagecopyresampled($im,$spaceImage,$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                        }
                        else {
                            continue;
                        }
                    }
                    elseif ($user !== null)
                    {
                        $fogCode = 0;
                        // Bottom
                        $fogCode += $ycoords > 1 && $userUniverseDiscoveryService->discovered($user, (($sx - 1) * $cx_num) + $xcoords  , (($sy - 1) * $cy_num) + $ycoords-1) ? 1 : 0;
                        // Left
                        $fogCode += $xcoords > 1 && $userUniverseDiscoveryService->discovered($user, (($sx - 1) * $cx_num) + $xcoords-1, (($sy - 1) * $cy_num) + $ycoords  ) ? 2 : 0;
                        // Right
                        $fogCode += $xcoords < $cx_num && $userUniverseDiscoveryService->discovered($user, (($sx - 1) * $cx_num) + $xcoords+1, (($sy - 1) * $cy_num) + $ycoords  ) ? 4 : 0;
                        // Top
                        $fogCode += $ycoords < $cy_num && $userUniverseDiscoveryService->discovered($user, (($sx - 1) * $cx_num) + $xcoords  , (($sy - 1) * $cy_num) + $ycoords+1) ? 8 : 0;
                        if ($fogCode > 0) {
                            imagecopyresampled($im,$fogborderImages[$fogCode],$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                        } else {
                            imagecopyresampled($im,$unexploredImages[mt_rand(1,6)],$xe,$ye,0,0,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE,GALAXY_IMAGE_SCALE);
                        }
                    }
                }
            }
            else
            {
                imagestring($im,3,20,20,"Universum existiert noch nicht!",$colWhite);
            }

            if ($legend) {
                imagestring($im,3,10,$h-$legendHeight+10,"Galaxiekarte",$colWhite);
            }
        }

        for ($x=($cx_num*GALAXY_IMAGE_SCALE);$x<$w;$x+=($cx_num*GALAXY_IMAGE_SCALE))
        {
            MDashedLine($im,$x,0,$x,$h-$legendHeight,$colGrey,$colBlack);
        }
        for ($y=($cy_num*GALAXY_IMAGE_SCALE);$y<$h;$y+=($cy_num*GALAXY_IMAGE_SCALE))
        {
            MDashedLine($im,0,$y,$w,$y,$colGrey,$colBlack);
        }
    }
    else
    {
        imagestring($im,5,10,10,"Nicht eingeloggt!",$colWhite);
    }
}
else
{
    imagestring($im,5,10,10,"Nicht eingeloggt!",$colWhite);
}
echo imagepng($im);
