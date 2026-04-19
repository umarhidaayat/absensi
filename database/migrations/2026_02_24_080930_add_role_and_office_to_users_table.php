<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom role untuk membedakan hak akses
            $table->enum('role', ['admin', 'karyawan'])->default('karyawan');
            
            // Kolom office_id, dibuat nullable (boleh kosong) karena Admin mungkin tidak terikat 1 kantor
            $table->foreignId('office_id')->nullable()->constrained('offices')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn(['role', 'office_id']);
        });
    }
};