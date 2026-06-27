<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:37              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 namespace Database\Factories; use App\Models\User; use Illuminate\Database\Eloquent\Factories\Factory; use Illuminate\Support\Facades\Hash; use Illuminate\Support\Str; class UserFactory extends Factory { protected static ?string $password; public function definition(): array { return ['name' => fake()->name(), 'email' => fake()->unique()->safeEmail(), 'email_verified_at' => now(), 'password' => static::$password ??= Hash::make('password'), 'remember_token' => Str::random(10)]; } public function unverified(): static { return $this->state(fn(array $B_5SF) => ['email_verified_at' => null]); } }
