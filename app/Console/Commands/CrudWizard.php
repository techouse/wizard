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
     */
    public function handle(): void
    {
        $name = ucfirst(Str::camel($this->argument('name')));

        $this->model($name);
        $this->resource($name);
        $this->resources($name);
        $this->policy($name);
        $this->controller($name);

        // JS
        $this->jsModel($name);
        $this->jsModule($name);
        $this->jsRoute($name);
        if (!File::exists(resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name)))))) {
            File::makeDirectory(resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name)))));
        }
        $this->jsPageCreate($name);
        $this->jsPageEdit($name);
        $this->jsPageIndex($name);
        $this->jsPageList($name);

        $this->warn('Do not forget to register the routes in ' . app_path('routes/api.php') . ' and ' . resource_path('js/router/index.js'). '. You might also want to make a menu entry in ' . resource_path('js/pages/Admin.vue') . '.');
    }

    protected function getStub(string $type): string
    {
        return File::get(resource_path("stubs/$type.stub"));
    }

    protected function getStubJs(string $type): string
    {
        return File::get(resource_path("stubs/js/$type.stub"));
    }

    protected function getStubJsPage(string $type): string
    {
        return File::get(resource_path("stubs/js/Page/$type.stub"));
    }

    protected function model(string $name): void
    {
        $modelTemplate = str_replace(
            ['{{modelName}}'],
            [$name],
            $this->getStub('Model')
        );

        File::put(app_path(ucfirst(Str::camel($name)) . '.php'), $modelTemplate);

        $this->info('Generated file: ' . app_path(ucfirst(Str::camel($name)) . '.php'));
    }

    protected function controller(string $name): void
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

        File::put(app_path('Http/Controllers/Api/' . ucfirst(Str::camel($name)) . 'Controller.php'), $controllerTemplate);

        $this->info('Generated file: ' . app_path('Http/Controllers/Api/' . ucfirst(Str::camel($name)) . 'Controller.php'));
    }

    protected function policy(string $name): void
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

        File::put(app_path('Policies/' . ucfirst(Str::camel($name)) . 'Policy.php'), $policyTemplate);

        $this->info('Generated file: ' . app_path('Policies/' . ucfirst(Str::camel($name)) . 'Policy.php'));
    }

    protected function resource(string $name): void
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

        File::put(app_path('Http/Resources/' . ucfirst(Str::camel($name)) . 'Resource.php'), $resourceTemplate);

        $this->info('Generated file: ' . app_path('Http/Resources/' . ucfirst(Str::camel($name)) . 'Resource.php'));
    }

    protected function resources(string $name): void
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

        File::put(app_path('Http/Resources/' . ucfirst(Str::camel(Str::plural($name))) . 'Resource.php'), $resourcesTemplate);

        $this->info('Generated file: ' . app_path('Http/Resources/' . ucfirst(Str::camel(Str::plural($name))) . 'Resource.php'));
    }

    protected function jsModel(string $name): void
    {
        $template = str_replace(
            [
                '##modelName##'
            ],
            [
                $name
            ],
            $this->getStubJs('Model')
        );

        File::put(resource_path('js/models/' . ucfirst(Str::camel($name)) . '.js'), $template);

        $this->info('Generated file: ' . resource_path('js/models/' . ucfirst(Str::camel($name)) . '.js'));
    }

    protected function jsModule(string $name): void
    {
        $template = str_replace(
            [
                '##modelName##',
                '##modelNamePluralKebabCase##',
                '##modelNamePluralPascalCase##',
            ],
            [
                $name,
                Str::kebab(Str::plural($name)),
                ucfirst(Str::camel(Str::plural($name))),
            ],
            $this->getStubJs('Module')
        );

        File::put(resource_path('js/store/modules/' . Str::camel($name) . '.js'), $template);

        $this->info('Generated file: ' . resource_path('js/store/modules/' . Str::camel($name) . '.js'));
    }

    protected function jsRoute(string $name): void
    {
        $template = str_replace(
            [
                '##modelName##',
                '##modelNamePluralKebabCase##',
                '##modelNamePluralPascalCase##',
            ],
            [
                $name,
                Str::kebab(Str::plural($name)),
                ucfirst(Str::camel(Str::plural($name))),
            ],
            $this->getStubJs('Route')
        );

        File::put(resource_path('js/router/routes/' . Str::camel(Str::plural($name)) . '.js'), $template);

        $this->info('Generated file: ' . resource_path('js/router/routes/' . Str::camel(Str::plural($name)) . '.js'));
    }

    protected function jsPageCreate(string $name): void
    {
        $template = str_replace(
            [
                '##modelName##',
                '##modelNameLowerWithSpaces##',
                '##modelNameKebabCase##',
                '##modelNameCamelCase##',
            ],
            [
                $name,
                str_replace('_', ' ', Str::snake($name)),
                Str::kebab($name),
                Str::camel($name)
            ],
            $this->getStubJsPage('Create')
        );

        File::put(resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/create.vue'), $template);

        $this->info('Generated file: ' . resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/create.vue'));
    }

    protected function jsPageEdit(string $name): void
    {
        $template = str_replace(
            [
                '##modelName##',
                '##modelNameKebabCase##',
                '##modelNameCamelCase##',
                '##modelNameCapitalisedWithSpaces##',
                '##modelNamePluralPascalCase##',
            ],
            [
                $name,
                Str::kebab($name),
                Str::camel($name),
                ucfirst(str_replace('_', ' ', Str::snake($name))),
                ucfirst(Str::camel(Str::plural($name)))
            ],
            $this->getStubJsPage('Edit')
        );

        File::put(resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/edit.vue'), $template);

        $this->info('Generated file: ' . resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/edit.vue'));
    }

    protected function jsPageIndex(string $name): void
    {
        $template = str_replace(
            [
                '##modelNamePluralPascalCase##'
            ],
            [
                ucfirst(Str::camel(Str::plural($name)))
            ],
            $this->getStubJsPage('Index')
        );

        File::put(resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/index.vue'), $template);

        $this->info('Generated file: ' . resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/index.vue'));
    }

    protected function jsPageList(string $name): void
    {
        $template = str_replace(
            [
                '##modelName##',
                '##modelNameLowerWithSpaces##',
                '##modelNamePluralPascalCase##',
                '##modelNameCamelCase##',
            ],
            [
                $name,
                str_replace('_', ' ', Str::snake($name)),
                ucfirst(Str::camel(Str::plural($name))),
                Str::camel($name),
            ],
            $this->getStubJsPage('List')
        );

        File::put(resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/list.vue'), $template);

        $this->info('Generated file: ' . resource_path('js/pages/' . ucfirst(Str::camel(Str::plural($name))) . '/list.vue'));
    }
}
