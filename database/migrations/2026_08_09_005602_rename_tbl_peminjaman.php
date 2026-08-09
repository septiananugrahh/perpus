<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('tbl_peminjaman', 'tbl_peminjaman_buku');

        Schema::table('tbl_peminjaman_buku', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_peminjaman_buku', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::rename('tbl_peminjaman_buku', 'tbl_peminjaman');
    }
};
