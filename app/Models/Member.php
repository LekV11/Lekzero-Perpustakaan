<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'member_id',
        'address',
        'phone',
        'email',
    ];

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
