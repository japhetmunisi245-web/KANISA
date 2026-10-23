<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ChurchOfficeAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name'=>'CHURCH SECRETARY','email'=>'katibu@philadelphia','password'=>'katibu123','member_id'=>'EAGT-KBP-SECRETARY','gender'=>'female','cheo'=>'KATIBU','role'=>'secretary'],
            ['name'=>'CHURCH DEACON','email'=>'shemasi@philadelphia','password'=>'shemasi123','member_id'=>'EAGT-KBP-DEACON','gender'=>'male','cheo'=>'SHEMASI','role'=>'deacon'],
            ['name'=>'CHURCH ACCOUNTANT','email'=>'mhasibu@philadelphia','password'=>'mhasibu123','member_id'=>'EAGT-KBP-ACCOUNTANT','gender'=>'male','cheo'=>'MHASIBU','role'=>'accountant'],
        ];
        foreach ($accounts as $account) {
            $role = Role::firstOrCreate(['name' => $account['role']]);
            $user = User::updateOrCreate(['email' => $account['email']], [
                'name'=>$account['name'], 'password'=>Hash::make($account['password']), 'member_id'=>$account['member_id'],
                'role'=>'mshirika', 'status'=>'active', 'gender'=>$account['gender'], 'cheo'=>$account['cheo'],
            ]);
            $user->syncRoles([$role]);
            $user->departments()->detach();
        }
    }
}
