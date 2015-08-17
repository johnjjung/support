<?php

/**
 * @package     Support
 * @author      Ian Olson <me@ianolson.io>
 * @license     MIT
 * @copyright   2015, Ian Olson
 */

namespace IanOlson\Support\Commands;

use Illuminate\Console\Command;
use RuntimeException;

class GenerateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'iolson:model {classname} : The class name of the model.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates model with repository and interface.';

    /**
     * Class name of the model being generated.
     *
     * @var string
     */
    protected $classname;

    /**
     * Application path.
     *
     * @var string
     */
    protected $path;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Grab classname from the console command.
        $this->classname = $this->argument('classname');

        // Application path.
        $this->path = app_path();

        // Setup files.
        $this->setUpFiles();

        // Output success message.
        $this->info("{$this->classname} model, exception, repo and interface generated.");
    }

    /**
     * Setup files generated.
     */
    public function setUpFiles()
    {
        // Contract stub file & replacing content from the stub file.
        $contract = __DIR__ . '/stubs/Contract.stub';
        $contractContents = str_replace('{{classname}}', $this->classname, $this->laravel['files']->get($contract));

        // Create contracts directory.
        try {
            $this->laravel['files']->makeDirectory("{$this->path}/Contracts");
        } catch (\ErrorException $e) {
            // Directory already exists, don't need to recreate.
        }

        // Write new file.
        if ($this->laravel['files']->put(($contractFile = "{$this->path}/Contracts/{$this->classname}Interface.php"),
            $contractContents) === false
        ) {
            throw new RuntimeException("Could not write env file to [$contractFile].");
        }

        // Exception stub file & replacing content from the stub file.
        $exception = __DIR__ . '/stubs/Exception.stub';
        $exceptionContents = str_replace('{{classname}}', $this->classname, $this->laravel['files']->get($exception));

        // Write new file.
        if ($this->laravel['files']->put(($exceptionFile = "{$this->path}/Exceptions/{$this->classname}Exception.php"),
            $exceptionContents) === false
        ) {
            throw new RuntimeException("Could not write env file to [$exceptionFile].");
        }

        // Model stub file & replacing content from the stub file.
        $model = __DIR__ . '/stubs/Model.stub';
        $modelContents = str_replace('{{classname}}', $this->classname, $this->laravel['files']->get($model));

        // Create models directory.
        try {
            $this->laravel['files']->makeDirectory("{$this->path}/Models");
        } catch (\ErrorException $e) {
            // Directory already exists, don't need to recreate.
        }

        // Write new file.
        if ($this->laravel['files']->put(($modelFile = "{$this->path}/Models/{$this->classname}.php"),
            $modelContents) === false
        ) {
            throw new RuntimeException("Could not write env file to [$modelFile].");
        }

        // Repo stub file & replacing content from the stub file.
        $repo = __DIR__ . '/stubs/Repo.stub';
        $repoContents = str_replace('{{classname}}', $this->classname, $this->laravel['files']->get($repo));

        // Create repositories directory.
        try {
            $this->laravel['files']->makeDirectory("{$this->path}/Repositories");
        } catch (\ErrorException $e) {
            // Directory already exists, don't need to recreate.
        }

        // Write new file.
        if ($this->laravel['files']->put(($repoFile = "{$this->path}/Repositories/{$this->classname}Repo.php"),
            $repoContents) === false
        ) {
            throw new RuntimeException("Could not write env file to [$repoFile].");
        }
    }
}