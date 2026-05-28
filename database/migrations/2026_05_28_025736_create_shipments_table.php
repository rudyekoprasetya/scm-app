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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('shipment_number')->unique();
            $table->string('carrier'); // e.g., JNE, J&T, GoSend
            $table->string('tracking_number')->unique()->nullable();
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->enum('status', ['pending', 'picked_up', 'in_transit', 'delivered', 'failed'])->default('pending');
            $table->text('origin')->nullable(); // warehouse address
            $table->text('destination')->nullable(); // customer address
            $table->date('estimated_delivery_date')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // courier/logistics user
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
