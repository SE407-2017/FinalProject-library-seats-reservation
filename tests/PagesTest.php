<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\User;
use Carbon\Carbon;

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

        // Test forbidden page
        $this->visit('/forbidden')
             ->see('Forbidden');

        $user = factory(App\User::class)->create();
        // Test home page
        $this->actingAs($user)
             ->visit('/reserve/home')
             ->see('图书馆预约');

        // Test QR generate page
        $this->visit('/test/qr')
             ->see('Floor');

        // Test reserve a seat
        $this->actingAs($user)
            ->post('/api/user/reservation/add', ['arrive_at' => Carbon::now(), 'floor_id' => 1, 'seat_id' => "2", 'table_id' => 1])
            ->see('true');

        // Check if the reservation has succeeded
        $this->actingAs($user)
            ->visit('/api/user/reservation/all')
            ->see(json_encode([1, '等待前往...']));

        // Test reserve a seat again
        $this->actingAs($user)
            ->post('/api/user/reservation/add', ['arrive_at' => Carbon::now(), 'floor_id' => 1, 'seat_id' => "3", 'table_id' => 1])
            ->see('false');

    }
}
