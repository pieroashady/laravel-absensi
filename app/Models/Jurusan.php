<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Jurusan extends Model
{
    use HasFactory, FilterQueryString;

    protected $table = 'jurusan';

    protected $fillable = [
        'nama_jurusan'
    ];

    protected $filters = [
        'nama_jurusan'
    ];
}
