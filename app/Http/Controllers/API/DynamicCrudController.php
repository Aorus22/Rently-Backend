<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Model;

class DynamicCrudController extends Controller
{
    // Fetch table config
    private function getTableConfig()
    {
        return [
            "User" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "status_blokir" => ["type" => "select", "options" => ["Ya", "Tidak"]]
                ],
                "detail_view" => true,
                "validation" => [
                    "status_blokir" => "required|in:Ya,Tidak",
                ]
            ],
            "Kendaraan" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "kategori_kendaraan" => ["type" => "select", "options" => ["Mobil", "Minibus", "Pickup"]],
                    "gambar_url" => ["type" => "text"],
                    "merek_model" => ["type" => "text"],
                    "kapasitas_kursi" => ["type" => "number"],
                    "jenis_transmisi" => ["type" => "select", "options" => ["Manual", "Automatic"]],
                    "tahun_produksi" => ["type" => "number"],
                    "nomor_polisi" => ["type" => "text"],
                    "status_ketersediaan" => ["type" => "select", "options" => ["Tersedia", "Disewa", "Perawatan"]],
                    "harga_sewa_per_periode" => ["type" => "number"],
                    "kondisi_fasilitas" => ["type" => "text"],
                    "lokasi_kendaraan" => ["type" => "text"]
                ],
                "validation" => [
                    "kategori_kendaraan" => "required|in:Mobil,Minibus,Pickup",
                    "gambar_url" => "required|string",
                    "merek_model" => "required|string",
                    "kapasitas_kursi" => "required|integer",
                    "jenis_transmisi" => "required|in:Manual,Automatic",
                    "tahun_produksi" => "required|integer",
                    "nomor_polisi" => "required|string|unique:kendaraan",
                    "status_ketersediaan" => "required|in:Tersedia,Disewa,Perawatan",
                    "harga_sewa_per_periode" => "required|numeric",
                    "kondisi_fasilitas" => "required|string",
                    "lokasi_kendaraan" => "required|string"
                ],
                "detail_view" => true
            ],
            "Pemesanan" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "user_id" => [
                        "type" => "select",
                        "options" => $this->getForeignOptions('users', 'id', 'nama_lengkap')
                    ],
                    "kendaraan_id" => [
                        "type" => "select",
                        "options" => $this->getForeignOptions('kendaraan', 'id', 'merek_model')
                    ],
                    "tanggal_mulai" => ["type" => "date"],
                    "tanggal_selesai" => ["type" => "date"],
                    "total_harga_sewa" => ["type" => "number"],
                    "status_pemesanan" => ["type" => "select", "options" => [
                        "Menunggu Pembayaran", "Menunggu Konfirmasi", "Dikonfirmasi",
                        "Sedang dalam Penggunaan", "Dibatalkan", "Selesai"
                    ]]
                ],
                "validation" => [
                    "user_id" => "required|exists:users,id",
                    "kendaraan_id" => "required|exists:kendaraan,id",
                    "tanggal_mulai" => "required|date",
                    "tanggal_selesai" => "required|date|after_or_equal:tanggal_mulai",
                    "total_harga_sewa" => "required|numeric",
                    "status_pemesanan" => "required|in:Menunggu Pembayaran,Menunggu Konfirmasi,Dikonfirmasi,Sedang dalam Penggunaan,Dibatalkan,Selesai"
                ],
                "detail_view" => true
            ],
            "Pembayaran" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "pemesanan_id" => [
                        "type" => "select",
                        "options" => $this->getForeignOptions('pemesanan', 'id', 'id')
                    ],
                    "metode_pembayaran" => ["type" => "select", "options" => ["Transfer Bank", "Kartu Kredit", "E-Wallet"]],
                    "jumlah_pembayaran" => ["type" => "number"],
                    "tanggal_pembayaran" => ["type" => "date"],
                    "status_pembayaran" => ["type" => "select", "options" => ["Lunas", "Belum Lunas", "Pending"]],
                    "deposit_keamanan" => ["type" => "number"],
                    "bukti_pembayaran" => ["type" => "text"]
                ],
                "validation" => [
                    "pemesanan_id" => "required|exists:pemesanan,id",
                    "metode_pembayaran" => "required|in:Transfer Bank,Kartu Kredit,E-Wallet",
                    "jumlah_pembayaran" => "required|numeric",
                    "tanggal_pembayaran" => "nullable|date",
                    "status_pembayaran" => "required|in:Lunas,Belum Lunas,Pending",
                    "deposit_keamanan" => "required|numeric",
                    "bukti_pembayaran" => "nullable|string"
                ],
                "detail_view" => true
            ],
            "KontrakSewa" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "pemesanan_id" => [
                        "type" => "select",
                        "options" => $this->getForeignOptions('pemesanan', 'id', 'id')
                    ],
                    "link_kontrak" => ["type" => "text"],
                    "status_kontrak" => ["type" => "select", "options" => ["Aktif", "Selesai"]]
                ],
                "validation" => [
                    "pemesanan_id" => "required|exists:pemesanan,id",
                    "link_kontrak" => "required|string",
                    "status_kontrak" => "required|in:Aktif,Selesai"
                ],
                "detail_view" => true
            ],
            "PelacakanKendaraan" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "kendaraan_id" => [
                        "type" => "select",
                        "options" => $this->getForeignOptions('kendaraan', 'id', 'merek_model')
                    ],
                    "lokasi_mobil_digunakan" => ["type" => "text"],
                    "status_kondisi_setelah_sewa" => ["type" => "text"]
                ],
                "validation" => [
                    "kendaraan_id" => "required|exists:kendaraan,id",
                    "lokasi_mobil_digunakan" => "required|string",
                    "status_kondisi_setelah_sewa" => "required|string"
                ],
                "detail_view" => true
            ],
            "PerawatanKendaraan" => [
                "can_create" => true,
                "can_read" => true,
                "can_update" => true,
                "can_delete" => true,
                "editable_columns" => [
                    "kendaraan_id" => [
                        "type" => "select",
                        "options" => $this->getForeignOptions('kendaraan', 'id', 'merek_model')
                    ],
                    "tanggal_perawatan" => ["type" => "date"],
                    "jenis_perawatan" => ["type" => "text"],
                    "biaya_perawatan" => ["type" => "number"],
                    "bengkel_teknisi" => ["type" => "text"],
                    "catatan_tambahan" => ["type" => "text"]
                ],
                "validation" => [
                    "kendaraan_id" => "required|exists:kendaraan,id",
                    "tanggal_perawatan" => "required|date",
                    "jenis_perawatan" => "required|string",
                    "biaya_perawatan" => "required|numeric",
                    "bengkel_teknisi" => "required|string",
                    "catatan_tambahan" => "nullable|string"
                ],
                "detail_view" => true
            ]
        ];
    }

    private function getForeignOptions($table, $valueField, $labelField) {
        $modelClass = 'App\\Models\\' . ucfirst($table);
        if (!class_exists($modelClass)) return [];

        return $modelClass::select($valueField, $labelField)
            ->get()
            ->map(function ($item) use ($valueField, $labelField) {
                return [
                    'value' => $item->$valueField,
                    'label' => $item->$labelField
                ];
            });
    }


    // Get model instance dynamically
    private function getModelInstance($table)
    {
        $modelClass = 'App\\Models\\' . ucfirst($table);
        return class_exists($modelClass) ? new $modelClass() : null;
    }

    // Get validation rules from config
    private function getValidationRules($table, $isUpdate = false)
    {
        $config = $this->getTableConfig();
        $rules = $config[$table]['validation'] ?? [];

        if ($isUpdate) {
            foreach ($rules as $key => $rule) {
                // Modify required fields to sometimes for update
                $rules[$key] = str_replace('required', 'sometimes', $rule);
            }
        }

        return $rules;
    }


    // List all records
    public function index($table)
    {
        $model = $this->getModelInstance($table);
        if (!$model) return response()->json(['message' => 'Table not found'], 404);

        return response()->json($model->all());
    }

    // Store new record
    public function store(Request $request, $table)
    {
        $config = $this->getTableConfig();
        if (!($config[$table]['can_create'] ?? false)) {
            return response()->json(['message' => 'Creation not allowed'], 403);
        }

        $model = $this->getModelInstance($table);
        if (!$model) return response()->json(['message' => 'Table not found'], 404);

        $rules = $this->getValidationRules($table);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $record = $model->create($request->all());
        return response()->json(['message' => 'Record created successfully!', 'data' => $record]);
    }

    // Show specific record
    public function show($table, $id)
    {
        $config = $this->getTableConfig();
        if (!($config[$table]['can_read'] ?? false)) {
            return response()->json(['message' => 'Read not allowed'], 403);
        }

        $model = $this->getModelInstance($table);
        if (!$model) return response()->json(['message' => 'Table not found'], 404);

        $record = $model->find($id);
        if (!$record) return response()->json(['message' => 'Record not found'], 404);

        return response()->json($record);
    }

    // Update record
    public function update(Request $request, $table, $id)
    {
        $config = $this->getTableConfig();
        if (!($config[$table]['can_update'] ?? false)) {
            return response()->json(['message' => 'Update not allowed'], 403);
        }

        $model = $this->getModelInstance($table);
        if (!$model) return response()->json(['message' => 'Table not found'], 404);

        $record = $model->find($id);
        if (!$record) return response()->json(['message' => 'Record not found'], 404);

        $rules = $this->getValidationRules($table, true);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $record->update($request->all());
        return response()->json(['message' => 'Record updated successfully!', 'data' => $record]);
    }

    // Delete record permanently
    public function destroy($table, $id)
    {
        $config = $this->getTableConfig();
        if (!($config[$table]['can_delete'] ?? false)) {
            return response()->json(['message' => 'Deletion not allowed'], 403);
        }

        $model = $this->getModelInstance($table);
        if (!$model) return response()->json(['message' => 'Table not found'], 404);

        $record = $model->find($id);
        if (!$record) return response()->json(['message' => 'Record not found'], 404);

        $record->delete();
        return response()->json(['message' => 'Record deleted successfully!']);
    }
}
