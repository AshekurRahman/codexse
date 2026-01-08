<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Expand columns to TEXT and encrypt existing sensitive order fields.
     *
     * Note: This migration first expands columns to TEXT to accommodate
     * encrypted data, then encrypts existing plaintext data.
     */
    public function up(): void
    {
        // Step 0: Drop index on payoneer_transaction_id if exists (can't have index on TEXT)
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['payoneer_transaction_id']);
        });

        // Step 1: Expand column sizes to TEXT for encrypted data
        Schema::table('orders', function (Blueprint $table) {
            $table->text('stripe_session_id')->nullable()->change();
            $table->text('stripe_payment_intent_id')->nullable()->change();
            $table->text('stripe_charge_id')->nullable()->change();
            $table->text('paypal_order_id')->nullable()->change();
            $table->text('payoneer_transaction_id')->nullable()->change();
            $table->text('billing_address')->nullable()->change();
            $table->text('ip_address')->nullable()->change();
        });

        // Step 2: Encrypt existing data
        $fieldsToEncrypt = [
            'stripe_session_id',
            'stripe_payment_intent_id',
            'stripe_charge_id',
            'paypal_order_id',
            'payoneer_transaction_id',
            'ip_address',
        ];

        // Process orders in chunks to avoid memory issues
        DB::table('orders')->orderBy('id')->chunk(100, function ($orders) use ($fieldsToEncrypt) {
            foreach ($orders as $order) {
                $updates = [];

                foreach ($fieldsToEncrypt as $field) {
                    $value = $order->$field;

                    // Skip if null or empty
                    if (empty($value)) {
                        continue;
                    }

                    // Skip if already encrypted (starts with eyJ - base64 JSON)
                    if (str_starts_with($value, 'eyJ')) {
                        continue;
                    }

                    try {
                        $updates[$field] = Crypt::encryptString($value);
                    } catch (\Exception $e) {
                        Log::warning("Failed to encrypt order {$order->id} field {$field}: " . $e->getMessage());
                    }
                }

                // Handle billing_address separately (needs array encryption)
                if (!empty($order->billing_address)) {
                    $billingAddress = $order->billing_address;

                    // Skip if already encrypted
                    if (!str_starts_with($billingAddress, 'eyJ')) {
                        try {
                            // Decode if it's JSON, then encrypt
                            $decoded = json_decode($billingAddress, true);
                            if ($decoded !== null) {
                                $updates['billing_address'] = Crypt::encryptString(json_encode($decoded));
                            }
                        } catch (\Exception $e) {
                            Log::warning("Failed to encrypt order {$order->id} billing_address: " . $e->getMessage());
                        }
                    }
                }

                // Update the record if there are changes
                if (!empty($updates)) {
                    DB::table('orders')->where('id', $order->id)->update($updates);
                }
            }
        });

        Log::info('Completed encrypting sensitive order fields');
    }

    /**
     * Decrypt fields back to plaintext (for rollback).
     *
     * WARNING: Only use this in development. In production, you should
     * keep data encrypted and remove the encryption casts from the model instead.
     */
    public function down(): void
    {
        $fieldsToDecrypt = [
            'stripe_session_id',
            'stripe_payment_intent_id',
            'stripe_charge_id',
            'paypal_order_id',
            'payoneer_transaction_id',
            'ip_address',
        ];

        DB::table('orders')->orderBy('id')->chunk(100, function ($orders) use ($fieldsToDecrypt) {
            foreach ($orders as $order) {
                $updates = [];

                foreach ($fieldsToDecrypt as $field) {
                    $value = $order->$field;

                    if (empty($value)) {
                        continue;
                    }

                    // Only decrypt if it looks encrypted (starts with eyJ)
                    if (str_starts_with($value, 'eyJ')) {
                        try {
                            $updates[$field] = Crypt::decryptString($value);
                        } catch (\Exception $e) {
                            Log::warning("Failed to decrypt order {$order->id} field {$field}: " . $e->getMessage());
                        }
                    }
                }

                // Handle billing_address
                if (!empty($order->billing_address) && str_starts_with($order->billing_address, 'eyJ')) {
                    try {
                        $updates['billing_address'] = Crypt::decryptString($order->billing_address);
                    } catch (\Exception $e) {
                        Log::warning("Failed to decrypt order {$order->id} billing_address: " . $e->getMessage());
                    }
                }

                if (!empty($updates)) {
                    DB::table('orders')->where('id', $order->id)->update($updates);
                }
            }
        });

        // Revert column sizes (optional - keeping TEXT is fine)
        Schema::table('orders', function (Blueprint $table) {
            $table->string('stripe_session_id')->nullable()->change();
            $table->string('stripe_payment_intent_id')->nullable()->change();
            $table->string('stripe_charge_id')->nullable()->change();
            $table->string('paypal_order_id')->nullable()->change();
            $table->string('payoneer_transaction_id')->nullable()->change();
            $table->string('ip_address', 45)->nullable()->change();
        });

        Log::info('Completed decrypting order fields (rollback)');
    }
};
