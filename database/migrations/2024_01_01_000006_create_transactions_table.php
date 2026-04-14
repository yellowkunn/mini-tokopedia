<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->enum('status', [
                'pending',
                'paid',
                'processing',
                'shipped',
                'delivered',
                'completed',
                'cancelled'
            ])->default('pending');
            $table->enum('payment_method', [
                'bank_transfer',
                'e_wallet',
                'cod'
            ])->nullable();
            $table->decimal('total_price', 15, 2);
            $table->text('shipping_address'); // Snapshot alamat saat checkout
            $table->string('shipping_courier')->nullable(); // JNE, J&T, dll
            $table->string('shipping_service')->nullable(); // REG, YES, dll
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->string('tracking_number')->nullable(); // No resi
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('voucher_discount', 12, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('shipping_address_id')->nullable()->constrained('shipping_addresses');
            $table->foreignId('shop_id')->constrained('shops'); // Perlu untuk grouping per toko
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
