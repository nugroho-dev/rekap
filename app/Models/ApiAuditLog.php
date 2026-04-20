<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiAuditLog extends Model
{
    use HasFactory;

    protected $table = 'api_audit_logs';

    protected $guarded = ['id'];

    protected $casts = [
        'authenticated' => 'boolean',
        'query_params' => 'array',
        'request_payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}