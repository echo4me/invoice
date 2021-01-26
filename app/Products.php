<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'Product_name',
        'description',
        'section_id'
    ];

    //protected $guarded =[]; // it will be as fillable 

    public function section()
    {
        // Get me the parent of this Table one to many 
        // one is Sections name , many are products
        return $this->belongsTo('App\Sections');
    }
}
