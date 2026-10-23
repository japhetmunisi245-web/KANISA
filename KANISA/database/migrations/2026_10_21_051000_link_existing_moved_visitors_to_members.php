<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
    public function up(): void
    {
        DB::table('service_visitors')->where('has_moved', true)->orderBy('id')->each(function (object $visitor) {
            $member = $visitor->member_id
                ? DB::table('users')->where('id', $visitor->member_id)->first()
                : null;

            if (! $member && filled($visitor->phone)) {
                $member = DB::table('users')->where('phone', $visitor->phone)->first();
            }

            if (! $member) {
                $suffix = str_pad((string) $visitor->id, 6, '0', STR_PAD_LEFT);
                $memberId = 'MHA-OLD-'.$suffix;
                $email = 'mhamia.old.'.$visitor->id.'@kanisa.local';
                $memberId = DB::table('users')->where('member_id', $memberId)->exists() ? 'MHA-OLD-'.$suffix.'-'.$visitor->id : $memberId;
                $id = DB::table('users')->insertGetId([
                    'name' => $visitor->name,
                    'phone' => $visitor->phone,
                    'email' => $email,
                    'member_id' => $memberId,
                    'role' => 'mshirika',
                    'status' => 'active',
                    'joined_as_visitor' => true,
                    'joined_church_at' => $visitor->visit_date,
                    'password' => Hash::make('password'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $member = (object) ['id' => $id];
            } else {
                DB::table('users')->where('id', $member->id)->update([
                    'joined_as_visitor' => true,
                    'joined_church_at' => DB::raw("COALESCE(joined_church_at, '".$visitor->visit_date."')"),
                    'updated_at' => now(),
                ]);
            }

            DB::table('service_visitors')->where('id', $visitor->id)->update(['member_id' => $member->id]);
        });
    }

    public function down(): void
    {
        // Existing attendance records are intentionally preserved.
    }
};
