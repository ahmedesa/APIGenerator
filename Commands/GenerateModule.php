<?php

namespace essa\CrudGenerator\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class GenerateModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {model}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command generate api crud.';
    private $model;
    /**
     * Filesystem instance
     *
     * @var string
     */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();
        $this->filesystem = $filesystem;
    }

    /**
     */
    public function handle()
    {
        $this->model = ucfirst($this->argument('model'));

        $this->createFolders();

        $this->filesystem->append(base_path('routes/api.php'), $this->routeDefinition());

        $this->calls();

        $this->CreateFiles();
    }

    protected function createFolders($baseFolder = ''): void
    {
        if (!file_exists(app_path($baseFolder . "/Services"))) {
            $this->filesystem->makeDirectory(app_path($baseFolder . "/Services"));
        }
        if (!file_exists(app_path($baseFolder . "/Http/Controllers/API"))) {
            $this->filesystem->makeDirectory(app_path($baseFolder . "/Http/Controllers/API"));
        }
    }

    private function CreateFiles(): void
    {
        $this->createService();

        $this->createController();

        $this->createCollection();

        $this->createTest();
    }

    protected function createController()
    {
        file_put_contents(app_path("Http/Controllers/API/{$this->model}Controller.php"), $this->getTemplate('DummyController'));
    }

    protected function createTest()
    {
        file_put_contents(base_path("tests/Feature/{$this->model}Test.php"), $this->getTemplate('DummyTest'));
    }

    protected function createService()
    {
        file_put_contents(app_path("Services/{$this->model}Service.php"), $this->getTemplate('DummyService'));
    }

    protected function createCollection()
    {
        $pluralModel = Str::plural($this->model);

        file_put_contents(app_path("Http/Resources/{$pluralModel}/{$this->model}Collection.php"), $this->getTemplate('DummyResourceCollection'));
    }

    protected function getTemplate($type)
    {
        return str_replace(
            [
                'Dummy',
                'Dummies',
                'dummy',
                'dummies',
            ],
            [
                $this->model,
                Str::plural($this->model),
                lcfirst($this->model),
                lcfirst(Str::plural($this->model)),
            ],
            $this->getStubs($type)
        );
    }

    /**
     */
    private function calls(): void
    {
        $pluralModel = Str::plural($this->model);

        $this->call('make:model', [
            'name'        => $this->model,
            '--migration' => true,
            '--factory'   => true,
        ]);

        $this->call('make:seed', [
            'name' => $this->model . 'Seed',
        ]);

        $this->call('make:request', [
            'name' => $pluralModel . '\Create' . $this->model . 'Request',
        ]);

        $this->call('make:request', [
            'name' => $pluralModel . '\Update' . $this->model . 'Request',
        ]);

        $this->call('make:resource', [
            'name' => $pluralModel . '\\' . $this->model . 'Resource',
        ]);
    }

    protected static function getStubs($type)
    {
        return file_get_contents(resource_path("vendor/essa/Stubs/{$type}.stub"));
    }

    protected function routeDefinition()
    {
        return "
/*===========================
=               " . Str::plural(lcfirst($this->model)) . "       =
=============================*/

Route::group([
   'prefix' => '" . Str::plural(lcfirst($this->model)) . "',
], function() {
    Route::get('/', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'index']);
    Route::get('/{" . lcfirst($this->model) . "}', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'show']);

    Route::post('/', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'store']);
    Route::put('/{" . lcfirst($this->model) . "}', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'update']);

    Route::delete('/{" . lcfirst($this->model) . "}', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'destroy']);
    Route::get('{id}/restore', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'restore']);
    Route::delete('{id}/permanent-delete', [\App\Http\Controllers\API\\" . $this->model . "Controller::class, 'permanentDelete']);
});
/*=====  End of " . Str::plural(lcfirst($this->model)) . "   ======*/
   ";
    }
}
