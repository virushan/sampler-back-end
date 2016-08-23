<?php

use Faker\Factory as Faker;
use App\Models\users;
use App\Repositories\usersRepository;

trait MakeUsersTrait
{
    /**
     * Create fake instance of users and save it in database
     *
     * @param array $usersFields
     * @return users
     */
    public function makeusers($usersFields = [])
    {
        /** @var usersRepository $usersRepo */
        $usersRepo = App::make(usersRepository::class);
        $theme = $this->fakeusersData($usersFields);
        return $usersRepo->create($theme);
    }

    /**
     * Get fake instance of users
     *
     * @param array $usersFields
     * @return users
     */
    public function fakeusers($usersFields = [])
    {
        return new users($this->fakeusersData($usersFields));
    }

    /**
     * Get fake data of users
     *
     * @param array $postFields
     * @return array
     */
    public function fakeusersData($usersFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'id' => $fake->word,
            'name' => $fake->word,
            'email' => $fake->word,
            'password' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $usersFields);
    }
}
