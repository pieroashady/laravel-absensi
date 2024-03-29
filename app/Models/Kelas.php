<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Kelas extends Model
{
    use HasFactory, FilterQueryString;

    protected $table = 'kelas';

    protected $casts = [
        'jurusan_id' => 'integer',
    ];

    protected $fillable = [
        'nama_kelas',
        'jurusan_id'
    ];

    protected $filters = [
        'nama_kelas',
        'jurusan_id',
        'sort'
    ];

    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class);
    }
}
