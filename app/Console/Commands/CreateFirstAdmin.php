<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateFirstAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer le premier administrateur du système';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===========================================');
        $this->info('   Création du premier administrateur');
        $this->info('===========================================');
        $this->newLine();

        // Demander les informations
        $firstName = $this->ask('Prénom');
        $lastName = $this->ask('Nom');
        $email = $this->ask('Email');
        $phone = $this->ask('Téléphone (optionnel)');
        $password = $this->secret('Mot de passe (min. 8 caractères)');
        $passwordConfirm = $this->secret('Confirmer le mot de passe');

        // Validation
        if ($password !== $passwordConfirm) {
            $this->error('❌ Les mots de passe ne correspondent pas !');
            return 1;
        }

        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password,
        ], [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error('❌ ' . $error);
            }
            return 1;
        }

        // Super admin ?
        $superAdmin = $this->confirm('Définir comme Super Administrateur ?', true);

        $this->newLine();
        $this->info('Création en cours...');

        try {
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone ?: null,
                'password' => Hash::make($password),
                'role' => 'admin',
                'super_admin' => $superAdmin,
                'is_active' => true,
            ]);

            $this->newLine();
            $this->info('✅ Administrateur créé avec succès !');
            $this->newLine();
            $this->table(
                ['Champ', 'Valeur'],
                [
                    ['ID', $user->id],
                    ['Nom complet', $user->full_name],
                    ['Email', $user->email],
                    ['Rôle', 'Administrateur'],
                    ['Super Admin', $superAdmin ? 'Oui' : 'Non'],
                ]
            );
            $this->newLine();
            $this->info('🔐 Vous pouvez maintenant vous connecter avec ces identifiants.');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la création : ' . $e->getMessage());
            return 1;
        }
    }
}