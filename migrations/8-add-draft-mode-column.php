<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to add the "mode" column to the "drafts" table
 * @author Garma
 */
class Migration8 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("ALTER TABLE `drafts` ADD `mode` INT(11) NOT NULL AFTER `id`;");
        App::db()->executeQuery("UPDATE `drafts` SET `mode` = 1;");
    }
}
