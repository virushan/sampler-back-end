<?php

use App\Models\users;
use App\Repositories\usersRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class usersRepositoryTest extends TestCase
{
    use MakeusersTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var usersRepository
     */
    protected $usersRepo;

    public function setUp()
    {
        parent::setUp();
        $this->usersRepo = App::make(usersRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateusers()
    {
        $users = $this->fakeusersData();
        $createdusers = $this->usersRepo->create($users);
        $createdusers = $createdusers->toArray();
        $this->assertArrayHasKey('id', $createdusers);
        $this->assertNotNull($createdusers['id'], 'Created users must have id specified');
        $this->assertNotNull(users::find($createdusers['id']), 'users with given id must be in DB');
        $this->assertModelData($users, $createdusers);
    }

    /**
     * @test read
     */
    public function testReadusers()
    {
        $users = $this->makeusers();
        $dbusers = $this->usersRepo->find($users->id);
        $dbusers = $dbusers->toArray();
        $this->assertModelData($users->toArray(), $dbusers);
    }

    /**
     * @test update
     */
    public function testUpdateusers()
    {
        $users = $this->makeusers();
        $fakeusers = $this->fakeusersData();
        $updatedusers = $this->usersRepo->update($fakeusers, $users->id);
        $this->assertModelData($fakeusers, $updatedusers->toArray());
        $dbusers = $this->usersRepo->find($users->id);
        $this->assertModelData($fakeusers, $dbusers->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteusers()
    {
        $users = $this->makeusers();
        $resp = $this->usersRepo->delete($users->id);
        $this->assertTrue($resp);
        $this->assertNull(users::find($users->id), 'users should not exist in DB');
    }
}
