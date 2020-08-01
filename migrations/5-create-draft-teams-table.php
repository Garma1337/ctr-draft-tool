<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to create the "draft_teams" table
 * @author Garma
 */
class Migration5 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("
            CREATE TABLE `draft_teams` (
              `id` int(11) NOT NULL,
              `draftId` int(11) NOT NULL,
              `teamName` varchar(50) NOT NULL,
              `accessKey` varchar(50) NOT NULL,
              `ready` tinyint(4) NOT NULL DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        App::db()->executeQuery("ALTER TABLE `draft_teams` ADD PRIMARY KEY (`id`);");
        App::db()->executeQuery("ALTER TABLE `draft_teams` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    }
}
