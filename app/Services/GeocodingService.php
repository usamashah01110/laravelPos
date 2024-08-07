<?php
namespace App\Services;

use GuzzleHttp\Client;

class GeocodingService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_MAPS_API_KEY'); // Make sure to set your API key in .env
    }

    public function getCoordinates($address)
    {
        $response = $this->client->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $address,
                'key' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        if ($data['status'] === 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            return [
                'latitude' => $location['lat'],
                'longitude' => $location['lng']
            ];
        }

        return null;
    }
}
