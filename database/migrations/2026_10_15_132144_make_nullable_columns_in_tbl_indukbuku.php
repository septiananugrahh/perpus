<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_indukbuku', function (Blueprint $table) {
            $table->string('penerbit_buku', 50)->nullable()->change();
            $table->string('pengarang_buku', 50)->nullable()->change();
            $table->string('tahunterbit_buku', 10)->nullable()->change();
            $table->string('kotaterbit_buku', 20)->nullable()->change();
            $table->string('isbn_buku', 50)->nullable()->change();
            $table->string('nomerpanggil_buku', 50)->nullable()->change();
            $table->string('eksemplar_buku', 20)->nullable()->change();
            $table->string('series_buku', 20)->nullable()->change();
            $table->string('sumber_buku', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_indukbuku', function (Blueprint $table) {
            $table->string('penerbit_buku', 50)->nullable(false)->change();
            $table->string('pengarang_buku', 50)->nullable(false)->change();
            $table->string('tahunterbit_buku', 10)->nullable(false)->change();
            $table->string('kotaterbit_buku', 20)->nullable(false)->change();
            $table->string('isbn_buku', 50)->nullable(false)->change();
            $table->string('nomerpanggil_buku', 50)->nullable(false)->change();
            $table->string('eksemplar_buku', 20)->nullable(false)->change();
            $table->string('series_buku', 20)->nullable(false)->change();
            $table->string('sumber_buku', 20)->nullable(false)->change();
        });
    }
};
