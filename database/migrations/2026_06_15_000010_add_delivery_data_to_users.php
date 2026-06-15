<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'delivery_data')) {
                $table->json('delivery_data')->nullable()->after('delivery_branch_ref');
            }
        });

        if (! Schema::hasColumn('users', 'delivery_data')) {
            return;
        }

        $carriers = ['nova_poshta', 'ukrposhta', 'meest', 'courier', 'pickup'];

        DB::table('users')->orderBy('id')->chunk(100, function ($users) use ($carriers) {
            foreach ($users as $user) {
                if (! empty($user->delivery_data)) {
                    continue;
                }

                $all = [];
                foreach ($carriers as $carrier) {
                    $all[$carrier] = [
                        'city' => '',
                        'city_ref' => '',
                        'branch' => '',
                        'branch_ref' => '',
                        'manual' => in_array($carrier, ['courier', 'pickup'], true),
                    ];
                }

                $savedCarrier = $user->delivery_carrier ?? null;
                if ($savedCarrier && in_array($savedCarrier, $carriers, true) && ! empty($user->delivery_city)) {
                    $all[$savedCarrier] = [
                        'city' => $user->delivery_city ?? '',
                        'city_ref' => $user->delivery_city_ref ?? '',
                        'branch' => $user->delivery_branch ?? '',
                        'branch_ref' => $user->delivery_branch_ref ?? '',
                        'manual' => in_array($savedCarrier, ['courier', 'pickup'], true),
                    ];
                }

                DB::table('users')->where('id', $user->id)->update([
                    'delivery_data' => json_encode($all, JSON_UNESCAPED_UNICODE),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'delivery_data')) {
                $table->dropColumn('delivery_data');
            }
        });
    }
};
