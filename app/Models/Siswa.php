<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Siswa extends Model
{
    use HasFactory, FilterQueryString;

    protected $table = 'siswa';

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

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
