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
        Schema::create('fee_students', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('feeId')->nullable();
            $table->unsignedBigInteger('userId')->nullable();
            $table->foreign('feeId')->references('id')->on('fees')->onDelete('set null');
            $table->foreign('userId')->references('id')->on('users')->onDelete('set null');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->float('paid_amount')->default(0);
            $table->json('payment');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fee_students');
    }
};
