<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to add the "mode" column to the "tracks" table
 * @author Garma
 */
class Migration7 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("ALTER TABLE `tracks` ADD `mode` INT(11) NOT NULL AFTER `name`;");
        App::db()->executeQuery("UPDATE `tracks` SET `mode` = 1;");
    }
}
