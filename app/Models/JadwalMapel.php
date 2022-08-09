<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class JadwalMapel extends Model
{
    use HasFactory, FilterQueryString;

    protected $table = 'jadwal_mapel';

    protected $casts = [
        'kelas_id' => 'integer',
        'guru_id' => 'integer',
        'mata_pelajaran_id' => 'integer'
    ];

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mata_pelajaran_id'
    ];

    protected $filters = [
        'guru_id',
        'kelas_id',
        'mata_pelajaran_id'
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class);
    }

    public function mata_pelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
}
