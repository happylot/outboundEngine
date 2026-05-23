<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutboundKbEntry extends Model
{
    protected $fillable = ['type', 'title', 'content', 'tags'];
}
