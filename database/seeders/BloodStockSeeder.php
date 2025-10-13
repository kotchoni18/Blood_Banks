<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BloodStock;


class BloodStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloodGroups = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        
        foreach ($bloodGroups as $group) {
            $quantity = rand(50, 300);
            $status = $quantity < 100 ? ($quantity < 50 ? 'critical' : 'low') : 'good';
            
            BloodStock::create([
                'blood_group' => $group,
                'quantity_units' => $quantity,
                'expiry_date' => now()->addDays(rand(7, 42)),
                'status' => $status,
                'location' => 'Frigo ' . ['A', 'B', 'C'][array_rand(['A', 'B', 'C'])] . '-' . rand(1, 4),
                'temperature' => rand(35, 42) / 10, // 3.5 to 4.2
                'collection_date' => now()->subDays(rand(1, 10)),
            ]);
        }
        //
    }
}
