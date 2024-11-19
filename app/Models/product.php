<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    use HasFactory;
    protected $fillable =[ 'Product_name','section_id','description'];
    // protected $quarded;
    public function section(){
        return $this->belongsTo(sections::class);
    }
}
