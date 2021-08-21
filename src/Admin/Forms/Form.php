<?php

declare(strict_types=1);

namespace EtoA\Admin\Forms;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Pimple\Container;
use Twig\Environment;

abstract class Form
{
    protected Container $app;
    protected Environment $twig;

    final public function __construct(Container $app, Environment $twig)
    {
        $this->app = $app;
        $this->twig = $twig;
    }

    protected function createQueryBuilder(): QueryBuilder
    {
        /** @var Connection $connection */
        $connection = $this->app['db'];

        return $connection->createQueryBuilder();
    }

    abstract protected function getName(): string;
    abstract protected function getTable(): string;
    abstract protected function getTableId(): string;
    abstract protected function getOverviewOrderField(): string;

    protected function getOverviewOrder(): string
    {
        return "ASC";
    }

    /**
     * Parameters:
     *
     * name	                DB Field Name
     * text	                Field Description
     * type                 Field Type: text, textarea, radio, select, numeric, color
     * def_val              Default Value
     * size                 Field length (text, date)
     * maxlen               Max Text length (text, date)
     * rows                 Rows (textarea)
     * cols                 Cols (textarea)
     * rcb_elem (Array)	    Checkbox-/Radio Elements (desc=>value)
     * rcb_elem_checked	    Value of default checked Checkbox-/Radio Element (Checkbox: has to be an array)
     * select_elem (Array)  Select Elements (desc=>value)
     * select_elem_checked  Value of default checked Select Element (desc=>value)
     * show_overview        Set 1 to show on overview page
     *
     * @return array<array<string,mixed>>
     */
    abstract protected function getFields(): array;
}
