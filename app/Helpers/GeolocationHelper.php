<?php

namespace App\Helpers;

class GeolocationHelper
{
    /**
     * Calculate distance between two coordinates using Haversine formula
     * Returns distance in kilometers
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2): float
    {
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return 0;
        }

        $earthRadius = 6371; // Earth radius in kilometers

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }

    /**
     * Calculate delivery cost based on distance and price per km
     */
    public static function calculateDeliveryCost(float $distance, ?float $pricePerKm): float
    {
        if ($pricePerKm === null || $pricePerKm <= 0) {
            return 0;
        }

        return round($distance * $pricePerKm, 2);
    }
}

