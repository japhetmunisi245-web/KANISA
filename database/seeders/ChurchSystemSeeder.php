<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ChurchSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles
        $roleAdmin      = Role::firstOrCreate(['name' => 'admin']);
        $roleMchungaji  = Role::firstOrCreate(['name' => 'mchungaji']);
        $roleKiongozi   = Role::firstOrCreate(['name' => 'kiongozi']);
        $roleMshiriki   = Role::firstOrCreate(['name' => 'mshiriki']);
        $roleSecretary  = Role::firstOrCreate(['name' => 'secretary']);
        $roleDeacon     = Role::firstOrCreate(['name' => 'deacon']);
        $roleAccountant = Role::firstOrCreate(['name' => 'accountant']);

        // 2. Create Committees (Departments)
        $committees = [
            [
                'name_sw' => 'Idara ya Wamama (WWI)',
                'name_en' => 'Women\'s Department (WWI)',
                'description_sw' => 'Idara ya ushirika wa wamama (WWI).',
                'description_en' => 'Women\'s fellowship department (WWI).',
                'icon' => 'fa-female',
                'color' => '#ec4899',
            ],
            [
                'name_sw' => 'Idara ya Wababa',
                'name_en' => 'Men\'s Department',
                'description_sw' => 'Idara ya ushirika wa wababa.',
                'description_en' => 'Men\'s fellowship department.',
                'icon' => 'fa-male',
                'color' => '#1e40af',
            ],
            [
                'name_sw' => 'Idara ya Vijana (CGM)',
                'name_en' => 'Youth Department (CGM)',
                'description_sw' => 'Idara inayokutanisha vijana (CGM) kwa ajili ya kumtumikia Mungu.',
                'description_en' => 'Youth department (CGM) serving God.',
                'icon' => 'fa-users',
                'color' => '#10b981',
            ],
            [
                'name_sw' => 'Idara ya Watoto (CWI)',
                'name_en' => 'Children\'s Department (CWI)',
                'description_sw' => 'Idara inayoshughulika na malezi ya kiroho kwa watoto (CWI).',
                'description_en' => 'Children\'s spiritual upbringing department (CWI).',
                'icon' => 'fa-child',
                'color' => '#3b82f6',
            ],
            [
                'name_sw' => 'Idara ya Praise & Kwaya (KUSKI)',
                'name_en' => 'Praise & Choir Department (KUSKI)',
                'description_sw' => 'Idara inayoshughulika na sifa, kuabudu na uimbaji wa kwaya (KUSKI).',
                'description_en' => 'Praise, worship and choir singing department (KUSKI).',
                'icon' => 'fa-music',
                'color' => '#8b5cf6',
            ],
        ];

        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('department_user')->delete();
        DB::table('departments')->delete();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        foreach ($committees as $c) {
            Department::create($c);
        }

        // 3. Create Positions
        $positions = [
            'MWENYEKITI',
            'MWENYEKITI MSAIDIZI',
            'KATIBU',
            'KATIBU MSAIDIZI',
            'MHAZINI'
        ];

        foreach ($positions as $p) {
            DB::table('positions')->updateOrInsert(['name' => $p]);
        }

        // 4. Create / Update Super Admin Accounts
        $adminEmails = ['admin@philadelphia.com', 'admin@philadelphia'];

        foreach ($adminEmails as $idx => $email) {
            $admin = User::where('email', $email)->first();
            $memberId = ($idx === 0) ? 'EAGT-KBP-ADMIN' : 'EAGT-KBP-ADMIN-' . ($idx + 1);
            if (!$admin) {
                $admin = User::create([
                    'name'      => 'SYSTEM ADMIN',
                    'email'     => $email,
                    'phone'     => '070000000' . ($idx + 1),
                    'password'  => Hash::make('admin@123'),
                    'member_id' => $memberId,
                    'role'      => 'admin',
                    'status'    => 'active',
                    'gender'    => 'male'
                ]);
            } else {
                $admin->update([
                    'password' => Hash::make('admin@123'),
                    'role'     => 'admin',
                ]);
            }
            $admin->assignRole($roleAdmin);
            $admin->departments()->detach();
        }

        // 5. Create / Update Mchungaji Accounts (MAGDALENA MKINGA)
        $mchungajiEmails = ['mchungaji@philadelphia.com', 'mcchungaji@philadelphia.com', 'mchungaji@philadelphia', 'mcchungaji@philadelphia'];
        foreach ($mchungajiEmails as $idx => $email) {
            $mchungaji = User::where('email', $email)->first();
            $mcMemberId = ($idx === 0) ? 'EAGT-KBP-0001' : 'EAGT-KBP-0001-' . ($idx + 1);
            if (!$mchungaji) {
                $mchungaji = User::create([
                    'name'      => 'REV. MAGDALENA MKINGA',
                    'email'     => $email,
                    'phone'     => '070000000' . ($idx + 3),
                    'password'  => Hash::make('mchungaji@123'),
                    'member_id' => $mcMemberId,
                    'role'      => 'mchungaji',
                    'status'    => 'active',
                    'gender'    => 'female'
                ]);
            } else {
                $mchungaji->update([
                    'password' => Hash::make('mchungaji@123'),
                    'role'     => 'mchungaji',
                ]);
            }
            $mchungaji->assignRole($roleMchungaji);
            $mchungaji->departments()->detach();
        }

        // 6. Church office accounts
        $this->call(ChurchOfficeAccountsSeeder::class);

        // 7. Default Church Settings
        DB::table('church_settings')->updateOrInsert(['key' => 'church_name'], ['value' => 'E.A.G.T KIBURUGWA PHILADELPHIA CHURCH']);
        DB::table('church_settings')->updateOrInsert(['key' => 'address'], ['value' => 'P.O. BOX 3236, DAR ES SALAAM']);
        DB::table('church_settings')->updateOrInsert(['key' => 'authorized_name'], ['value' => 'REV. MAGDALENA MKINGA']);
        DB::table('church_settings')->updateOrInsert(['key' => 'authorized_title'], ['value' => 'MCHUNGAJI']);
        DB::table('church_settings')->updateOrInsert(['key' => 'logo_path'], ['value' => '../images/eagt_logo.png']);
    }
}
