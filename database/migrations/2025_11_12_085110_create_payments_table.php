<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('submission_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['initiated','success','failed','refunded'])->default('initiated');
            $table->string('gateway')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
