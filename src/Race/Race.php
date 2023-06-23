<?php declare(strict_types=1);

namespace EtoA\Race;

class Race
{
    public int $id;
    public string $name;
    public string $comment;
    public string $shortComment;
    public string $adj1;
    public string $adj2;
    public string $adj3;
    public string $leaderTitle;
    public float $researchTime;
    public float $buildTime;
    public float $fleetTime;
    public float $metal;
    public float $crystal;
    public float $plastic;
    public float $fuel;
    public float $food;
    public float $power;
    public float $population;
    public bool $active;

    public function __construct(array $data)
    {
        $this->id = (int) $data['race_id'];
        $this->name = $data['race_name'];
        $this->comment = $data['race_comment'];
        $this->shortComment = $data['race_short_comment'];
        $this->adj1 = $data['race_adj1'];
        $this->adj2 = $data['race_adj2'];
        $this->adj3 = $data['race_adj3'];
        $this->leaderTitle = $data['race_leadertitle'];
        $this->researchTime = (float) $data['race_f_researchtime'];
        $this->buildTime = (float) $data['race_f_buildtime'];
        $this->fleetTime = (float) $data['race_f_fleettime'];
        $this->metal = (float) $data['race_f_metal'];
        $this->crystal = (float) $data['race_f_crystal'];
        $this->plastic = (float) $data['race_f_plastic'];
        $this->fuel = (float) $data['race_f_fuel'];
        $this->food = (float) $data['race_f_food'];
        $this->power = (float) $data['race_f_power'];
        $this->population = (float) $data['race_f_population'];
        $this->active = (bool) $data['race_active'];
    }
}
