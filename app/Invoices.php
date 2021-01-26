<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoices extends Model
{
    use SoftDeletes; 

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'due_date',
        'amount_commission',
        'amount_collection',
        'product',
        'section_id',
        'discount',
        'value_vat',
        'rate_vat',
        'total',
        'status',
        'value_status',
        'note',
        'payment_date',
    ];


    //method Relation
    public function section()
    {
        // Get me the parent of this Table one to many 
        // one is Sections name , many are products
        return $this->belongsTo('App\Sections');
    }
}
