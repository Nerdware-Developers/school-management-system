<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeFee;
use App\Models\AdmissionFee;
use App\Models\TransportFee;
use Illuminate\Support\Facades\DB;

class FeeStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('grade_fee_structure')->truncate();
        DB::table('admission_fees')->truncate();
        DB::table('transport_fees')->truncate();

        // Seed Grade Fee Structure
        $gradeFees = [
            ['grade' => 'PLAY GROUP', 'tuition_fee' => 6500, 'exam_fee' => 200, 'total_fee' => 6700],
            ['grade' => 'PP1', 'tuition_fee' => 7500, 'exam_fee' => 200, 'total_fee' => 7700],
            ['grade' => 'PP2', 'tuition_fee' => 7500, 'exam_fee' => 200, 'total_fee' => 7700],
            ['grade' => 'GRADE 1', 'tuition_fee' => 8500, 'exam_fee' => 200, 'total_fee' => 8700],
            ['grade' => 'GRADE 2', 'tuition_fee' => 8500, 'exam_fee' => 200, 'total_fee' => 8700],
            ['grade' => 'GRADE 3', 'tuition_fee' => 9500, 'exam_fee' => 500, 'total_fee' => 10000],
            ['grade' => 'GRADE 4', 'tuition_fee' => 9500, 'exam_fee' => 500, 'total_fee' => 10000],
            ['grade' => 'GRADE 5', 'tuition_fee' => 10500, 'exam_fee' => 500, 'total_fee' => 11000],
            ['grade' => 'GRADE 6', 'tuition_fee' => 10500, 'exam_fee' => 500, 'total_fee' => 11000],
            ['grade' => 'GRADE 7', 'tuition_fee' => 18000, 'exam_fee' => 2000, 'total_fee' => 20000],
            ['grade' => 'GRADE 8', 'tuition_fee' => 18000, 'exam_fee' => 2000, 'total_fee' => 20000],
            ['grade' => 'GRADE 9', 'tuition_fee' => 18000, 'exam_fee' => 2000, 'total_fee' => 20000],
        ];

        foreach ($gradeFees as $fee) {
            GradeFee::create($fee);
        }

        // Seed Admission Fees
        $admissionFees = [
            ['fee_type' => 'Interview', 'amount' => 500],
            ['fee_type' => 'Admission', 'amount' => 1000],
            ['fee_type' => 'Assessment Book', 'amount' => 350],
        ];

        foreach ($admissionFees as $fee) {
            AdmissionFee::create($fee);
        }

        // Seed Transport Fees
        // 3,000/= locations
        $transport3000 = [
            'MUWA', 'BARAKA', 'KANGEMA', 'UMOJA ONE', 'URITHI', 
            'KIAMUNYEKI', 'MURUNYU', 'MODERN', 'UMOJA TWO'
        ];

        foreach ($transport3000 as $location) {
            TransportFee::create([
                'location' => $location,
                'amount' => 3000,
            ]);
        }

        // 4,000/= locations
        $transport4000 = [
            'ST. GABRIEL', 'KIRATINA', 'FREE AREA', 'PIPE LINE'
        ];

        foreach ($transport4000 as $location) {
            TransportFee::create([
                'location' => $location,
                'amount' => 4000,
            ]);
        }

        // 6,000/= locations (location name not fully visible in document, adding placeholder)
        TransportFee::create([
            'location' => 'OTHER LOCATIONS',
            'amount' => 6000,
        ]);

        $this->command->info('Fee structure seeded successfully!');
    }
}
