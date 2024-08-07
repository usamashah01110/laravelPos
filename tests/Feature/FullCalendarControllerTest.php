<?php

namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Event;

class FullCalendarControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexMethod()
    {
        $events = [
            [
                'title' => 'Event 1',
                'start' => '2023-09-05',
                'end' => '2023-09-06',
            ],
            [
                'title' => 'Event 2',
                'start' => '2023-09-10',
                'end' => '2023-09-11',
            ],
        ];

        Event::insert($events);
        $response = $this->json('GET', '/api/v1/fullcalendar', [
            'start' => '2023-09-01',
            'end' => '2023-09-30',
        ]);
        $response->assertStatus(200);

    }

    public function testAjaxAddMethod()
    {
        $eventData = [
            'title' => 'New Event',
            'start' => '2023-09-15',
            'end' => '2023-09-16',
        ];
        $response = $this->json('POST', '/api/v1/fullcalendar-ajax', [
            'type' => 'add',
            'title' => $eventData['title'],
            'start' => $eventData['start'],
            'end' => $eventData['end'],
        ]);
        $response->assertStatus(200);
    }

    public function testAjaxUpdateMethod()
    {
        $event = Event::create([
            'title' => 'Existing Event',
            'start' => '2023-09-20',
            'end' => '2023-09-21',
        ]);

        $updatedEventData = [
            'id' => $event->id,
            'title' => 'Updated Event',
            'start' => '2023-09-22',
            'end' => '2023-09-23',
        ];
        $response = $this->json('POST', '/api/v1/fullcalendar-ajax', [
            'type' => 'update',
            'id' => $updatedEventData['id'],
            'title' => $updatedEventData['title'],
            'start' => $updatedEventData['start'],
            'end' => $updatedEventData['end'],
        ]);
        $response->assertStatus(200);

    }

    public function testAjaxDeleteMethod()
    {
        $event = Event::create([
            'title' => 'Event to Delete',
            'start' => '2023-09-25',
            'end' => '2023-09-26',
        ]);
        $response = $this->json('POST', '/api/v1/fullcalendar-ajax', [
            'type' => 'delete',
            'id' => $event->id,
        ]);
        $response->assertStatus(200);


    }
}
