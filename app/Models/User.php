<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    use HasFactory, HasApiTokens,SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    const DELETED_AT = 'archived_at';
    protected $guarded=[];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function usertype()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function likedCommentsForProduct($productId)
    {
        return $this->hasManyThrough(CommentLike::class, Comment::class, 'id', 'comment_id')
                    ->where('is_like', true)
                    ->where('commentable_id', $productId)
                    ->where('commentable_type', Product::class);
    }

    public function dislikedCommentsForProduct($productId)
    {
        return $this->hasManyThrough(CommentLike::class, Comment::class, 'id', 'comment_id')
                    ->where('is_like', false)
                    ->where('commentable_id', $productId)
                    ->where('commentable_type', Product::class);
    }


    public function likedCommentsForPost($postId)
    {
        return $this->hasManyThrough(CommentLike::class, Comment::class, 'id', 'comment_id')
                    ->where('is_like', true)
                    ->where('commentable_id', $postId)
                    ->where('commentable_type', Post::class);
    }

    public function dislikedCommentsForPost($postId)
    {
        return $this->hasManyThrough(CommentLike::class, Comment::class, 'id', 'comment_id')
                    ->where('is_like', false)
                    ->where('commentable_id', $postId)
                    ->where('commentable_type', Post::class);
    }

}
