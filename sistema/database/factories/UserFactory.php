<?php

namespace Database\Factories;

<<<<<<< Updated upstream
=======
use App\Models\Role;
>>>>>>> Stashed changes
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
<<<<<<< Updated upstream
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
=======
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'role_id' => Role::query()->value('id') ?? Role::create(['name' => 'Vendedor'])->id,
            'name' => fake()->name(),
            'dni' => fake()->unique()->numerify('########'),
            'phone' => fake()->numerify('9########'),
>>>>>>> Stashed changes
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

<<<<<<< Updated upstream
    /**
     * Indicate that the model's email address should be unverified.
     */
=======
>>>>>>> Stashed changes
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
