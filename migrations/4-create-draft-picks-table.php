<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to create the "draft_picks" table
 * @author Garma
 */
class Migration4 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("
            CREATE TABLE `draft_picks` (
              `id` int(11) NOT NULL,
              `draftId` int(11) NOT NULL,
              `trackId` int(11) NOT NULL,
              `teamId` int(11) NOT NULL,
              `sortOrder` int(11) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        App::db()->executeQuery("ALTER TABLE `draft_picks` ADD PRIMARY KEY (`id`);;");
        App::db()->executeQuery("ALTER TABLE `draft_picks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    }
}
