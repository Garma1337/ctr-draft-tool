<?php

declare(strict_types = 1);

namespace DraftTool\Commands;

use DraftTool\Lib\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to run migrations from CLI
 * @author Garma
 */
class MigrationRunCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('dt:migrations:run')
            ->setDescription('Runs pending migrations')
            ->addOption('preview', 'p', InputOption::VALUE_NONE)
        ;
    }
    
    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $preview = (bool) $input->getOption('preview');
        
        if ($preview) {
            $pendingMigrations = App::migrationManager()->getPendingMigrations();
            
            if (count($pendingMigrations) > 0) {
                $output->writeln('Pending migrations:');
                $output->writeln('######################');
                
                foreach ($pendingMigrations as $pendingMigrationId => $pendingMigration) {
                    $output->writeln($pendingMigrationId . '. ' . $pendingMigration['name']);
                }
            } else {
                $output->writeln('There are no pending migrations.');
            }
        } else {
            App::migrationManager()->runPendingMigrations();
            
            $output->writeln('All pending migrations have been executed successfully.');
        }
        
        return 0;
    }
}
