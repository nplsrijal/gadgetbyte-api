<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SwaggerGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Swagger documentation and copy to public directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Generate Swagger documentation using l5-swagger:generate
        $this->info('Generating Swagger documentation...');
        Artisan::call('l5-swagger:generate');

        // Create the docs folder in public if it doesn't exist
        $docsFolder = public_path('docs');
        if (!File::exists($docsFolder)) {
            File::makeDirectory($docsFolder);
            $this->info('Created "docs" folder in public directory.');
        }

        $docsFolder = public_path('docs/asset');
        if (!File::exists($docsFolder)) {
            File::makeDirectory($docsFolder);
            $this->info('Created "asset" folder in public directory.');
        }

        // Copy contents from storage/api-docs/api-docs.json to public/docs/api-docs.json
        $sourcePath = storage_path('api-docs/api-docs.json');
        $destinationPath = public_path('docs/api-docs.json');

        // Ensure the source file exists
        if (File::exists($sourcePath)) {
            // Copy the file
            File::copy($sourcePath, $destinationPath);

            shell_exec('cp -r -L vendor/swagger-api/swagger-ui/dist/* public/docs/asset/');

            $this->info('Swagger documentation generated and copied successfully.');
        } else {
            $this->error('Swagger documentation not found. Make sure it has been generated.');
        }
    }
}