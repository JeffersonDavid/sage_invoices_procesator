<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $primaryKey = 'id';
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'updated_date';

    protected $fillable = [
        'invoice_number',
        'invoice_name',
        'invoice_api_name',
        'invoice_download_url',
        'send_staus',
        'shipment_date',
        'creation_date',
        'updated_date',
        'token_call',
        'refresh_token_call'
    ];


}
