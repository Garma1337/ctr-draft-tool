<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to create the "tracks" table
 * @author Garma
 */
class Migration1 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("
            CREATE TABLE `tracks` (
              `id` int(11) NOT NULL,
              `name` varchar(50) NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        App::db()->executeQuery("
            INSERT INTO `tracks` (`id`, `name`) VALUES
                (1, 'Crash Cove'),
                (2, 'Mystery Caves'),
                (3, 'Sewer Speedway'),
                (4, 'Roo\'s Tubes'),
                (5, 'Slide Coliseum'),
                (6, 'Turbo Track'),
                (7, 'Coco Park'),
                (8, 'Tiger Temple'),
                (9, 'Papu\'s Pyramid'),
                (10, 'Dingo Canyon'),
                (11, 'Polar Pass'),
                (12, 'Tiny Arena'),
                (13, 'Dragon Mines'),
                (14, 'Blizzard Bluff'),
                (15, 'Hot Air Skyway'),
                (16, 'Cortex Castle'),
                (17, 'N. Gin Labs'),
                (18, 'Oxide Station'),
                (19, 'Inferno Island'),
                (20, 'Jungle Boogie'),
                (21, 'Clockwork Wumpa'),
                (22, 'Android Alley'),
                (23, 'Electron Avenue'),
                (24, 'Deep Sea Driving'),
                (25, 'Thunder Struck'),
                (26, 'Tiny Temple'),
                (27, 'Meteor Gorge'),
                (28, 'Barin Ruins'),
                (29, 'Out of Time'),
                (30, 'Assembly Lane'),
                (31, 'Hyper Spaceway'),
                (32, 'Twilight Tour'),
                (33, 'Prehistoric Playground'),
                (34, 'Spyro Circuit'),
                (35, 'Nina\'s Nightmare'),
                (36, 'Koala Carnival'),
                (37, 'Gingerbread Joyride'),
                (38, 'Megamix Mania'),
                (39, 'Drive-Thru Danger'),
                (40, 'Retro Stadium');
        ");
        
        App::db()->executeQuery('ALTER TABLE `tracks` ADD PRIMARY KEY (`id`);');
        App::db()->executeQuery('ALTER TABLE `tracks` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;');
    }
}
