<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('attribute_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            
            $table->unique(['attribute_id', 'locale']);
        });

        // Move existing data to translations table
        $defaultLocale = config('app.locale', 'en');
        DB::statement("
            INSERT INTO attribute_translations (attribute_id, locale, name)
            SELECT id, '{$defaultLocale}', name FROM attributes
        ");

        // Remove translated columns from attributes table
        Schema::table('attributes', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    public function down()
    {
        // Add back the columns to attributes table
        Schema::table('attributes', function (Blueprint $table) {
            $table->string('name');
        });

        // Move data back from translations
        $defaultLocale = config('app.locale', 'en');
        DB::statement("
            UPDATE attributes a
            INNER JOIN attribute_translations at ON at.attribute_id = a.id
            SET a.name = at.name
            WHERE at.locale = '{$defaultLocale}'
        ");

        Schema::dropIfExists('attribute_translations');
    }
};
