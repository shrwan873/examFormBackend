<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('exam_date')->nullable();
            $table->decimal('fee', 10, 2)->default(0.00);
            $table->json('structure')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('forms');
    }
};
