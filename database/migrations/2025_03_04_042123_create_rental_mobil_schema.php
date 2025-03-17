<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentalMobilSchema extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->enum('status_blokir', ['Ya', 'Tidak']);
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('lokasi_garasi', function (Blueprint $table) {
            $table->id();
            $table->string('kota');
            $table->text('alamat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });

        Schema::create('kendaraan', function (Blueprint $table) {
            $table->id();
            $table->enum('kategori_kendaraan', ['Mobil', 'Minibus', 'Pickup']);
            $table->string('gambar_url');
            $table->string('merek_model');
            $table->integer('kapasitas_kursi');
            $table->enum('jenis_transmisi', ['Manual', 'Automatic']);
            $table->integer('tahun_produksi');
            $table->string('nomor_polisi')->unique();
            $table->enum('status_ketersediaan', ['Tersedia', 'Disewa', 'Perawatan']);
            $table->decimal('harga_sewa_per_periode', 10, 2);
            $table->text('kondisi_fasilitas');
            $table->foreignId('lokasi_garasi_id')->constrained('lokasi_garasi')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('total_harga_sewa', 10, 2);
            $table->enum('status_pemesanan', ['Menunggu Pembayaran','Menunggu Konfirmasi', 'Dikonfirmasi', 'Sedang dalam Penggunaan', 'Dibatalkan', 'Selesai']);
            $table->timestamps();
        });

        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->onDelete('cascade');
            $table->enum('metode_pembayaran', ['Transfer Bank', 'Kartu Kredit', 'E-Wallet']);
            $table->decimal('jumlah_pembayaran', 10, 2);
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->enum('status_pembayaran', ['Lunas', 'Belum Lunas', 'Pending']);
            $table->decimal('deposit_keamanan', 10, 2);
            $table->text('bukti_pembayaran')->nullable();
            $table->timestamps();
        });

        Schema::create('kontrak_sewa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')->constrained('pemesanan')->onDelete('cascade');
            $table->text('link_kontrak');
            $table->enum('status_kontrak', ['Aktif', 'Selesai']);
            $table->timestamps();
        });

        Schema::create('pelacakan_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->string('lokasi_mobil_digunakan');
            $table->text('status_kondisi_setelah_sewa');
            $table->timestamps();
        });

        Schema::create('perawatan_kendaraan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kendaraan_id')->constrained('kendaraan')->onDelete('cascade');
            $table->date('tanggal_perawatan');
            $table->string('jenis_perawatan');
            $table->decimal('biaya_perawatan', 10, 2);
            $table->string('bengkel_teknisi');
            $table->text('catatan_tambahan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('perawatan_kendaraan');
        Schema::dropIfExists('pelacakan_kendaraan');
        Schema::dropIfExists('kontrak_sewa');
        Schema::dropIfExists('pembayaran');
        Schema::dropIfExists('pemesanan');
        Schema::dropIfExists('kendaraan');
        Schema::dropIfExists('users');
    }
}
