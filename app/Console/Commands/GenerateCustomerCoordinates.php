<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\CustomerCoordinate;
use Illuminate\Console\Command;

class GenerateCustomerCoordinates extends Command
{
    protected $signature = 'customers:generate-coordinates
                            {--base-lat=-8.145000 : Base latitude}
                            {--base-lng=112.285000 : Base longitude}
                            {--spacing=0.0025 : Spacing between customers (degrees)}
                            {--cols=4 : Number of columns in grid}';

    protected $description = 'Generate dummy coordinates for customers in Desa Jatitengah, Selopuro, Blitar';

    public function handle()
    {
        $baseLat = (float) $this->option('base-lat');
        $baseLng = (float) $this->option('base-lng');
        $spacing = (float) $this->option('spacing');
        $cols = (int) $this->option('cols');

        $customers = Customer::all();

        if ($customers->isEmpty()) {
            $this->error('No customers found.');
            return 1;
        }

        $this->info("Generating coordinates for {$customers->count()} customers...");
        $this->info("Location: Desa Jatitengah, Selopuro, Blitar");
        $this->info("Spacing: ~" . round($spacing * 111, 1) . " km (~" . round($spacing * 111000, 0) . " meters)");

        $row = 0;
        $col = 0;
        $created = 0;
        $updated = 0;

        $progressBar = $this->output->createProgressBar($customers->count());
        $progressBar->start();

        foreach ($customers as $customer) {
            // Calculate grid position
            $lat = $baseLat + ($row * $spacing);
            $lng = $baseLng + ($col * $spacing);

            // Add random variation (-50m to +50m)
            $lat += (mt_rand(-5, 5) / 10000);
            $lng += (mt_rand(-5, 5) / 10000);

            // Round to 7 decimal places
            $lat = round($lat, 7);
            $lng = round($lng, 7);

            // Check if coordinate already exists
            $exists = CustomerCoordinate::where('customer_id', $customer->id)->exists();

            // Create or update coordinate
            CustomerCoordinate::updateOrCreate(
                ['customer_id' => $customer->id],
                [
                    'tenant_id' => $customer->tenant_id,
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]
            );

            if ($exists) {
                $updated++;
            } else {
                $created++;
            }

            // Move to next grid position
            $col++;
            if ($col >= $cols) {
                $col = 0;
                $row++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ Coordinates generated successfully!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Total', $customers->count()],
            ]
        );

        $this->info("Coordinate range:");
        $this->line("  Latitude:  {$baseLat} to " . round($baseLat + ($row * $spacing), 4));
        $this->line("  Longitude: {$baseLng} to " . round($baseLng + ($cols * $spacing), 4));

        return 0;
    }
}
