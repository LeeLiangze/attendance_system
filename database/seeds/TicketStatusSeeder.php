<?php

use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ticket_statuses = [
            [
                'id' => 1,
                'name' => 'Not Available',
            ],
            [
                'id' => 2,
                'name' => 'No Longer Available',
            ],
            [
                'id' => 3,
                'name' => 'Upcoming',
            ],
            [
                'id' => 4,
                'name' => 'Available',
            ],
            [
                'id' => 5,
                'name' => 'Available',
            ],
        ];

        DB::table('ticket_statuses')->insert($ticket_statuses);
    }
}
