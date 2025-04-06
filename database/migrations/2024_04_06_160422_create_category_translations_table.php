<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('locale')->index();
            $table->string('name');
            $table->text('description')->nullable();
            
            $table->unique(['category_id', 'locale']);
        });

        // Move existing data to translations table
        $defaultLocale = config('app.locale', 'en');
        DB::statement("
            INSERT INTO category_translations (category_id, locale, name, description)
            SELECT id, '{$defaultLocale}', name, description FROM categories
        ");

        // Remove translated columns from categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });
    }

    public function down()
    {
        // Add back the columns to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name');
            $table->text('description')->nullable();
        });

        // Move data back from translations
        $defaultLocale = config('app.locale', 'en');
        DB::statement("
            UPDATE categories c
            INNER JOIN category_translations ct ON ct.category_id = c.id
            SET c.name = ct.name, c.description = ct.description
            WHERE ct.locale = '{$defaultLocale}'
        ");

        Schema::dropIfExists('category_translations');
    }
};
