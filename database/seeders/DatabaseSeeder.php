<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the hospital database with demo data.
     * Safe to re-run: uses updateOrCreate / firstOrCreate on unique keys.
     */
    public function run(): void
    {
        // ---------------- Users ----------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@hospital.com'],
            ['name' => 'System Administrator', 'role' => User::ROLE_ADMIN, 'phone' => '+254700000001', 'password' => Hash::make('password123')]
        );

        $doctorUser1 = User::updateOrCreate(
            ['email' => 'doctor@hospital.com'],
            ['name' => 'Dr. James Mwangi', 'role' => User::ROLE_DOCTOR, 'phone' => '+254700000002', 'password' => Hash::make('password123')]
        );

        $doctorUser2 = User::updateOrCreate(
            ['email' => 'amina.otieno@hospital.com'],
            ['name' => 'Dr. Amina Otieno', 'role' => User::ROLE_DOCTOR, 'phone' => '+254700000003', 'password' => Hash::make('password123')]
        );

        $doctorUser3 = User::updateOrCreate(
            ['email' => 'peter.kamau@hospital.com'],
            ['name' => 'Dr. Peter Kamau', 'role' => User::ROLE_DOCTOR, 'phone' => '+254700000004', 'password' => Hash::make('password123')]
        );

        User::updateOrCreate(
            ['email' => 'reception@hospital.com'],
            ['name' => 'Grace Wanjiru', 'role' => User::ROLE_RECEPTIONIST, 'phone' => '+254700000005', 'password' => Hash::make('password123')]
        );

        // ---------------- Departments ----------------
        $cardiology = Department::firstOrCreate(
            ['name' => 'Cardiology'],
            ['description' => 'Diagnosis and treatment of heart and blood vessel disorders.']
        );
        $pediatrics = Department::firstOrCreate(
            ['name' => 'Pediatrics'],
            ['description' => 'Medical care for infants, children and adolescents.']
        );
        $surgery = Department::firstOrCreate(
            ['name' => 'General Surgery'],
            ['description' => 'Surgical treatment of abdominal organs, trauma and emergencies.']
        );

        // ---------------- Wards ----------------
        $wardA = Ward::firstOrCreate(['name' => 'Cardiac Care Unit'], ['department_id' => $cardiology->id, 'type' => 'icu', 'capacity' => 8, 'description' => 'Intensive monitoring for cardiac patients.']);
        $wardB = Ward::firstOrCreate(['name' => 'Children\'s Ward'], ['department_id' => $pediatrics->id, 'type' => 'pediatric', 'capacity' => 20, 'description' => 'General ward for pediatric patients.']);
        $wardC = Ward::firstOrCreate(['name' => 'Surgical Ward'], ['department_id' => $surgery->id, 'type' => 'surgical', 'capacity' => 15, 'description' => 'Pre- and post-operative care.']);
        Ward::firstOrCreate(['name' => 'General Ward A'], ['department_id' => $surgery->id, 'type' => 'general', 'capacity' => 25, 'description' => 'Mixed general admissions.']);

        // ---------------- Doctors (linked to user accounts) ----------------
        $doc1 = Doctor::updateOrCreate(
            ['user_id' => $doctorUser1->id],
            ['department_id' => $cardiology->id, 'name' => 'James Mwangi', 'specialization' => 'Cardiologist', 'license_number' => 'KMPDC-10001', 'phone' => '+254711000001', 'email' => $doctorUser1->email, 'schedule' => 'Mon-Fri 08:00-16:00', 'is_active' => true]
        );
        $doc2 = Doctor::updateOrCreate(
            ['user_id' => $doctorUser2->id],
            ['department_id' => $pediatrics->id, 'name' => 'Amina Otieno', 'specialization' => 'Pediatrician', 'license_number' => 'KMPDC-10002', 'phone' => '+254711000002', 'email' => $doctorUser2->email, 'schedule' => 'Mon-Sat 09:00-17:00', 'is_active' => true]
        );
        $doc3 = Doctor::updateOrCreate(
            ['user_id' => $doctorUser3->id],
            ['department_id' => $surgery->id, 'name' => 'Peter Kamau', 'specialization' => 'General Surgeon', 'license_number' => 'KMPDC-10003', 'phone' => '+254711000003', 'email' => $doctorUser3->email, 'schedule' => 'Tue-Sat 07:00-15:00', 'is_active' => true]
        );

        // Assign department heads
        $cardiology->update(['head_doctor_id' => $doc1->id]);
        $pediatrics->update(['head_doctor_id' => $doc2->id]);
        $surgery->update(['head_doctor_id' => $doc3->id]);

        // Only create transactional demo data once
        if (Patient::count() > 0) {
            return;
        }

        // ---------------- Patients ----------------
        $patientsData = [
            ['first_name' => 'Mary', 'last_name' => 'Njeri', 'date_of_birth' => '1985-04-12', 'gender' => 'female', 'blood_type' => 'O+', 'phone' => '+254722100001', 'email' => 'mary.njeri@example.com', 'address' => '12 Moi Avenue, Nairobi', 'emergency_contact_name' => 'John Njeri', 'emergency_contact_phone' => '+254722200001', 'emergency_contact_relation' => 'Husband', 'allergies' => 'Penicillin'],
            ['first_name' => 'David', 'last_name' => 'Ochieng', 'date_of_birth' => '1972-09-30', 'gender' => 'male', 'blood_type' => 'A+', 'phone' => '+254722100002', 'email' => 'david.ochieng@example.com', 'address' => '45 Oginga Odinga St, Kisumu', 'emergency_contact_name' => 'Rose Ochieng', 'emergency_contact_phone' => '+254722200002', 'emergency_contact_relation' => 'Wife', 'allergies' => null],
            ['first_name' => 'Brian', 'last_name' => 'Kiptoo', 'date_of_birth' => '2016-02-18', 'gender' => 'male', 'blood_type' => 'B+', 'phone' => '+254722100003', 'email' => null, 'address' => '8 Uganda Road, Eldoret', 'emergency_contact_name' => 'Ann Kiptoo', 'emergency_contact_phone' => '+254722200003', 'emergency_contact_relation' => 'Mother', 'allergies' => 'Peanuts'],
            ['first_name' => 'Fatuma', 'last_name' => 'Hassan', 'date_of_birth' => '1990-11-05', 'gender' => 'female', 'blood_type' => 'AB-', 'phone' => '+254722100004', 'email' => 'fatuma.hassan@example.com', 'address' => '3 Digo Road, Mombasa', 'emergency_contact_name' => 'Ali Hassan', 'emergency_contact_phone' => '+254722200004', 'emergency_contact_relation' => 'Brother', 'allergies' => null],
            ['first_name' => 'Samuel', 'last_name' => 'Mutua', 'date_of_birth' => '1958-07-22', 'gender' => 'male', 'blood_type' => 'O-', 'phone' => '+254722100005', 'email' => 'samuel.mutua@example.com', 'address' => '21 Ngei Street, Machakos', 'emergency_contact_name' => 'Esther Mutua', 'emergency_contact_phone' => '+254722200005', 'emergency_contact_relation' => 'Daughter', 'allergies' => 'Sulfa drugs'],
        ];

        $patients = collect($patientsData)->map(fn (array $data) => Patient::create($data));

        // ---------------- Appointments ----------------
        $today = now()->startOfDay();

        Appointment::create(['patient_id' => $patients[0]->id, 'doctor_id' => $doc1->id, 'appointment_date' => $today->copy()->setTime(9, 0), 'status' => Appointment::STATUS_SCHEDULED, 'reason' => 'Chest pain follow-up', 'notes' => 'Bring previous ECG results.']);
        Appointment::create(['patient_id' => $patients[2]->id, 'doctor_id' => $doc2->id, 'appointment_date' => $today->copy()->setTime(11, 30), 'status' => Appointment::STATUS_SCHEDULED, 'reason' => 'Routine vaccination', 'notes' => null]);
        Appointment::create(['patient_id' => $patients[1]->id, 'doctor_id' => $doc3->id, 'appointment_date' => $today->copy()->subDays(3)->setTime(10, 0), 'status' => Appointment::STATUS_COMPLETED, 'reason' => 'Hernia consultation', 'notes' => 'Surgery recommended.']);
        Appointment::create(['patient_id' => $patients[3]->id, 'doctor_id' => $doc1->id, 'appointment_date' => $today->copy()->addDays(2)->setTime(14, 0), 'status' => Appointment::STATUS_SCHEDULED, 'reason' => 'Palpitations', 'notes' => null]);
        Appointment::create(['patient_id' => $patients[4]->id, 'doctor_id' => $doc3->id, 'appointment_date' => $today->copy()->subDay()->setTime(15, 30), 'status' => Appointment::STATUS_CANCELLED, 'reason' => 'Post-operative review', 'notes' => 'Patient requested to reschedule.']);

        // ---------------- Admissions ----------------
        Admission::create(['patient_id' => $patients[1]->id, 'ward_id' => $wardC->id, 'doctor_id' => $doc3->id, 'admitted_at' => now()->subDays(2), 'diagnosis' => 'Inguinal hernia - scheduled for repair', 'notes' => 'Nil by mouth from midnight before surgery.', 'status' => Admission::STATUS_ADMITTED]);
        Admission::create(['patient_id' => $patients[4]->id, 'ward_id' => $wardA->id, 'doctor_id' => $doc1->id, 'admitted_at' => now()->subDays(1), 'diagnosis' => 'Unstable angina', 'notes' => 'Continuous cardiac monitoring.', 'status' => Admission::STATUS_ADMITTED]);
        Admission::create(['patient_id' => $patients[2]->id, 'ward_id' => $wardB->id, 'doctor_id' => $doc2->id, 'admitted_at' => now()->subDays(10), 'discharged_at' => now()->subDays(6), 'diagnosis' => 'Acute gastroenteritis', 'notes' => 'Recovered well with oral rehydration.', 'status' => Admission::STATUS_DISCHARGED]);
    }
}
