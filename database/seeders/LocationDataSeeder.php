<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\Block;

class LocationDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find existing countries or create them
        $india = Country::where('name', 'India')->first();
        if (!$india) {
            $india = Country::create([
                'name' => 'India',
                'code' => 'IN',
                'status' => true
            ]);
        }

        // Create Indian States
        $odisha = State::create([
            'name' => 'Odisha',
            'country_id' => $india->id,
            'code' => 'OR',
            'status' => true
        ]);

        $delhi = State::create([
            'name' => 'Delhi',
            'country_id' => $india->id,
            'code' => 'DL',
            'status' => true
        ]);

        $maharashtra = State::create([
            'name' => 'Maharashtra',
            'country_id' => $india->id,
            'code' => 'MH',
            'status' => true
        ]);

        $karnataka = State::create([
            'name' => 'Karnataka',
            'country_id' => $india->id,
            'code' => 'KA',
            'status' => true
        ]);

        $tamilNadu = State::create([
            'name' => 'Tamil Nadu',
            'country_id' => $india->id,
            'code' => 'TN',
            'status' => true
        ]);

        // Create Districts for Odisha
        $khordha = District::create([
            'name' => 'Khordha',
            'state_id' => $odisha->id,
            'status' => true
        ]);

        $cuttack = District::create([
            'name' => 'Cuttack',
            'state_id' => $odisha->id,
            'status' => true
        ]);

        $puri = District::create([
            'name' => 'Puri',
            'state_id' => $odisha->id,
            'status' => true
        ]);

        // Create Districts for Delhi
        $newDelhi = District::create([
            'name' => 'New Delhi',
            'state_id' => $delhi->id,
            'status' => true
        ]);

        $centralDelhi = District::create([
            'name' => 'Central Delhi',
            'state_id' => $delhi->id,
            'status' => true
        ]);

        // Create Districts for Maharashtra
        $pune = District::create([
            'name' => 'Pune',
            'state_id' => $maharashtra->id,
            'status' => true
        ]);

        $mumbai = District::create([
            'name' => 'Mumbai',
            'state_id' => $maharashtra->id,
            'status' => true
        ]);

        // Create Districts for Karnataka
        $bangaloreUrban = District::create([
            'name' => 'Bengaluru Urban',
            'state_id' => $karnataka->id,
            'status' => true
        ]);

        $mysore = District::create([
            'name' => 'Mysuru',
            'state_id' => $karnataka->id,
            'status' => true
        ]);

        // Create Districts for Tamil Nadu
        $chennai = District::create([
            'name' => 'Chennai',
            'state_id' => $tamilNadu->id,
            'status' => true
        ]);

        $coimbatore = District::create([
            'name' => 'Coimbatore',
            'state_id' => $tamilNadu->id,
            'status' => true
        ]);

        // Create Blocks for Khordha District
        $bhubaneswarBlock = Block::create([
            'name' => 'Bhubaneswar',
            'district_id' => $khordha->id,
            'status' => true
        ]);

        $patiaBlock = Block::create([
            'name' => 'Patia',
            'district_id' => $khordha->id,
            'status' => true
        ]);

        $chandrasekharpurBlock = Block::create([
            'name' => 'Chandrasekharpur',
            'district_id' => $khordha->id,
            'status' => true
        ]);

        // Create Blocks for Jatani (as block under Khordha)
        $jataniBlock = Block::create([
            'name' => 'Jatani',
            'district_id' => $khordha->id,
            'status' => true
        ]);

        // Create Blocks for New Delhi District
        $connaughtPlaceBlock = Block::create([
            'name' => 'Connaught Place',
            'district_id' => $newDelhi->id,
            'status' => true
        ]);

        $rajivChowkBlock = Block::create([
            'name' => 'Rajiv Chowk',
            'district_id' => $newDelhi->id,
            'status' => true
        ]);

        // Create Blocks for Pune District
        $puneCityBlock = Block::create([
            'name' => 'Pune City',
            'district_id' => $pune->id,
            'status' => true
        ]);

        $koregaonParkBlock = Block::create([
            'name' => 'Koregaon Park',
            'district_id' => $pune->id,
            'status' => true
        ]);

        // Create Blocks for Bengaluru Urban District
        $bangaloreNorthBlock = Block::create([
            'name' => 'Bangalore North',
            'district_id' => $bangaloreUrban->id,
            'status' => true
        ]);

        $koramangalaBlock = Block::create([
            'name' => 'Koramangala',
            'district_id' => $bangaloreUrban->id,
            'status' => true
        ]);

        // Create Blocks for Chennai District
        $annaNagarBlock = Block::create([
            'name' => 'Anna Nagar',
            'district_id' => $chennai->id,
            'status' => true
        ]);

        $mylaporeBlock = Block::create([
            'name' => 'Mylapore',
            'district_id' => $chennai->id,
            'status' => true
        ]);

        $this->command->info('Location data seeded successfully!');
    }
}
