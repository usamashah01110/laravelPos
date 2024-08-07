<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Repositories\BookingRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BookingSeeder extends Seeder
{
    protected $bookingRepository;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Collect IDs of services
        $serviceIds = Service::pluck('id')->toArray();

        // Define number of bookings to create
        $numberOfBookings = 50;

        // Seed bookings
        for ($i = 0; $i < $numberOfBookings; $i++) {
            $booking = [
                'service_id' => $serviceIds[array_rand($serviceIds)],
                'created_by' => rand(1, 10), // Adjust as necessary for existing user IDs
                'booking_date' => now()->addDays(rand(1, 30))->format('Y-m-d'), // Random date within next 30 days
                'booking_time' => now()->format('H:i:s'), // Current time
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->bookingRepository->create($booking);
        }

        Log::info('Bookings seeded successfully.');
    }
}
