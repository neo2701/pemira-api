<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ChangeUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "user:change-password {email? : The email of the user}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change a user\'s password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get email from argument or ask for it
        $email = $this->argument("email") ?: $this->ask("Enter the user email");

        if (empty($email)) {
            $this->error("Email is required!");
            return Command::FAILURE;
        }

        // Validate email format
        $emailValidator = Validator::make(
            ["email" => $email],
            ["email" => "required|email"],
        );

        if ($emailValidator->fails()) {
            $this->error("Invalid email format!");
            return Command::FAILURE;
        }

        // Find the user by email
        $user = User::where("email", $email)->first();

        if (!$user) {
            $this->error("User with email '{$email}' not found!");
            return Command::FAILURE;
        }

        // Display user information
        $this->info("User found:");
        $this->table(
            ["NPM", "Name", "Email", "Role"],
            [[$user->npm, $user->name, $user->email, $user->role]],
        );

        // Confirm the user wants to continue
        if (
            !$this->confirm(
                "Do you want to change the password for this user?",
                true,
            )
        ) {
            $this->info("Operation cancelled.");
            return Command::SUCCESS;
        }

        // Ask for new password
        $password = $this->secret("Enter new password");

        if (empty($password)) {
            $this->error("Password cannot be empty!");
            return Command::FAILURE;
        }

        // Validate password length
        $validator = Validator::make(
            ["password" => $password],
            ["password" => "required|min:8"],
        );

        if ($validator->fails()) {
            $this->error("Password must be at least 8 characters long!");
            return Command::FAILURE;
        }

        // Ask for password confirmation
        $passwordConfirmation = $this->secret("Confirm new password");

        if ($password !== $passwordConfirmation) {
            $this->error("Passwords do not match!");
            return Command::FAILURE;
        }

        // Update the password
        try {
            $user->password = Hash::make($password);
            $user->save();

            $this->info(
                "✓ Password successfully changed for user: " . $user->name,
            );
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to update password: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
