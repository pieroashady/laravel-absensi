<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalMapel extends Model
{
    use HasFactory;

    protected $table = 'jadwal_mapel';

    protected $fillable = [
        'guru_id',
        'kelas_id',
        'mata_pelajaran_id'
    ];
}
