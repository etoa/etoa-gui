<?php

namespace EtoA\Economy;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\User;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class EconomyService
{
    public function __construct(
        private readonly ConfigurationService $configurationService,
        private readonly PlanetRepository $planetRepository,
        private readonly RequestStack $requestStack,
        private readonly Security                 $security,
    )
    {
    }

    //
    // Rohstoffe/Bewohner und Speicher
    //
    public function renderRess()
    {
        $user = $this->security->getUser()->getData();
        $planets = $user->getPlanets();
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        $cnt_res = 0;
        $max_res = array(0, 0, 0, 0, 0, 0);
        $min_res = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
        $tot_res = array(0, 0, 0, 0, 0, 0);


        $val_res = [];
        $val_store = [];
        $val_time = [];
        foreach ($planets as $planet) {
            //Speichert die aktuellen Rohstoffe in ein Array
            $val_res[$planet->getId()][0] = floor($planet->getResMetal());
            $val_res[$planet->getId()][1] = floor($planet->getResCrystal());
            $val_res[$planet->getId()][2] = floor($planet->getResPlastic());
            $val_res[$planet->getId()][3] = floor($planet->getResFuel());
            $val_res[$planet->getId()][4] = floor($planet->getResFood());
            $val_res[$planet->getId()][5] = floor($planet->getPeople());

            for ($x = 0; $x < 6; $x++) {
                $max_res[$x] = max($max_res[$x], $val_res[$planet->getId()][$x]);
                $min_res[$x] = min($min_res[$x], $val_res[$planet->getId()][$x]);
                $tot_res[$x] += $val_res[$planet->getId()][$x];
            }

            //Speichert die aktuellen Speicher in ein Array
            $val_store[$planet->getId()][0] = floor($planet->getStoreMetal());
            $val_store[$planet->getId()][1] = floor($planet->getStoreCrystal());
            $val_store[$planet->getId()][2] = floor($planet->getStorePlastic());
            $val_store[$planet->getId()][3] = floor($planet->getStoreFuel());
            $val_store[$planet->getId()][4] = floor($planet->getStoreFood());
            $val_store[$planet->getId()][5] = floor($planet->getPeoplePlace());

            //Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

            //Titan
            if ($planet->getProdMetal() > 0) {
                if ($planet->getStoreMetal() - $planet->getResMetal() > 0) {
                    $val_time[$planet->getId()][0] = ceil(($planet->getStoreMetal() - $planet->getResMetal()) / $planet->getProdMetal() * 3600);
                } else {
                    $val_time[$planet->getId()][0] = 0;
                }
            } else {
                $val_time[$planet->getId()][0] = 0;
            }

            //Silizium
            if ($planet->getProdCrystal() > 0) {
                if ($planet->getStoreCrystal() - $planet->getResCrystal() > 0) {
                    $val_time[$planet->getId()][1] = ceil(($planet->getStoreCrystal() - $planet->getResCrystal()) / $planet->getProdCrystal() * 3600);
                } else {
                    $val_time[$planet->getId()][1] = 0;
                }
            } else {
                $val_time[$planet->getId()][1] = 0;
            }

            //PVC
            if ($planet->getProdPlastic() > 0) {
                if ($planet->getStorePlastic() - $planet->getResPlastic() > 0) {
                    $val_time[$planet->getId()][2] = ceil(($planet->getStorePlastic() - $planet->getResPlastic()) / $planet->getProdPlastic() * 3600);
                } else {
                    $val_time[$planet->getId()][2] = 0;
                }
            } else {
                $val_time[$planet->getId()][2] = 0;
            }

            //Tritium
            if ($planet->getProdFuel() > 0) {
                if ($planet->getStoreFuel() - $planet->getResFuel() > 0) {
                    $val_time[$planet->getId()][3] = ceil(($planet->getStoreFuel() - $planet->getResFuel()) / $planet->getProdFuel() * 3600);
                } else {
                    $val_time[$planet->getId()][3] = 0;
                }
            } else {
                $val_time[$planet->getId()][3] = 0;
            }

            //Nahrung
            if ($planet->getProdFood() > 0) {
                if ($planet->getStoreFood() - $planet->getResFood() > 0) {
                    $val_time[$planet->getId()][4] = ceil(($planet->getStoreFood() - $planet->getResFood()) / $planet->getProdFood() * 3600);
                } else {
                    $val_time[$planet->getId()][4] = 0;
                }
            } else {
                $val_time[$planet->getId()][4] = 0;
            }

            //Bewohner
            if ($planet->getProdPeople() > 0) {
                if ($planet->getPeoplePlace() - $planet->getPeople() > 0) {
                    $val_time[$planet->getId()][5] = ceil(($planet->getPeoplePlace() - $planet->getPeople()) / $planet->getProdPeople() * 3600);
                } else {
                    $val_time[$planet->getId()][5] = 0;
                }
            } else {
                $val_time[$planet->getId()][5] = 0;
            }
        }

        ob_start();

        foreach ($planets as $planet) {
            echo '<tr><td><a href="?page=economy&amp;change_entity=' . $planet->getId() . '">' . $planet->getName() . '</a></td>';
            for ($x = 0; $x < 6; $x++) {
                echo '<td';
                if ($max_res[$x] == $val_res[$planet->getId()][$x]) {
                    echo ' style="color:#0f0"';
                } elseif ($min_res[$x] == $val_res[$planet->getId()][$x]) {
                    echo ' style="color:#f00"';
                } else {
                    echo ' ';
                }

                //Der Speicher ist noch nicht gefüllt
                if ($val_res[$planet->getId()][$x] < $val_store[$planet->getId()][$x] && $val_time[$planet->getId()][$x] != 0) {
                    $capacity = $cp->people_place;
                    if ($capacity < 200) {
                        $capacity = 200;
                    }

                    $people_div = $cp->people * (($this->configurationService->getFloat('people_multiply')  + $cp->typePopulation + $user->getRace()->getPopulation() + $cp->starPopulation + ($user->getSpecialist() ? $user->getSpecialist()->getProdPeople() : 1) - 4) * (1 - ($cp->people / ($capacity + 1))) / 24);

                    if ($x < 5) {
                        echo ' ' . tm("Speicher", "Speicher voll in " . StringUtils::formatTimespan($val_time[$planet->getId()][$x]) . "") . '> ';
                    } else {
                        echo ' ' . tm("Wachstum", "Wachstum pro Stunde: " . round($people_div) . "") . '> ';
                    }

                    if ($val_time[$planet->getId()][$x] < 43200) {
                        echo '<i>';
                    }
                    echo StringUtils::formatNumber($val_res[$planet->getId()][$x]);
                    if ($val_time[$planet->getId()][$x] < 43200) {
                        echo '</i>';
                    }
                    echo '</td>';
                }
                //Speicher Gefüllt
                else {
                    echo ' ' . tm("Speicher", "Speicher voll!") . '';
                    echo ' style="" ';
                    echo '><b>' . StringUtils::formatNumber($val_res[$planet->getId()][$x]) . '</b></td>';
                }
            }
            echo '</tr>';
            $cnt_res++;
        }
        echo '<tr><td colspan="7"></td></tr>';
        echo '<tr><th>Total</th>';
        for ($x = 0; $x < 6; $x++)
            echo '<td>' . StringUtils::formatNumber($tot_res[$x]) . '</td>';
        echo '</tr><tr><th>Durchschnitt</th>';
        for ($x = 0; $x < 6; $x++)
            echo '<th>' . StringUtils::formatNumber($tot_res[$x] / $cnt_res) . '</th>';
        echo '</tr>';

        return ob_get_clean();
    }

    //
    // Rohstoffproduktion inkl. Energie
    //
    public function renderProduction()
    {
        $user = $this->security->getUser()->getData();
        $planets = $user->getPlanets();

        $cnt_prod = 0;
        $max_prod = array(0, 0, 0, 0, 0, 0);
        $min_prod = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
        $tot_prod = array(0, 0, 0, 0, 0, 0);
        $val_prod = [];

        foreach ($planets as $planet) {
            //Speichert die aktuellen Rohstoffproduktionen in ein Array
            $val_prod[$planet->getId()][0] = floor($planet->getProdMetal());
            $val_prod[$planet->getId()][1] = floor($planet->getProdCrystal());
            $val_prod[$planet->getId()][2] = floor($planet->getProdPlastic());
            $val_prod[$planet->getId()][3] = floor($planet->getProdFuel());
            $val_prod[$planet->getId()][4] = floor($planet->getProdFood());
            $val_prod[$planet->getId()][5] = floor($planet->getProdPeople());

            for ($x = 0; $x < 6; $x++) {
                $max_prod[$x] = max($max_prod[$x], $val_prod[$planet->getId()][$x]);
                $min_prod[$x] = min($min_prod[$x], $val_prod[$planet->getId()][$x]);
                $tot_prod[$x] += $val_prod[$planet->getId()][$x];
            }

            //Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
            $val_prod[$planet->getId()][5] = floor($planet->getProdPower() - $planet->getUsePower());

            // Gibt Min. / Max. aus
            $max_prod[5] = max($max_prod[5], $val_prod[$planet->getId()][5]);
            $min_prod[5] = min($min_prod[5], $val_prod[$planet->getId()][5]);
            $tot_prod[5] += $val_prod[$planet->getId()][5];
        }

        ob_start();

        foreach ($planets as $planet) {
            echo '<tr><td><a href="?page=economy&amp;change_entity=' . $planet->getId() . '">' . $planet->getName() . '</a></td>';
            for ($x = 0; $x < 6; $x++) {
                echo '<td';
                if ($max_prod[$x] == $val_prod[$planet->getId()][$x]) {
                    echo '  style="color:#0f0"';
                } elseif ($min_prod[$x] == $val_prod[$planet->getId()][$x]) {
                    echo '  style="color:#f00"';
                } else {
                    echo ' ';
                }
                echo '>' . StringUtils::formatNumber($val_prod[$planet->getId()][$x]) . '</td>';
            }
            echo '</tr>';
            $cnt_prod++;
        }
        echo '<tr><td colspan="7"></td></tr>';
        echo '<tr><th>Total</th>';
        for ($x = 0; $x < 6; $x++)
            echo '<td>' . StringUtils::formatNumber($tot_prod[$x]) . '</td>';
        echo '</tr><tr><th>Durchschnitt</th>';
        for ($x = 0; $x < 6; $x++)
            echo '<th>' . StringUtils::formatNumber($tot_prod[$x] / $cnt_prod) . '</th>';
        echo '</tr>';

        return ob_get_clean();
    }
}