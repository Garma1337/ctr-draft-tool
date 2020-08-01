<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to create the "drafts" table
 * @author Garma
 */
class Migration2 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("
            CREATE TABLE `drafts` (
              `id` int(11) NOT NULL,
              `bans` int(11) NOT NULL,
              `picks` int(11) NOT NULL,
              `timeout` int(11) DEFAULT NULL,
              `enableSpyroCircuit` tinyint(4) NOT NULL,
              `enableHyperSpaceway` tinyint(4) NOT NULL,
              `enableRetroStadium` tinyint(4) NOT NULL,
              `splitTurboRetro` tinyint(4) NOT NULL,
              `allowTrackRepeats` tinyint(4) NOT NULL,
              `created` datetime NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        App::db()->executeQuery("ALTER TABLE `drafts` ADD PRIMARY KEY (`id`);");
        App::db()->executeQuery("ALTER TABLE `drafts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    }
}
