<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('author'); // Remove the author column
        });
    }

    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('author')->nullable(); // Re-add the author column if rolling back
        });
    }
};
