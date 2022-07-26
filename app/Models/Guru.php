<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mehradsadeghi\FilterQueryString\FilterQueryString;

class Guru extends Model
{
    use HasFactory, FilterQueryString;

    protected $table = 'guru';

    protected $fillable = [
        'nip',
        'nama_guru',
        'foto',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
    ];

    protected $filters = [
        'nip',
        'nama_guru',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'sort'
    ];
}
