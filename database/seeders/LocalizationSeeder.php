<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Database\Seeder;

class LocalizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create India
        $india = Country::create([
            'name' => 'India',
            'code' => 'IND',
            'phone_code' => '+91',
            'is_active' => true,
        ]);

        // Create Indian states
        $maharashtra = State::create([
            'name' => 'Maharashtra',
            'code' => 'MH',
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        $karnataka = State::create([
            'name' => 'Karnataka',
            'code' => 'KA',
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        $tamilNadu = State::create([
            'name' => 'Tamil Nadu',
            'code' => 'TN',
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        $gujarat = State::create([
            'name' => 'Gujarat',
            'code' => 'GJ',
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        $rajasthan = State::create([
            'name' => 'Rajasthan',
            'code' => 'RJ',
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        $delhi = State::create([
            'name' => 'Delhi',
            'code' => 'DL',
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        // Create Indian cities
        City::create([
            'name' => 'Mumbai',
            'pincode' => '400001',
            'state_id' => $maharashtra->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Pune',
            'pincode' => '411001',
            'state_id' => $maharashtra->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Nagpur',
            'pincode' => '440001',
            'state_id' => $maharashtra->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Bangalore',
            'pincode' => '560001',
            'state_id' => $karnataka->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Mysore',
            'pincode' => '570001',
            'state_id' => $karnataka->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Chennai',
            'pincode' => '600001',
            'state_id' => $tamilNadu->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Coimbatore',
            'pincode' => '641001',
            'state_id' => $tamilNadu->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Ahmedabad',
            'pincode' => '380001',
            'state_id' => $gujarat->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Surat',
            'pincode' => '395001',
            'state_id' => $gujarat->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Jaipur',
            'pincode' => '302001',
            'state_id' => $rajasthan->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Udaipur',
            'pincode' => '313001',
            'state_id' => $rajasthan->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'New Delhi',
            'pincode' => '110001',
            'state_id' => $delhi->id,
            'country_id' => $india->id,
            'is_active' => true,
        ]);

        // Create USA
        $usa = Country::create([
            'name' => 'United States',
            'code' => 'USA',
            'phone_code' => '+1',
            'is_active' => true,
        ]);

        $california = State::create([
            'name' => 'California',
            'code' => 'CA',
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        $texas = State::create([
            'name' => 'Texas',
            'code' => 'TX',
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        $florida = State::create([
            'name' => 'Florida',
            'code' => 'FL',
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        $newYork = State::create([
            'name' => 'New York',
            'code' => 'NY',
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        // Create US cities
        City::create([
            'name' => 'Los Angeles',
            'pincode' => '90210',
            'state_id' => $california->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'San Francisco',
            'pincode' => '94102',
            'state_id' => $california->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'San Diego',
            'pincode' => '92101',
            'state_id' => $california->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Houston',
            'pincode' => '77001',
            'state_id' => $texas->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Dallas',
            'pincode' => '75201',
            'state_id' => $texas->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Miami',
            'pincode' => '33101',
            'state_id' => $florida->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Orlando',
            'pincode' => '32801',
            'state_id' => $florida->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'New York City',
            'pincode' => '10001',
            'state_id' => $newYork->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Buffalo',
            'pincode' => '14201',
            'state_id' => $newYork->id,
            'country_id' => $usa->id,
            'is_active' => true,
        ]);

        // Create United Kingdom
        $uk = Country::create([
            'name' => 'United Kingdom',
            'code' => 'GBR',
            'phone_code' => '+44',
            'is_active' => true,
        ]);

        $england = State::create([
            'name' => 'England',
            'code' => 'ENG',
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        $scotland = State::create([
            'name' => 'Scotland',
            'code' => 'SCT',
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        $wales = State::create([
            'name' => 'Wales',
            'code' => 'WLS',
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        // Create UK cities
        City::create([
            'name' => 'London',
            'pincode' => 'SW1A 1AA',
            'state_id' => $england->id,
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Manchester',
            'pincode' => 'M1 1AA',
            'state_id' => $england->id,
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Birmingham',
            'pincode' => 'B1 1AA',
            'state_id' => $england->id,
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Edinburgh',
            'pincode' => 'EH1 1AA',
            'state_id' => $scotland->id,
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Glasgow',
            'pincode' => 'G1 1AA',
            'state_id' => $scotland->id,
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Cardiff',
            'pincode' => 'CF1 1AA',
            'state_id' => $wales->id,
            'country_id' => $uk->id,
            'is_active' => true,
        ]);

        // Create Canada
        $canada = Country::create([
            'name' => 'Canada',
            'code' => 'CAN',
            'phone_code' => '+1',
            'is_active' => true,
        ]);

        $ontario = State::create([
            'name' => 'Ontario',
            'code' => 'ON',
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        $britishColumbia = State::create([
            'name' => 'British Columbia',
            'code' => 'BC',
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        $quebec = State::create([
            'name' => 'Quebec',
            'code' => 'QC',
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        // Create Canadian cities
        City::create([
            'name' => 'Toronto',
            'pincode' => 'M5H 2N2',
            'state_id' => $ontario->id,
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Ottawa',
            'pincode' => 'K1A 0A6',
            'state_id' => $ontario->id,
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Vancouver',
            'pincode' => 'V6B 1A1',
            'state_id' => $britishColumbia->id,
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Victoria',
            'pincode' => 'V8W 1P1',
            'state_id' => $britishColumbia->id,
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Montreal',
            'pincode' => 'H1A 0A1',
            'state_id' => $quebec->id,
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Quebec City',
            'pincode' => 'G1A 0A1',
            'state_id' => $quebec->id,
            'country_id' => $canada->id,
            'is_active' => true,
        ]);

        // Create Australia
        $australia = Country::create([
            'name' => 'Australia',
            'code' => 'AUS',
            'phone_code' => '+61',
            'is_active' => true,
        ]);

        $newSouthWales = State::create([
            'name' => 'New South Wales',
            'code' => 'NSW',
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        $victoria = State::create([
            'name' => 'Victoria',
            'code' => 'VIC',
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        $queensland = State::create([
            'name' => 'Queensland',
            'code' => 'QLD',
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        // Create Australian cities
        City::create([
            'name' => 'Sydney',
            'pincode' => '2000',
            'state_id' => $newSouthWales->id,
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Newcastle',
            'pincode' => '2300',
            'state_id' => $newSouthWales->id,
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Melbourne',
            'pincode' => '3000',
            'state_id' => $victoria->id,
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Geelong',
            'pincode' => '3220',
            'state_id' => $victoria->id,
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Brisbane',
            'pincode' => '4000',
            'state_id' => $queensland->id,
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Gold Coast',
            'pincode' => '4217',
            'state_id' => $queensland->id,
            'country_id' => $australia->id,
            'is_active' => true,
        ]);

        // Create Germany
        $germany = Country::create([
            'name' => 'Germany',
            'code' => 'DEU',
            'phone_code' => '+49',
            'is_active' => true,
        ]);

        $bavaria = State::create([
            'name' => 'Bavaria',
            'code' => 'BY',
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        $northRhineWestphalia = State::create([
            'name' => 'North Rhine-Westphalia',
            'code' => 'NW',
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        $badenWurttemberg = State::create([
            'name' => 'Baden-Württemberg',
            'code' => 'BW',
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        // Create German cities
        City::create([
            'name' => 'Munich',
            'pincode' => '80331',
            'state_id' => $bavaria->id,
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Nuremberg',
            'pincode' => '90402',
            'state_id' => $bavaria->id,
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Cologne',
            'pincode' => '50667',
            'state_id' => $northRhineWestphalia->id,
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Düsseldorf',
            'pincode' => '40213',
            'state_id' => $northRhineWestphalia->id,
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Stuttgart',
            'pincode' => '70173',
            'state_id' => $badenWurttemberg->id,
            'country_id' => $germany->id,
            'is_active' => true,
        ]);

        City::create([
            'name' => 'Karlsruhe',
            'pincode' => '76133',
            'state_id' => $badenWurttemberg->id,
            'country_id' => $germany->id,
            'is_active' => true,
        ]);
    }
}
