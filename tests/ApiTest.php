<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class ApiTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testApis()
    {
        $this->withoutMiddleware();

        $this->visit('/api/floors/get')
            ->see('true');

        $this->visit('/api/table/1/detail')
            ->see('avail_seats');


    }
}
