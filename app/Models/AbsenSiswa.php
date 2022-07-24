<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Nicolaslopezj\Searchable\SearchableTrait;

class AbsenSiswa extends Model
{
    use HasFactory, FilterQueryString, SearchableTrait;

    protected $table = 'absen_siswa';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'keterangan',
    ];

    protected $filters = [
        'siswa_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'keterangan',
        'sort'
    ];

    protected $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'siswa.nis' => 10,
            'siswa.nama_siswa' => 10,
        ],
        'joins' => [
            'siswa' => ['siswa.id', 'absen_siswa.siswa_id'],
        ],
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
