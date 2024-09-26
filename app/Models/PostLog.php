<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class PostLog extends Model
{
    use HasFactory,SoftDeletes;
    const DELETED_AT = 'archived_at';
    protected $guarded=[];

    
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
