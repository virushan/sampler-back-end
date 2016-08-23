<?php

namespace App\Repositories;

use App\Models\users;
use InfyOm\Generator\Common\BaseRepository;

class usersRepository extends BaseRepository
{
    function __construct(\Illuminate\Container\Container $app, AccountRepository $accountRepo)
    {
        $this->accountRepo = $accountRepo;
        parent::__construct($app);
    }

    /**
     * @var array
     */
    protected $fieldSearchable = [
        'email',
        'first_name',
        'last_name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return users::class;
    }

    public function create(array $input){
        $user = $this->model->create($input);
        $this->accountRepo->create(['user_email' => $input['email'], 'balance' => 100.00]);
        return $user;
    }
}
