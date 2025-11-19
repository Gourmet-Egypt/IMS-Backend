<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'Category';

    protected $connection = 'sqlsrv_rms';
    protected $hidden = ['DBTimeStamp'];


    public function item()
    {
        return $this->hasMany(Item::class, 'CategoryID', 'ID');
    }
}
