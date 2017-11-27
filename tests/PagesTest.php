<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;

class PagesTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testPages()
    {
        $this->withoutMiddleware();

        $this->visit('/forbidden')
             ->see('Forbidden');
        $this->visit('/reserve/home')
             ->see('图书馆预约');
        $this->visit('/test/qr')
             ->see('Floor');
    }
}
