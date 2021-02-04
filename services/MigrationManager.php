<?php

declare(strict_types = 1);

namespace DraftTool\Services;

use Carbon\Carbon;
use DraftTool\Lib\App;
use DraftTool\Lib\FileSystem;
use DraftTool\Lib\MigrationInterface;

/**
 * Migration Manager
 * @author Garma
 */
class MigrationManager
{
    /**
     * @var static
     */
    protected string $directory;
    
    /**
     * @param string $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;
        
        $query = 'CREATE TABLE IF NOT EXISTS `schema_version` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `created` datetime NOT NULL,
                     PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;';
        
        App::db()->executeQuery($query);
    }
    
    /**
     * Gets a list of all pending migrations
     * @return array
     */
    public function getPendingMigrations(): array
    {
        $pendingMigrations = [];
        $migrationFiles = FileSystem::getFilesInDirectory($this->directory);
        
        foreach ($migrationFiles as $migrationFile) {
            $splittedFileName = explode('.', $migrationFile);
            
            $splittedMigrationName = explode('-', $splittedFileName[0]);
            $migrationId = (int) array_shift($splittedMigrationName);
            $migrationName = implode('-', $splittedMigrationName);
            
            if (!$this->isMigrationUp($migrationId)) {
                $pendingMigrations[$migrationId] = [
                    'file' => $migrationFile,
                    'name' => $migrationName
                ];
            }
        }
        
        return $pendingMigrations;
    }
    
    /**
     * Executes all pending migrations
     */
    public function runPendingMigrations(): void
    {
        $pendingMigrations = $this->getPendingMigrations();
        
        foreach ($pendingMigrations as $pendingMigrationId => $pendingMigration) {
            require_once $this->directory . $pendingMigrationId . '-' . $pendingMigration['name'] . '.php';
            
            $migrationClassName = 'Migration' . $pendingMigrationId;
            
            /** @var MigrationInterface $migrationClass */
            $migrationClass = new $migrationClassName();
            $migrationClass->run();
            
            App::db()->insert('schema_version', [
                'name'      => $pendingMigration['name'],
                'created'   => Carbon::now()->toDateTimeString()
            ]);
        }
    }
    
    /**
     * @param int $migrationId
     * @return bool
     */
    public function isMigrationUp(int $migrationId): bool
    {
        $query = 'SELECT *
                  FROM schema_version
                  WHERE id = ?';
        
        $migration = App::db()->executeQuery($query, [$migrationId])->fetch();
        
        return ($migration !== false);
    }
}
