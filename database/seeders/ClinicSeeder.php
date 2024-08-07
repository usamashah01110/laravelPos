<?php

namespace Database\Seeders;

use App\Repositories\ClinicsRepository;
use Illuminate\Database\Seeder;

class ClinicSeeder extends Seeder
{
    protected   $clinicsRepository;
    public function __construct(ClinicsRepository $clinicsRepository)
    {
        $this->clinicsRepository = $clinicsRepository;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clinicNames = [
            'Shaukat Khanum Memorial Cancer Hospital & Research Centre',
            'Lahore General Hospital',
            'Mayo Hospital Lahore',
            'Hameed Latif Hospital',
            'Jinnah Hospital Lahore',
            'Evercare Hospital',
            'Continental Hospital',
            'Fauji Foundation Hospital',
            'Ameerpet Hospital',
            'City Hospital',
            'Rafiq Hospital',
            'Chughtai Lab',
            'The Indus Hospital',
            'KASB Hospital',
            'Fatima Memorial Hospital',
            'Saira Memorial Hospital',
            'Rashid Latif Hospital',
            'Park Lane Hospital',
            'Al-Khair Hospital',
            'Medicare Hospital',
            'Naz Hospital',
            'Peshawar Road Hospital',
            'Lahore Medical Complex',
            'Dr. Aasim’s Clinic',
            'Life Care Hospital',
            'Nishat Hospital',
            'Zainab Hospital',
            'Health Link Clinic',
            'Anwar Hospital',
            'Max Health Hospital',
            'Aisha Hospital',
            'Prime Hospital',
            'Diamond Hospital',
            'Saad Hospital',
            'Siddiqui Hospital',
            'Lahore Institute of Urology & Transplantation (LIUT)',
            'Orient Hospital',
            'Chronic Care Hospital',
            'Trust Hospital',
            'Safa Hospital',
            'National Hospital',
            'Ammar Medical Center',
            'Hadi Hospital',
            'Shifa International Hospital',
            'Smile Dental Clinic',
            'Asma Clinic',
            'Care Medical Center',
            'Mediwell Clinic',
            'Dr. Ahmed’s Clinic',
            'Premier Hospital'
        ];

        // Loop through each clinic name and create a record
        foreach ($clinicNames as $index => $clinicName) {
            $clinic = [
                'name' => $clinicName,
                'address' => 'Address for ' . $clinicName,
                'email' => strtolower(str_replace(' ', '.', $clinicName)) . '@example.com',
                'phone' => '1234567890', // Placeholder phone number
                'image_url' => 'http://example.com/images/' . strtolower(str_replace(' ', '_', $clinicName)) . '.jpg',
                'about_us' => 'Providing quality healthcare services.',
                'website' => 'http://' . strtolower(str_replace(' ', '', $clinicName)) . '.com',
                'created_by' => $index
            ];

            // Insert data into the clinics table
            $this->clinicsRepository->create($clinic);
            }
        }
}
