<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Comment extends Model implements Auditable
{
    use HasFactory,SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    const DELETED_AT = 'archived_at';
    protected $guarded=[];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function likes()
    {
        return $this->hasMany(CommentLike::class);
    }

    public function likesCount()
    {
        return $this->likes()->where('is_like', true)->count();
    }

    public function dislikesCount()
    {
        return $this->likes()->where('is_like', false)->count();
    }
}
