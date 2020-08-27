<?php

declare(strict_types = 1);

use DraftTool\Lib\App;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration to add the battle arenas to the "tracks" table
 * @author Garma
 */
class Migration9 implements MigrationInterface
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        App::db()->executeQuery("
            INSERT INTO `tracks` (`id`, `name`, `mode`) VALUES
            (41, 'Skull Rock', 2),
            (42, 'Rampage Ruins', 2),
            (43, 'Rocky Road', 2),
            (44, 'Nitro Court', 2),
            (45, 'Parking Lot', 2),
            (46, 'The North Bowl', 2),
            (47, 'Lab Basement', 2),
            (48, 'Temple Turmoil', 2),
            (49, 'Frozen Frenzy', 2),
            (50, 'Desert Storm', 2),
            (51, 'Magnetic Mayhem', 2),
            (52, 'Terra Drome', 2);
        ");
    }
}
