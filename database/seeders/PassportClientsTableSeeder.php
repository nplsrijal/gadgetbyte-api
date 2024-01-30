<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;

class PassportClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clientRepository = app(ClientRepository::class);

        // Create a client
        $client = $clientRepository->create(
            null,
            'Myself',
            '',
            null,            
            false,
            false,
            true,
        );

        // Set the fixed client ID and client secret
        $client->forceFill([
            'id' => '9b377178-c4a6-44a5-882f-e742838fd5e8',
            'secret' => '6kHmkUg7nZwaPHrbO9M5BRS35iVQ9Tt2OONyP04F',
        ])->save();
    }
}