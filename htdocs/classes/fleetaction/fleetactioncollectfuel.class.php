<?PHP

class FleetActionCollectFuel extends FleetAction
{

    public function __construct()
    {
        $this->code = "collectfuel";
        $this->name = "Gas saugen";
        $this->desc = RES_FUEL . " aus der Atmospähre von Gasplaneten saugen.";
        $this->longDesc = "In Andromeda gibt es Planeten die sich aus verschiedenen Gasen zusammensetzen. Diese Planeten sind nicht bewohnbar. Nichts desto trotz, hat man sich deren Eigenschaft zu Nutze gemacht und Schiffe entwickelt, die deren Gase absaugen und sie in verwendbares Tritium umwandeln können! Die Gasplaneten bilden je nach Grösse ihre Gase wieder her. Sie sind immer bestrebt daran ihr ganzes Volumen mit den Gasen zu füllen. Durch die Entstehung neuer Gase entsteht W&auml;rme. Es ist nicht selten, dass sich Gasexplosionen entfesseln und dass aufgrund dessen einige Schiffe in der Flotte von solchen Explosionen zerst&ouml;rt werden! Es lässt sich streiten, ob sich das Gassaugen lohnt, denn es ist ausserdem so, dass nur speziell dafür gebaute Schiffe das Gas in Tritium umwandeln k&ouml;nnen. So ist es also nicht m&ouml;glich normale Transporter auf die Mission mitzuschicken, in der Hoffnung, dass mehr Tritium abgebaut werden kann. Der Gasbezug erfolgt nur durch die Gassauger.";
        $this->visible = false;
        $this->exclusive = false;
        $this->attitude = 0;

        $this->allowPlayerEntities = false;
        $this->allowActivePlayerEntities = false;
        $this->allowOwnEntities = false;
        $this->allowNpcEntities = true;
        $this->allowSourceEntity = false;
        $this->allowAllianceEntities = false;
        $this->allianceAction = false;
    }

    function startAction()
    {
    }
    function cancelAction()
    {
    }
    function targetAction()
    {
    }
    function returningAction()
    {
    }
}
