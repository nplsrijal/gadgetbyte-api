<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PostCategory extends Model implements Auditable
{
    use HasFactory,SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    const DELETED_AT = 'archived_at';
    protected $guarded=[];

    /**
     * Get the parent category of this category.
     */
    public function parentCategory()
    {
        return $this->belongsTo(PostCategory::class, 'parent_id');
    }

    /**
     * Get the subcategories of this category.
     */
    public function subcategories()
    {
        return $this->hasMany(PostCategory::class, 'parent_id');
    }
    
    public function categoryposts()
    {
        return $this->hasMany(Post::class, 'category_id');
    }

    /**
     * Get the posts for the subcategory.
     */
    public function subcategoryPosts()
    {
        return $this->hasMany(Post::class, 'sub_category_id');
    }
}
