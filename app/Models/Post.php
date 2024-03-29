<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Post extends Model implements Auditable
{
    use HasFactory,SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    const DELETED_AT = 'archived_at';
    protected $guarded=[];

    public function post_category()
    {
        return $this->belongsTo(PostCategory::class, 'category_id');
    }

    public function post_sub_category()
    {
        return $this->belongsTo(PostCategory::class, 'sub_category_id');
    }

    public function prices()
    {
        return $this->hasMany(PostPrice::class);
    }
    public function post_tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags', 'post_id', 'tag_id')->where('post_tags.archived_at',null);
    }


    public function tags()
    {
        return $this->hasMany(PostTag::class);
    }
    public function reviews()
    {
        return $this->hasMany(PostReview::class);
    }
  

}