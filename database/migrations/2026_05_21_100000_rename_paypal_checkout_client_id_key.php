<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename the PayPal Checkout "client-id" config key to "client_id"
     * so the gateway data keys use a consistent naming convention.
     */
    public function up(): void
    {
        $this->renameDataKey('client-id', 'client_id');
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        $this->renameDataKey('client_id', 'client-id');
    }

    private function renameDataKey(string $from, string $to): void
    {
        $gateways = DB::table('shop_gateways')
            ->where('type', 'paypal-checkout')
            ->whereNotNull('data')
            ->get();

        foreach ($gateways as $gateway) {
            $data = json_decode($gateway->data, true);

            if (! is_array($data) || ! array_key_exists($from, $data)) {
                continue;
            }

            $data[$to] = $data[$from];
            unset($data[$from]);

            DB::table('shop_gateways')->where('id', $gateway->id)->update([
                'data' => json_encode($data),
            ]);
        }
    }
};
