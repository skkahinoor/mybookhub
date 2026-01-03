<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
// use App\Models\City;
use App\Models\Block;

class CompleteIndiaLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data in correct order due to foreign key constraints
        Block::query()->delete();
        // City::query()->delete();
        District::query()->delete();
        State::query()->delete();

        // Find or create India
        $india = Country::where('name', 'India')->first();
        if (!$india) {
            $india = Country::create([
                'name' => 'India',
                'code' => 'IN',
                'status' => true
            ]);
        }

        // Create all Indian States and Union Territories
        $states = [
            // States
            ['name' => 'Andhra Pradesh', 'code' => 'AP'],
            ['name' => 'Arunachal Pradesh', 'code' => 'AR'],
            ['name' => 'Assam', 'code' => 'AS'],
            ['name' => 'Bihar', 'code' => 'BR'],
            ['name' => 'Chhattisgarh', 'code' => 'CG'],
            ['name' => 'Goa', 'code' => 'GA'],
            ['name' => 'Gujarat', 'code' => 'GJ'],
            ['name' => 'Haryana', 'code' => 'HR'],
            ['name' => 'Himachal Pradesh', 'code' => 'HP'],
            ['name' => 'Jharkhand', 'code' => 'JH'],
            ['name' => 'Karnataka', 'code' => 'KA'],
            ['name' => 'Kerala', 'code' => 'KL'],
            ['name' => 'Madhya Pradesh', 'code' => 'MP'],
            ['name' => 'Maharashtra', 'code' => 'MH'],
            ['name' => 'Manipur', 'code' => 'MN'],
            ['name' => 'Meghalaya', 'code' => 'ML'],
            ['name' => 'Mizoram', 'code' => 'MZ'],
            ['name' => 'Nagaland', 'code' => 'NL'],
            ['name' => 'Odisha', 'code' => 'OR'],
            ['name' => 'Punjab', 'code' => 'PB'],
            ['name' => 'Rajasthan', 'code' => 'RJ'],
            ['name' => 'Sikkim', 'code' => 'SK'],
            ['name' => 'Tamil Nadu', 'code' => 'TN'],
            ['name' => 'Telangana', 'code' => 'TG'],
            ['name' => 'Tripura', 'code' => 'TR'],
            ['name' => 'Uttar Pradesh', 'code' => 'UP'],
            ['name' => 'Uttarakhand', 'code' => 'UK'],
            ['name' => 'West Bengal', 'code' => 'WB'],

            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN'],
            ['name' => 'Chandigarh', 'code' => 'CH'],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DH'],
            ['name' => 'Delhi', 'code' => 'DL'],
            ['name' => 'Jammu and Kashmir', 'code' => 'JK'],
            ['name' => 'Ladakh', 'code' => 'LA'],
            ['name' => 'Lakshadweep', 'code' => 'LD'],
            ['name' => 'Puducherry', 'code' => 'PY']
        ];

        foreach ($states as $stateData) {
            State::create([
                'name' => $stateData['name'],
                'country_id' => $india->id,
                'code' => $stateData['code'],
                'status' => true
            ]);
        }

        // Get all states for district creation
        $allStates = State::all()->keyBy('name');

        // Create Districts for Major States
        $this->createOdishaDistricts($allStates['Odisha']);
        $this->createDelhiDistricts($allStates['Delhi']);
        $this->createMaharashtraDistricts($allStates['Maharashtra']);
        $this->createKarnatakaDistricts($allStates['Karnataka']);
        $this->createTamilNaduDistricts($allStates['Tamil Nadu']);
        $this->createGujaratDistricts($allStates['Gujarat']);
        $this->createUttarPradeshDistricts($allStates['Uttar Pradesh']);
        $this->createWestBengalDistricts($allStates['West Bengal']);
        $this->createKeralaDistricts($allStates['Kerala']);
        $this->createPunjabDistricts($allStates['Punjab']);
        $this->createHaryanaDistricts($allStates['Haryana']);
        $this->createRajasthanDistricts($allStates['Rajasthan']);
        $this->createMadhyaPradeshDistricts($allStates['Madhya Pradesh']);
        $this->createAndhraPradeshDistricts($allStates['Andhra Pradesh']);
        $this->createTelanganaDistricts($allStates['Telangana']);
        $this->createBiharDistricts($allStates['Bihar']);
        $this->createJharkhandDistricts($allStates['Jharkhand']);
        $this->createChhattisgarhDistricts($allStates['Chhattisgarh']);
        $this->createAssamDistricts($allStates['Assam']);
        $this->createHimachalPradeshDistricts($allStates['Himachal Pradesh']);
        $this->createUttarakhandDistricts($allStates['Uttarakhand']);
        $this->createGoaDistricts($allStates['Goa']);
        $this->createManipurDistricts($allStates['Manipur']);
        $this->createMeghalayaDistricts($allStates['Meghalaya']);
        $this->createMizoramDistricts($allStates['Mizoram']);
        $this->createNagalandDistricts($allStates['Nagaland']);
        $this->createTripuraDistricts($allStates['Tripura']);
        $this->createSikkimDistricts($allStates['Sikkim']);
        $this->createArunachalPradeshDistricts($allStates['Arunachal Pradesh']);

        $this->command->info('Complete India location data seeded successfully!');
        $this->command->info('States: ' . State::count());
        $this->command->info('Districts: ' . District::count());
        // $this->command->info('Cities: ' . City::count());
        $this->command->info('Blocks: ' . Block::count());
    }

    private function createOdishaDistricts($state)
    {
        $districts = [
            'Angul', 'Balangir', 'Balasore', 'Bargarh', 'Bhadrak', 'Boudh', 'Cuttack',
            'Deogarh', 'Dhenkanal', 'Gajapati', 'Ganjam', 'Jagatsinghpur', 'Jajpur',
            'Jharsuguda', 'Kalahandi', 'Kandhamal', 'Kendrapara', 'Kendujhar', 'Khordha',
            'Koraput', 'Malkangiri', 'Mayurbhanj', 'Nabarangpur', 'Nayagarh', 'Nuapada',
            'Puri', 'Rayagada', 'Sambalpur', 'Subarnapur', 'Sundargarh'
        ];

        foreach ($districts as $districtName) {
            $district = District::create([
                'name' => $districtName,
                'state_id' => $state->id,
                'status' => true
            ]);

            // Create blocks for key districts
            if ($districtName === 'Khordha') {
                $this->createKhordhaBlocks($district);
            } elseif ($districtName === 'Cuttack') {
                $this->createCuttackBlocks($district);
            } elseif ($districtName === 'Puri') {
                $this->createPuriBlocks($district);
            } 
            else {
                // Default block for other districts
                Block::create([
                    'name' => $districtName . ' Main',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createKhordhaBlocks($district)
    {
        // Detailed blocks for Bhubaneswar plus defaults for nearby towns
        $blocks = ['Bhubaneswar Central', 'Patia', 'Chandrasekharpur', 'Khandagiri', 'Nayapalli', 'Acharya Vihar', 'Saheed Nagar'];
        foreach ($blocks as $blockName) {
            Block::create([
                'name' => $blockName,
                'district_id' => $district->id,
                'status' => true
            ]);
        }
        foreach (['Jatani', 'Khordha'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createCuttackBlocks($district)
    {
        foreach (['Cuttack', 'Choudwar', 'Athagad'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createPuriBlocks($district)
    {
        foreach (['Puri', 'Konark', 'Brahmagiri'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createDelhiDistricts($state)
    {
        $districts = [
            'Central Delhi', 'East Delhi', 'New Delhi', 'North Delhi', 'North East Delhi',
            'North West Delhi', 'Shahdara', 'South Delhi', 'South East Delhi', 'South West Delhi', 'West Delhi'
        ];

        foreach ($districts as $districtName) {
            $district = District::create([
                'name' => $districtName,
                'state_id' => $state->id,
                'status' => true
            ]);

            if ($districtName === 'New Delhi') {
                $this->createNewDelhiBlocks($district);
            } elseif ($districtName === 'Central Delhi') {
                $this->createCentralDelhiBlocks($district);
            } else {
                Block::create([
                    'name' => $districtName . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createNewDelhiBlocks($district)
    {
        foreach (['New Delhi', 'Connaught Place', 'Rajiv Chowk'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createCentralDelhiBlocks($district)
    {
        foreach (['Karol Bagh', 'Daryaganj', 'Paharganj'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createMaharashtraDistricts($state)
    {
        $districts = [
            'Ahmednagar', 'Akola', 'Amravati', 'Aurangabad', 'Beed', 'Bhandara', 'Buldhana',
            'Chandrapur', 'Dhule', 'Gadchiroli', 'Gondia', 'Hingoli', 'Jalgaon', 'Jalna',
            'Kolhapur', 'Latur', 'Mumbai City', 'Mumbai Suburban', 'Nagpur', 'Nanded',
            'Nandurbar', 'Nashik', 'Osmanabad', 'Palghar', 'Parbhani', 'Pune', 'Raigad',
            'Ratnagiri', 'Sangli', 'Satara', 'Sindhudurg', 'Solapur', 'Thane', 'Wardha',
            'Washim', 'Yavatmal'
        ];

        foreach ($districts as $districtName) {
            $district = District::create([
                'name' => $districtName,
                'state_id' => $state->id,
                'status' => true
            ]);

            if ($districtName === 'Pune') {
                $this->createPuneBlocks($district);
            } elseif ($districtName === 'Mumbai City') {
                $this->createMumbaiBlocks($district);
            } elseif ($districtName === 'Nagpur') {
                $this->createNagpurBlocks($district);
            } else {
                Block::create([
                    'name' => $districtName . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createPuneBlocks($district)
    {
        foreach (['Pune', 'Pimpri-Chinchwad', 'Baramati'] as $name) {
            if ($name === 'Pune') {
                $blocks = ['Pune City', 'Koregaon Park', 'Shivajinagar', 'Baner', 'Hinjewadi', 'Kothrud'];
                foreach ($blocks as $blockName) {
                    Block::create([
                        'name' => $blockName,
                        'district_id' => $district->id,
                        'status' => true
                    ]);
                }
            } else {
                Block::create([
                    'name' => $name . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createMumbaiBlocks($district)
    {
        foreach (['Mumbai', 'Dharavi', 'Bandra'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createNagpurBlocks($district)
    {
        foreach (['Nagpur', 'Kamptee', 'Katol'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createKarnatakaDistricts($state)
    {
        $districts = [
            'Bagalkot', 'Ballari', 'Belagavi', 'Bengaluru Rural', 'Bengaluru Urban',
            'Bidar', 'Chamarajanagar', 'Chikballapur', 'Chikkamagaluru', 'Chitradurga',
            'Dakshina Kannada', 'Davangere', 'Dharwad', 'Gadag', 'Hassan', 'Haveri',
            'Kalaburagi', 'Kodagu', 'Kolar', 'Koppal', 'Mandya', 'Mysuru', 'Raichur',
            'Ramanagara', 'Shivamogga', 'Tumakuru', 'Udupi', 'Uttara Kannada', 'Vijayapura', 'Yadgir'
        ];

        foreach ($districts as $districtName) {
            $district = District::create([
                'name' => $districtName,
                'state_id' => $state->id,
                'status' => true
            ]);

            if ($districtName === 'Bengaluru Urban') {
                $this->createBangaloreBlocks($district);
            } elseif ($districtName === 'Mysuru') {
                $this->createMysoreBlocks($district);
            } else {
                Block::create([
                    'name' => $districtName . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createBangaloreBlocks($district)
    {
        foreach (['Bangalore', 'Whitefield', 'Electronic City'] as $name) {
            if ($name === 'Bangalore') {
                $blocks = ['Bangalore North', 'Koramangala', 'Indiranagar', 'Hebbal', 'Yelahanka'];
                foreach ($blocks as $blockName) {
                    Block::create([
                        'name' => $blockName,
                        'district_id' => $district->id,
                        'status' => true
                    ]);
                }
            } else {
                Block::create([
                    'name' => $name . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createMysoreBlocks($district)
    {
        foreach (['Mysore', 'Srirangapatna', 'Nanjangud'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    private function createTamilNaduDistricts($state)
    {
        $districts = [
            'Ariyalur', 'Chengalpattu', 'Chennai', 'Coimbatore', 'Cuddalore', 'Dharmapuri',
            'Dindigul', 'Erode', 'Kallakurichi', 'Kancheepuram', 'Karur', 'Krishnagiri',
            'Madurai', 'Mayiladuthurai', 'Nagapattinam', 'Namakkal', 'Nilgiris', 'Perambalur',
            'Pudukkottai', 'Ramanathapuram', 'Ranipet', 'Salem', 'Sivaganga', 'Tenkasi',
            'Thanjavur', 'Theni', 'Thoothukudi', 'Tiruchirappalli', 'Tirunelveli', 'Tirupathur',
            'Tiruppur', 'Tiruvallur', 'Tiruvannamalai', 'Tiruvarur', 'Vellore', 'Villupuram', 'Virudhunagar'
        ];

        foreach ($districts as $districtName) {
            $district = District::create([
                'name' => $districtName,
                'state_id' => $state->id,
                'status' => true
            ]);

            if ($districtName === 'Chennai') {
                $this->createChennaiBlocks($district);
            } elseif ($districtName === 'Coimbatore') {
                $this->createCoimbatoreBlocks($district);
            } else {
                Block::create([
                    'name' => $districtName . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createChennaiBlocks($district)
    {
        foreach (['Chennai', 'Tambaram', 'Ambattur'] as $name) {
            if ($name === 'Chennai') {
                $blocks = ['Anna Nagar', 'Mylapore', 'T. Nagar', 'Adyar', 'Velachery'];
                foreach ($blocks as $blockName) {
                    Block::create([
                        'name' => $blockName,
                        'district_id' => $district->id,
                        'status' => true
                    ]);
                }
            } else {
                Block::create([
                    'name' => $name . ' Block',
                    'district_id' => $district->id,
                    'status' => true
                ]);
            }
        }
    }

    private function createCoimbatoreBlocks($district)
    {
        foreach (['Coimbatore', 'Pollachi', 'Tiruppur'] as $name) {
            Block::create([
                'name' => $name . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }

    // Additional state methods (simplified for brevity)
    private function createGujaratDistricts($state)
    {
        $districts = ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createUttarPradeshDistricts($state)
    {
        $districts = ['Lucknow', 'Kanpur', 'Agra', 'Varanasi', 'Meerut', 'Ghaziabad', 'Noida'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createWestBengalDistricts($state)
    {
        $districts = ['Kolkata', 'Howrah', 'Burdwan', 'Hooghly', 'North 24 Parganas', 'South 24 Parganas'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createKeralaDistricts($state)
    {
        $districts = ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Palakkad', 'Malappuram'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createPunjabDistricts($state)
    {
        $districts = ['Amritsar', 'Ludhiana', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createHaryanaDistricts($state)
    {
        $districts = ['Gurgaon', 'Faridabad', 'Panipat', 'Karnal', 'Hisar', 'Rohtak'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createRajasthanDistricts($state)
    {
        $districts = ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Bikaner', 'Ajmer'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createMadhyaPradeshDistricts($state)
    {
        $districts = ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain', 'Sagar'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createAndhraPradeshDistricts($state)
    {
        $districts = ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Tirupati'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createTelanganaDistricts($state)
    {
        $districts = ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Khammam', 'Mahbubnagar'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createBiharDistricts($state)
    {
        $districts = ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Darbhanga', 'Purnia'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createJharkhandDistricts($state)
    {
        $districts = ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Deoghar', 'Hazaribagh'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createChhattisgarhDistricts($state)
    {
        $districts = ['Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Rajnandgaon', 'Durg'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createAssamDistricts($state)
    {
        $districts = ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Tezpur', 'Nagaon'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createHimachalPradeshDistricts($state)
    {
        $districts = ['Shimla', 'Kangra', 'Mandi', 'Solan', 'Una', 'Hamirpur'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createUttarakhandDistricts($state)
    {
        $districts = ['Dehradun', 'Haridwar', 'Nainital', 'Almora', 'Pauri', 'Chamoli'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createGoaDistricts($state)
    {
        $districts = ['North Goa', 'South Goa'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createManipurDistricts($state)
    {
        $districts = ['Imphal East', 'Imphal West', 'Bishnupur', 'Chandel', 'Churachandpur'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createMeghalayaDistricts($state)
    {
        $districts = ['Shillong', 'Tura', 'Jowai', 'Nongstoin', 'Williamnagar'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createMizoramDistricts($state)
    {
        $districts = ['Aizawl', 'Lunglei', 'Champhai', 'Kolasib', 'Mamit'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createNagalandDistricts($state)
    {
        $districts = ['Kohima', 'Dimapur', 'Mokokchung', 'Tuensang', 'Wokha'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createTripuraDistricts($state)
    {
        $districts = ['Agartala', 'Dharmanagar', 'Kailashahar', 'Udaipur', 'Ambassa'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createSikkimDistricts($state)
    {
        $districts = ['Gangtok', 'Pelling', 'Namchi', 'Gyalshing', 'Mangan'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createArunachalPradeshDistricts($state)
    {
        $districts = ['Itanagar', 'Tawang', 'Pasighat', 'Tezpur', 'Bomdila'];
        $this->createSimpleDistricts($districts, $state);
    }

    private function createSimpleDistricts($districts, $state)
    {
        foreach ($districts as $districtName) {
            $district = District::create([
                'name' => $districtName,
                'state_id' => $state->id,
                'status' => true
            ]);

            Block::create([
                'name' => $districtName . ' Block',
                'district_id' => $district->id,
                'status' => true
            ]);
        }
    }
}
