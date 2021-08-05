<?PHP

use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipSearch;

class GetShipListJsonResponder extends JsonResponder
{
    function getRequiredParams()
    {
        return array('q');
    }

    function getResponse($params)
    {
        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $this->app[ShipDataRepository::class];

        $data = [];
        $shipNames = $shipRepository->searchShipNames(ShipSearch::create()->showOrBuildable()->nameLike($params['q']), null, 20);
        $data['count'] = count($shipNames);
        if ($data['count'] > 0) {
            $data['entries'] = [];
            foreach ($shipNames as $shipId => $shipName) {
                $data['entries'][] = [
                    'id' => $shipId,
                    'name' => $shipName,
                ];
            }
        }

        return $data;
    }
}
