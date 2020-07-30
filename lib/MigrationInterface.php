<?php

declare(strict_types = 1);

namespace DraftTool\Lib;

/**
 * Interface for all migrations
 * @author Garma
 */
interface MigrationInterface
{
    /**
     * Runs the migration
     */
    public function run(): void;
}
