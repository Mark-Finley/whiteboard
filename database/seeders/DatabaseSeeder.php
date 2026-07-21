<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = collect([
            'Admin',
            'Triage Nurse',
            'Ward Staff',
            'Specialty Doctor',
            'In-Charge',
            'Consultant',
            'Medical Officer',
            'Doctor',
        ])->mapWithKeys(fn (string $roleName): array => [
            $roleName => Role::query()->firstOrCreate(['name' => $roleName]),
        ]);

        $teams = collect([
            'Haematology',
            'Cardiology',
            'General Surgery',
            'Neurosurgery',
            'Orthopaedics',
            'Oncology',
            'Internal Medicine',
            'Paediatrics',
            'Obstetrics & Gynaecology',
            'ENT',
            'Urology',
            'Maxillofacial',
            'Plastic Surgery',
            'Emergency Medicine',
        ])->mapWithKeys(fn (string $teamName): array => [
            $teamName => Team::query()->firstOrCreate(['name' => $teamName]),
        ]);

        foreach ([
            Ward::TRIAGE_HOLDING,
            Ward::RED,
            Ward::ORANGE,
            Ward::YELLOW,
        ] as $wardName) {
            $color = Ward::colorForName($wardName);
            Ward::query()->firstOrCreate(['name' => $wardName], ['color_code' => $color]);
        }

        User::query()->updateOrCreate(
            ['email' => 'admin@kath.local'],
            [
                'name' => 'KEPTS Administrator',
                'phone' => '+233000000001',
                'password' => Hash::make('Password123!'),
                'role_id' => $roles['Admin']->id,
                'team_id' => $teams['Emergency Medicine']->id,
                'ward_id' => Ward::query()->where('name', Ward::TRIAGE_HOLDING)->value('id'),
                'status' => 'active',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'triage@kath.local'],
            [
                'name' => 'Triage Nurse Demo',
                'phone' => '+233000000002',
                'password' => Hash::make('Password123!'),
                'role_id' => $roles['Triage Nurse']->id,
                'team_id' => null,
                'ward_id' => Ward::query()->where('name', Ward::TRIAGE_HOLDING)->value('id'),
                'status' => 'active',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'ward@kath.local'],
            [
                'name' => 'Ward Staff Demo',
                'phone' => '+233000000003',
                'password' => Hash::make('Password123!'),
                'role_id' => $roles['Ward Staff']->id,
                'team_id' => null,
                'ward_id' => Ward::query()->where('name', Ward::RED)->value('id'),
                'status' => 'active',
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'doctor@kath.local'],
            [
                'name' => 'Specialty Doctor Demo',
                'phone' => '+233000000004',
                'password' => Hash::make('Password123!'),
                'role_id' => $roles['Specialty Doctor']->id,
                'team_id' => $teams['Emergency Medicine']->id,
                'ward_id' => Ward::query()->where('name', Ward::ORANGE)->value('id'),
                'status' => 'active',
            ]
        );

        $catalog = [
            'Laboratory' => [
                'Full Blood Count (FBC)',
                'Urea & Electrolytes (U&E)',
                'Blood Culture',
                'Malaria Test',
                'Blood Grouping',
                'Cross Match',
                'Coagulation Profile',
                'Liver Function Test',
                'Renal Function Test',
                'Hb Electrophoresis',
                'ESR',
                'CRP',
                'Troponin',
                'Blood Gas Analysis',
                'Custom Laboratory Request'
            ],
            'Imaging' => [
                'Chest X-Ray',
                'Skull X-Ray',
                'Pelvic X-Ray',
                'Abdominal X-Ray',
                'CT Brain',
                'CT Chest',
                'CT Abdomen',
                'MRI Brain',
                'MRI Spine',
                'Ultrasound Abdomen',
                'Echocardiogram',
                'Doppler Ultrasound',
                'Custom Imaging Request'
            ],
            'Procedures' => [
                'ECG',
                'Lumbar Puncture',
                'Bone Marrow Aspiration',
                'Blood Transfusion',
                'Oxygen Therapy',
                'Wound Dressing',
                'Catheterization',
                'Central Line Placement',
                'Custom Procedure'
            ]
        ];

        foreach ($catalog as $category => $items) {
            foreach ($items as $name) {
                \App\Models\InvestigationCatalog::query()->firstOrCreate(
                    ['name' => $name],
                    ['category' => $category, 'is_active' => true]
                );
            }
        }
    }
}
