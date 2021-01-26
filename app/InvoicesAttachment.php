<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicesAttachment extends Model
{
    protected $fillable = [
        'file_name',
        'invoice_number',
        'created_by',
        'invoice_id ',
        
    ];
}
