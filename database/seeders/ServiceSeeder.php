<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Clinics;
use App\Repositories\ServiceRepository;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class ServiceSeeder extends Seeder
{
    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Collect IDs of clinics and categories
        $clinicIds = Clinics::pluck('id')->toArray();
        $categoryIds = Category::pluck('id')->toArray();

        // Array of services
        $services = [
            "General Checkup",
            "Routine Blood Test",
            "X-ray Examination",
            "Ultrasound Scan",
            "Dental Cleaning",
            "Eye Examination",
            "Hearing Test",
            "Physical Therapy",
            "Vaccination",
            "Weight Management",
            "Diabetes Management",
            "Hypertension Management",
            "Chronic Pain Management",
            "Allergy Testing",
            "Skin Biopsy",
            "Colonoscopy",
            "Endoscopy",
            "Cardiac Stress Test",
            "Pulmonary Function Test",
            "Pregnancy Test",
            "STD Testing",
            "HIV Testing",
            "Hepatitis Screening",
            "Flu Shot",
            "Travel Vaccination",
            "Nutritional Counseling",
            "Sports Physical",
            "Pre-employment Physical",
            "Men's Health Exam",
            "Women's Health Exam",
            "Pediatric Checkup",
            "Geriatric Care",
            "Occupational Therapy",
            "Speech Therapy",
            "Psychological Counseling",
            "Substance Abuse Counseling",
            "Grief Counseling",
            "Behavioral Therapy",
            "Sleep Study",
            "Medication Management",
            "Hearing Aid Fitting",
            "Vision Correction",
            "Foot Care",
            "Wound Care",
            "Minor Surgery",
            "Pain Management Clinic",
            "Travel Medicine",
            "Complementary Medicine",
            "Blood Pressure Monitoring",
            "Diabetes Education",
            "Health Risk Assessment",
            "Chronic Disease Management",
            "Cancer Screening",
            "Heart Disease Screening",
            "Lung Disease Screening",
            "Kidney Function Test",
            "Liver Function Test",
            "Thyroid Function Test",
            "Hormone Replacement Therapy",
            "Dermatological Procedures",
            "Vascular Health Screening",
            "Allergy Immunotherapy",
            "Infertility Consultations",
            "Prenatal Care",
            "Postnatal Care",
            "Breastfeeding Counseling",
            "Family Planning Services",
            "Contraceptive Counseling",
            "Emergency Care",
            "Urgent Care",
            "Trauma Care",
            "Ambulance Services",
            "Home Health Services",
            "Medical Equipment Rental",
            "Insurance Counseling",
            "Patient Advocacy",
            "Community Health Programs",
            "Telemedicine Consultations",
            "On-site Pharmacy",
            "Medical Aesthetics"
        ];

        // Seed services
        foreach ($services as $index => $serviceName) {
            $service = [
                'clinics_id' => $clinicIds[array_rand($clinicIds)],
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'name' => $serviceName,
                'description' => "Description for $serviceName",
                'price' => rand(50, 300), // Random price between $50 and $300
                'created_by' => 1, // Adjust this if necessary
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $this->serviceRepository->create($service);
        }

        Log::info('Services seeded successfully.');
    }
}
