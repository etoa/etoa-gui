<?php

declare(strict_types=1);

namespace EtoA\UI;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityCoordinates;
use Symfony\Component\HttpFoundation\ParameterBag;

class EntityCoordinatesSelector
{
    private ConfigurationService $config;

    public function __construct(
        ConfigurationService $config
    ) {
        $this->config = $config;
    }

    public function getHTML(string $name, ?EntityCoordinates $coordinates = null, bool $showEmptyOption = true): string
    {
        $str = "<select name=\"" . $name . "_sx\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\">(egal)</option>";
        }
        for ($x = 1; $x <= $this->config->param1Int('num_of_sectors'); $x++) {
            $str .= "<option value=\"$x\"";
            if ($coordinates !== null && $coordinates->sx == $x) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">$x</option>";
        }
        $str .= "</select>";
        $str .= "/";
        $str .= "<select name=\"" . $name . "_sy\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\">(egal)</option>";
        }
        for ($x = 1; $x <= $this->config->param2Int('num_of_sectors'); $x++) {
            $str .= "<option value=\"$x\"";
            if ($coordinates !== null && $coordinates->sy == $x) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">$x</option>";
        }
        $str .= "</select>";
        $str .= " : ";
        $str .= "<select name=\"" . $name . "_cx\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\">(egal)</option>";
        }
        for ($x = 1; $x <= $this->config->param1Int('num_of_cells'); $x++) {
            $str .= "<option value=\"$x\"";
            if ($coordinates !== null && $coordinates->cx == $x) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">$x</option>";
        }
        $str .= "</select>";
        $str .= "/";
        $str .= "<select name=\"" . $name . "_cy\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\">(egal)</option>";
        }
        for ($x = 1; $x <= $this->config->param2Int('num_of_cells'); $x++) {
            $str .= "<option value=\"$x\"";
            if ($coordinates !== null && $coordinates->cy == $x) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">$x</option>";
        }
        $str .= "</select>";
        $str .= " : ";
        $str .= "<select name=\"" . $name . "_pos\">";
        if ($showEmptyOption) {
            $str .= "<option value=\"\">(egal)</option>";
        }
        for ($x = 0; $x <= $this->config->param2Int('num_planets'); $x++) {
            $str .= "<option value=\"$x\"";
            if ($coordinates !== null && $coordinates->pos == $x) {
                $str .= " selected=\"selected\"";
            }
            $str .= ">$x</option>";
        }
        $str .= "</select>";

        return $str;
    }

    public function parse(string $name, ParameterBag $parameters): ?EntityCoordinates
    {
        if (
            $parameters->getInt($name . "_sx") > 0
            && $parameters->getInt($name . "_sy") > 0
            && $parameters->getInt($name . "_cx") > 0
            && $parameters->getInt($name . "_cy") > 0
            && $parameters->get($name . "_pos", '') != ''
        ) {
            return new EntityCoordinates(
                $parameters->getInt($name . "_sx"),
                $parameters->getInt($name . "_sy"),
                $parameters->getInt($name . "_cx"),
                $parameters->getInt($name . "_cy"),
                $parameters->getInt($name . "_pos")
            );
        }

        return null;
    }
}
