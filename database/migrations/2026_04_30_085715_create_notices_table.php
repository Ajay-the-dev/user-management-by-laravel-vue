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
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('title');
            $table->string('type');
            $table->string('audience'); 
            $table->unsignedBigInteger('customBatch')->nullable();
            $table->longText('html');
            $table->foreign('customBatch')->references('id')->on('batches')->onDelete('set null');
            $table->boolean('show')->default(true);
            $table->date('expiry');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
