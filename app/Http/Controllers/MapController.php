<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

use Spatie\Geocoder\Geocoder;
use Illuminate\Support\Facades\Http;

class MapController extends Controller
{
    public function showMap(Request $request)
    {

        // $position = Location::get('127.0.0.1');

        // if ($position) {

        //     $lat = $position->latitude;
        //     $lng = $position->longitude;

        //     return response()->json([
        //         'lat' => $lat,
        //         'lng' => $lng
        //     ]);
        // }

        // return response()->json([
        //     'error' => 'Invalid or unreachable IP address'
        // ], 400);


        //     $address = 'Banasree Central Jame Masjid Market'; // ইনপুট হতে পারে

        // $client = new Client();
        // $geocoder = new Geocoder($client);
        // $geocoder->setApiKey(config('services.geocoder.api_key'));

        // $coordinates = $geocoder->getCoordinatesForAddress($address);

        // return response()->json([
        //     'lat' => $coordinates['lat'],
        //     'lng' => $coordinates['lng'],
        //     'google_map_link' => 'https://www.google.com/maps?q=' . $coordinates['lat'] . ',' . $coordinates['lng'],
        // ]);


        // $origin = '23.804093,90.4152376'; // ঢাকার একটি পয়েন্ট
        // $destination = '23.8103,90.4125'; // অন্য একটি পয়েন্ট

        // $apiKey = config('services.geocoder.api_key');

        // $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
        //     'origins' => $origin,
        //     'destinations' => $destination,
        //     'key' => $apiKey
        // ]);

        // $data = $response->json();

        // $distance = $data['rows'][0]['elements'][0]['distance']['text']; // যেমনঃ "1.2 km"
        // $duration = $data['rows'][0]['elements'][0]['duration']['text']; // যেমনঃ "5 mins"


        // $mapLink = 'https://www.google.com/maps/dir/' . $origin . '/' . $destination;

        // return response()->json([
        //     'distance' => $distance,
        //     'duration' => $duration,
        //     'map link' => $mapLink,
        // ]);


        // // process 1
        // $lat = 23.804093;
        // $lng = 90.4152376;

        // $apiKey = config('services.geocoder.api_key');

        // $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
        //     'latlng' => $lat . ',' . $lng,
        //     'key' => $apiKey
        // ]);

        // return $data = $response->json();

        // return $fullAddress = $data['results'][0]['locality'];

        // //process 2
        // $client = new Client();
        // $geocoder = new Geocoder($client);
        // $geocoder->setApiKey(config('services.geocoder.api_key'));

        // return $address = $geocoder->getAddressForCoordinates(23.804093, 90.4152376);



        // // process 3
        // $lat = 23.804093;
        // $lng = 90.4152376;

        // $apiKey = config('services.geocoder.api_key'); // .env থেকে api key নেবে

        // $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
        //     'latlng' => "{$lat},{$lng}",
        //     'key' => $apiKey,
        // ]);

        // $data = $response->json();

        // if (isset($data['results'][0])) {
        //     $result = $data['results'][0];

        //     return response()->json([
        //         'formatted_address' => $result['formatted_address'],
        //         'country' => collect($result['address_components'])->firstWhere('types', ['country'])['long_name'] ?? '',
        //         'locality' => collect($result['address_components'])->firstWhere('types', ['locality'])['long_name'] ?? '',
        //         'postal_code' => collect($result['address_components'])->firstWhere('types', ['postal_code'])['long_name'] ?? '',
        //     ]);
        // }

        // return response()->json(['message' => 'Location not found'], 404);



        // process 4
        $lat = 23.804093;
        $lng = 90.4152376;

        $apiKey = config('services.geocoder.api_key'); // .env থেকে api key নেবে

        $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
            'latlng' => "{$lat},{$lng}",
            'key' => $apiKey,
        ]);

        $data = $response->json();

        if (isset($data['results'][0])) {
            $result = $data['results'][0];

            $addressComponents = collect($result['address_components']);

            // Extracting components dynamically if they exist
            $country = $addressComponents->firstWhere('types', ['country'])['long_name'] ?? 'Country Not Found';
            $locality = $addressComponents->firstWhere('types', ['locality'])['long_name'] ?? 'Locality Not Found';
            $postalCode = $addressComponents->firstWhere('types', ['postal_code'])['long_name'] ?? 'Postal Code Not Found';

            return response()->json([
                'formatted_address' => $result['formatted_address'],
                'country' => $country,
                'locality' => $locality,
                'postal_code' => $postalCode,
            ]);
        }

        return response()->json(['message' => 'Location not found'], 404);
    }
}
