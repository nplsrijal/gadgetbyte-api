<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ProductVariant extends Model implements Auditable
{
    use HasFactory,SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    const DELETED_AT = 'archived_at';
    protected $guarded=[];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
    
    public function variantAttributes()
    {
        return $this->hasMany(ProductVariantAttribute::class, 'variant_slug', 'slug');
    }

    public function variantVendors()
    {
        return $this->hasMany(ProductVariantVendor::class, 'variant_slug', 'slug');
    }
}
