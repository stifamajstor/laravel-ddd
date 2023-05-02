<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateDomainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:create {name : The name of the Domain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Domain with the required directories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $domainPath = app_path("Domain/{$name}");
        $infrastructurePath = app_path("Infrastructure/{$name}");

        // Create Bounded Context directories in the Domain folder
        $this->createDirectory("{$domainPath}/Models");
        $this->createDirectory("{$domainPath}/Http");
        $this->createDirectory("{$domainPath}/Http/Controllers");
        $this->createDirectory("{$domainPath}/Services");
        $this->createDirectory("{$domainPath}/Providers");
        $this->createDirectory("{$domainPath}/routes");

        // Create Bounded Context directories in the Infrastructure folder
        $this->createDirectory("{$infrastructurePath}");

        $this->info("Domain '{$name}' has been created.");

        // Generate ServiceProvider
        $stub = File::get(base_path('stubs/DomainServiceProvider.stub'));
        $stub = str_replace(['DummyNamespace', 'DummyServiceProvider'], ["App\\Domain\\{$name}", "{$name}ServiceProvider"], $stub);
        File::put("{$domainPath}/Providers/{$name}ServiceProvider.php", $stub);
        $this->line("Created ServiceProvider: <info>{$domainPath}/Providers/{$name}ServiceProvider.php</info>");

        // Generate RouteServiceProvider
        $routeServiceProviderStub = File::get(base_path('stubs/DomainRouteServiceProvider.stub'));
        $routeServiceProviderStub = str_replace(
            ['DummyNamespace', 'DummyRouteServiceProvider', 'DummyDomain'],
            ["App\\Domain\\{$name}", "{$name}RouteServiceProvider", $name],
            $routeServiceProviderStub
        );
        File::put("{$domainPath}/Providers/RouteServiceProvider.php", $routeServiceProviderStub);
        $this->line("Created RouteServiceProvider: <info>{$domainPath}/Providers/RouteServiceProvider.php</info>");
    }

    protected function createDirectory(string $path): void
    {
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
            $this->line("Created directory: <info>{$path}</info>");
        } else {
            $this->line("Directory already exists: <comment>{$path}</comment>");
        }
    }

}
