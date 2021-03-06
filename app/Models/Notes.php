<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notes extends Model
{
    use HasFactory;
    
    protected $fillable = [
      'title', 'body', 'updated_by', 'user_id', 'file_attachments'
    ];
}
