<?php

$baseLat = -8.145000;
$baseLng = 112.285000;
$spacing = 0.0025; // 0.0025 derajat = ~277 meter

$customers = App\Models\Customer::all();
$row = 0;
$col = 0;
$count = 0;

foreach ($customers as $c) {
    // Basic grid
    $lat = $baseLat + ($row * $spacing);
    $lng = $baseLng + ($col * $spacing);

    // Add small random noise (-50m to +50m) so it's not a perfect square
    $lat += (mt_rand(-5, 5) / 10000);
    $lng += (mt_rand(-5, 5) / 10000);

    App\Models\CustomerCoordinate::updateOrCreate(
        ['customer_id' => $c->id],
        [
            'tenant_id' => $c->tenant_id,
            'latitude' => round($lat, 7),
            'longitude' => round($lng, 7)
        ]
    );

    $col++;
    if ($col >= 4) { // 4 per row
        $col = 0;
        $row++;
    }
    $count++;
}

echo "OK! 18 Koordinat berhasil diset di Desa Jatitengah, Selopuro, Blitar.\n";
