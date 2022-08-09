<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;
use Nicolaslopezj\Searchable\SearchableTrait;

class Siswa extends Model
{
    use HasFactory, FilterQueryString, SearchableTrait;

    protected $table = 'siswa';

    protected $casts = [
        'kelas_id' => 'integer',
        'user_id' => 'integer',
    ];

    protected $fillable = [
        'nis',
        'nama_siswa',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'foto_siswa',
        'kelas_id',
        'phone_number'
    ];

    protected $filters = [
        'nis',
        'nama_siswa',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'foto_siswa',
        'kelas_id',
        'phone_number'
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
            'nis' => 10,
            'nama_siswa' => 10,
        ],
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
