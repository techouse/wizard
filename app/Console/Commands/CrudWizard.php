<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CrudWizard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wizard:crud
                            {name : Class (singular) for example User}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create CRUD operations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucfirst(Str::camel($this->argument('name')));

        $this->model($name);
        $this->resource($name);
        $this->resources($name);
        $this->policy($name);
        $this->controller($name);

        File::append(base_path('routes/api.php'), 'Route::apiResource(\'' . Str::kebab(Str::plural($name)) . "', '{$name}Controller');");
    }

    protected function getStub($type)
    {
        return file_get_contents(resource_path("stubs/$type.stub"));
    }

    protected function model($name): void
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        file_put_contents(app_path('/' . ucfirst(Str::camel($name)) . '.php'), $modelTemplate);
    }

    protected function controller($name): void
    {
        $controllerTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralPascalCase}}',
                '{{modelNameSingularCamelCase}}',
                '{{modelNamePluralSnakeCase}}',
            ],
            [
                ucfirst(Str::camel($name)),
                ucfirst(Str::camel(Str::plural($name))),
                Str::camel($name),
                Str::snake(Str::plural($name)),
            ],
            $this->getStub('Controller')
        );

        file_put_contents(app_path('/Http/Controllers/Api/' . ucfirst(Str::camel($name)) . 'Controller.php'), $controllerTemplate);
    }

    protected function policy($name): void
    {
        $policyTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNameSingularCamelCase}}',
            ],
            [
                ucfirst(Str::camel($name)),
                Str::camel($name),
            ],
            $this->getStub('Policy')
        );

        file_put_contents(app_path('/Policies/' . ucfirst(Str::camel($name)) . 'Policy.php'), $policyTemplate);
    }

    protected function resource($name): void
    {
        $resourceTemplate = str_replace(
            [
                '{{modelName}}',
            ],
            [
                ucfirst(Str::camel($name)),
            ],
            $this->getStub('Resource')
        );

        file_put_contents(app_path('/Http/Resources/' . ucfirst(Str::camel($name)) . 'Resource.php'), $resourceTemplate);
    }

    protected function resources($name): void
    {
        $resourcesTemplate = str_replace(
            [
                '{{modelName}}',
                '{{modelNamePluralPascalCase}}',
                '{{modelNamePluralKebabCase}}',
            ],
            [
                ucfirst(Str::camel($name)),
                ucfirst(Str::camel(Str::plural($name))),
                Str::kebab(Str::plural($name)),
            ],
            $this->getStub('Resources')
        );

        file_put_contents(app_path('/Http/Resources/' . ucfirst(Str::camel(Str::plural($name))) . 'Resource.php'), $resourcesTemplate);
    }
}
