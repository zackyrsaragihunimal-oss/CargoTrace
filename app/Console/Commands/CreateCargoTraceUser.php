<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateCargoTraceUser extends Command
{
    protected $signature = 'cargotrace:create-user';

    protected $description = 'Create an Admin or Operator account securely';

    public function handle(): int
    {
        $name = trim($this->ask('Nama lengkap'));
        $email = strtolower(trim($this->ask('Email')));

        $role = $this->choice(
            'Pilih role akun',
            ['admin', 'operator']
        );

        $password = $this->secret('Masukkan password');
        $confirmation = $this->secret('Konfirmasi password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(12)->letters()->numbers(),
            ],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->role = $role;
        $user->is_active = true;
        $user->save();

        $this->newLine();
        $this->info('Akun CargoTrace berhasil dibuat!');
        $this->table(
            ['ID', 'Nama', 'Email', 'Role', 'Status'],
            [[
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                'Aktif',
            ]]
        );

        return self::SUCCESS;
    }
}
