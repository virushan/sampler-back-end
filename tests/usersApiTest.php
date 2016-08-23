<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class usersApiTest extends TestCase
{
    use MakeusersTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateusers()
    {
        $users = $this->fakeusersData();
        $this->json('POST', '/api/v1/users', $users);

        $this->assertApiResponse($users);
    }

    /**
     * @test
     */
    public function testReadusers()
    {
        $users = $this->makeusers();
        $this->json('GET', '/api/v1/users/'.$users->id);

        $this->assertApiResponse($users->toArray());
    }

    /**
     * @test
     */
    public function testUpdateusers()
    {
        $users = $this->makeusers();
        $editedusers = $this->fakeusersData();

        $this->json('PUT', '/api/v1/users/'.$users->id, $editedusers);

        $this->assertApiResponse($editedusers);
    }

    /**
     * @test
     */
    public function testDeleteusers()
    {
        $users = $this->makeusers();
        $this->json('DELETE', '/api/v1/users/'.$users->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/users/'.$users->id);

        $this->assertResponseStatus(404);
    }
}
