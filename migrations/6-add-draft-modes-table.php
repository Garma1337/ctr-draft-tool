<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to add the "draft_modes" table
 * @author Garma
 */
class Migration6 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("
            CREATE TABLE `draft_modes` (
              `id` int(11) NOT NULL,
              `uid` varchar(50) NOT NULL,
              `name` varchar(100) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        
        App::db()->executeQuery("
            INSERT INTO `draft_modes` (`id`, `uid`, `name`) VALUES
            (1, 'race', 'Race Mode'),
            (2, 'battle', 'Battle Mode');
        ");
        
        App::db()->executeQuery("ALTER TABLE `draft_modes` ADD PRIMARY KEY (`id`);");
        App::db()->executeQuery("ALTER TABLE `draft_modes` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;");
    }
}
