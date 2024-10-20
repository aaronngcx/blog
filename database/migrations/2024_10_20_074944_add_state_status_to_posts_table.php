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
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->default(1)->after('content');
            $table->foreign('state_id')->references('id')->on('states')->onDelete('set null');

            $table->enum('status', ['hidden', 'public'])->default('hidden')->after('state_id');
        });
    }

    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropForeign(['state_id']);
            $table->dropColumn(['status', 'state_id']);
        });
    }
};
