<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer le dossier de stockage s'il n'existe pas
        if (!Storage::disk('public')->exists('centers')) {
            Storage::disk('public')->makeDirectory('centers');
        }

        // Copier une image par défaut
        $defaultImage = public_path('assets/img/default-logo.png');
        if (File::exists($defaultImage)) {
            Storage::disk('public')->put('centers/default-logo.png', File::get($defaultImage));
        }

        $centers = [
            [
                'name' => 'Centre de Formation Paris',
                'support_email' => 'support.paris@formation.fr',
                'support_contact' => '+33 1 23 45 67 89',
                'logo' => 'centers/default-logo.png',
                'tagline' => 'Excellence en éducation',
                'address' => '123 Rue de la Formation, 75001 Paris',
            ],
            [
                'name' => 'Centre de Formation Lyon',
                'support_email' => 'support.lyon@formation.fr',
                'support_contact' => '+33 4 56 78 90 12',
                'logo' => 'centers/default-logo.png',
                'tagline' => 'Votre avenir commence ici',
                'address' => '456 Avenue de l\'Éducation, 69001 Lyon',
            ],
            [
                'name' => 'Centre de Formation Marseille',
                'support_email' => 'support.marseille@formation.fr',
                'support_contact' => '+33 4 91 23 45 67',
                'logo' => 'centers/default-logo.png',
                'tagline' => 'Former pour réussir',
                'address' => '789 Boulevard du Savoir, 13001 Marseille',
            ],
        ];

        foreach ($centers as $centerData) {
            // Créer un utilisateur administrateur pour chaque centre
            $user = User::create([
                'name' => 'Admin ' . $centerData['name'],
                'email' => str_replace(' ', '.', strtolower($centerData['name'])) . '@admin.com',
                'password' => bcrypt('password'),
                'role_id' => 1, // Rôle administrateur
            ]);

            // Créer le centre
            $centerData['user_id'] = $user->id;
            Center::create($centerData);
        }
    }
} 