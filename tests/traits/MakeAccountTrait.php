<?php

use Faker\Factory as Faker;
use App\Models\Account;
use App\Repositories\AccountRepository;

trait MakeAccountTrait
{
    /**
     * Create fake instance of Account and save it in database
     *
     * @param array $accountFields
     * @return Account
     */
    public function makeAccount($accountFields = [])
    {
        /** @var AccountRepository $accountRepo */
        $accountRepo = App::make(AccountRepository::class);
        $theme = $this->fakeAccountData($accountFields);
        return $accountRepo->create($theme);
    }

    /**
     * Get fake instance of Account
     *
     * @param array $accountFields
     * @return Account
     */
    public function fakeAccount($accountFields = [])
    {
        return new Account($this->fakeAccountData($accountFields));
    }

    /**
     * Get fake data of Account
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAccountData($accountFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'id' => $fake->randomDigitNotNull,
            'user_email' => $fake->word,
            'balance' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $accountFields);
    }
}
