<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HQItem extends Model
{
    use HasFactory;
    protected $table = 'Item' ;
    protected $connection = 'sqlsrv_rms';
    protected $hidden = ['DBTimeStamp'];

    public function department()
    {
        return $this->belongsTo(Department::class , 'DepartmentID' , 'ID');
    }

    public function category()
    {
        return $this->belongsTo(Category::class , 'CategoryID' , 'ID');
    }
}
