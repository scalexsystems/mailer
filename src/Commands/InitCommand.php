<?php namespace Scalex\Mailer\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure() {
        $this->setName('init')->setDescription('Install Mailer');
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output) {
        $root = realpath(__DIR__.'/../..');
        if (!file_exists($root.'/storage/database.sqlite')) {
            touch($root.'/storage/database.sqlite');
        }

        $files = $this->getMigrationFiles(realpath(__DIR__.'/../../migrations'));

        foreach ($files as $file) {
            $migration = $this->resolve($file);

            $migration->up();
        }

        $output->writeln('<info>Mailer Installed!</info>');
    }

    /**
     * Get all of the migration files in a given path.
     *
     * @param  string $path
     *
     * @return array
     */
    public function getMigrationFiles($path) {
        $files = glob($path.'/*_*.php');

        // Once we have the array of files in the directory we will just remove the
        // extension and take the basename of the file which is all we need when
        // finding the migrations that haven't been run against the databases.
        if ($files === false) {
            return [];
        }

        $files = array_map(
            function ($file) {
                return str_replace('.php', '', basename($file));
            },
            $files
        );

        // Once we have all of the formatted file names we will sort them and since
        // they all start with a timestamp this should give us the migrations in
        // the order they were actually created by the application developers.
        sort($files);

        return $files;
    }

    /**
     * Resolve a migration instance from a file.
     *
     * @param  string $file
     *
     * @return object
     */
    public function resolve($file) {
        $file = implode('_', array_slice(explode('_', $file), 4));

        $class = Str::studly($file);

        return new $class;
    }
}
