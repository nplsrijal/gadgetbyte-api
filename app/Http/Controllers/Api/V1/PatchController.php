<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\Models\PostReview;
use App\Models\Post;
use App\Models\PostFaq;
use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\ProductAttribute;
use App\Models\ProductPost;
use App\Models\Product;
use App\Models\Specification;
use App\Models\ProductSpecification;
use App\Models\Vendor;
use App\Models\ProductVariant;
use App\Models\ProductVariantAttribute;
use App\Models\ProductVariantVendor;
use App\Models\ProductVariation;
use App\Models\ProductImage;



class PatchController extends Controller
{
    //

    public function post_reviews()
    {
        $review_data = DB::table('wp_reviews')->where('id','>','4046')->where('id','<','4057')->orderBy('id', 'asc')->get();

        foreach($review_data as $data)
        {
            $a_serializedData = stripslashes($data->reviews);

            
            // Unserialize the data
            $unserializedData = unserialize($a_serializedData);

            // Check if unserialization was successful
            if ($unserializedData !== false) {
                // Access individual elements as needed'
                if(isset($unserializedData['has_review']) && $unserializedData['has_review']=='rate_point')
                {
                    $type=$unserializedData['p_review_points'];
                    $rev_id='1';
                }
                else if(isset($unserializedData['has_review']) && $unserializedData['has_review']=='rate_stars')
                {
                    $type=$unserializedData['p_review_stars'];
                    $rev_id='2';

                }
                else if(isset($unserializedData['p_review_stars']))
                {
                    $type=$unserializedData['p_review_stars'];
                    $rev_id='2';

                }
                else if(isset($unserializedData['p_review_points']))
                {
                    $type=$unserializedData['p_review_points'];
                    $rev_id='1';

                }
                $reviewStars = $type;

                // Iterate over the review stars array
                foreach ($reviewStars as $review) {
                    $desc = $review['desc']; // Description of the review
                    $rate = @$review['rate']; // Rating of the review
                  //  echo "Product: $desc, Rating: $rate <br>";
                  if($rate !='')
                  {
                    $ins=array(
                        'post_id'=>$data->post_id,
                        'review_id'=>$rev_id,
                        'title'=>$review['desc'],
                        'review'=>$review['rate']
                    );
                  }
                    

                    PostReview::create($ins);
                }
                if(isset($unserializedData['review']))
                {
                    $ins=array(
                        'post_id'=>$data->post_id,
                        'review_id'=>4,
                        'title'=>'Summary',
                        'review'=>$unserializedData['review']
                    );
    
                    PostReview::create($ins);
                }
                
               // echo "Summary: ".$unserializedData['review'];
               echo $data->post_id.' -> '.$data->id.'<br/>';

            } else {
                // Handle case where unserialization failed
                echo "Error: Unable to unserialize data.";
            }
      }




    }

    public function post_gallery(Request $request)
    {
        
            $page = $request->input('page', 1);

            // Define limit and calculate offset based on the page number
            $limit = 200;
            $offset = ($page - 1) * $limit;
            $sequence=0;

            // Fetch posts with limit and offset
            $posts = Post::where('description', 'like', '%gallery%')
                        ->offset($offset)
                        ->limit($limit)
                        ->get();

            $totalpost=count($posts);
            foreach ($posts as $post) {
                $sequence++;
                $postid = $post->id;
                $content = $post->description;

                preg_match_all("/\[gallery.*?ids=\"(.*?)\"\]/", $content, $matches);

                if (!empty($matches[1])) {
                    foreach ($matches[1] as $index => $ids) {
                        $explode_ids = explode(',', $ids);
                        $gallery_items = [];

                        foreach ($explode_ids as $id) {
                            $media = DB::table('wp_medias')->where('attachment_id', (int)$id)->first();

                            if ($media) {
                                $gallery_items[] = [
                                    'title' => $media->caption,
                                    'image_path' => 'http://45.117.153.85:8002/'.$media->attachment_file_path,
                                ];
                            } else {
                                // Handle case where media is not found
                                // For example: Log an error or skip the ID
                            }
                        }

                        $gallery_json = json_encode($gallery_items);
                        $content = str_replace($matches[0][$index], $gallery_json, $content);
                    }
                }

                if(Post::where('id', $postid)->update(['description' => $content]))
                {
                    echo $sequence.' Done ->id '.$postid.'<br/>';

                }
                else
                {
                    echo $sequence.' Error ->id '.$postid.'<br/>';

                }
            }

           
    }

    public function post_faq()
    {
        $posts = Post::where('description', 'like', '%[su_accordion%')->get();

        foreach($posts as $list)
        {
             // Extract content within each su_spoiler tag
           preg_match_all('/\[su_spoiler title="(.*?)"[^]]*\](.*?)\[\/su_spoiler\]/s', $list->description, $matches, PREG_SET_ORDER);
            
            
            // Loop through each match and extract title and content
            foreach ($matches as $match) {
                $data=['question'=>$match[1],'answer'=>$match[2],'post_id'=>$list->id,'created_by'=>1];
                PostFaq::create($data);
            }
             $content = preg_replace('/\[su_accordion\].*?\[\/su_accordion\]/s', '[faq]', $list->description);

            if(Post::where('id', $list->id)->update(['description' => $content]))
            {
                echo ' Done ->id '.$list->id.'<br/>';

            }
            else
            {
                echo 'Error ->id '.$list->id.'<br/>';

            }

        }
          

    }

    public function product()
    {
        $review_data = DB::table('product_wp')->where('wp_id','122068')->orderBy('wp_id', 'asc')->get();

        foreach($review_data as $data)
        {
            //var_Dump($data->productattribute);
            // $data_attr = json_decode($data->productattribute, true);
            // var_dump($data_attr);exit;
            $a_serializedData = stripslashes($data->productattribute);


            $attributes = unserialize($a_serializedData);
           // var_Dump($attributes);exit;

            $product=$data->post_name;
            $productAttributes = [];
            foreach ($attributes as $taxonomy => $attribute) {
                $productAttributes = $this->extracted($taxonomy, $data->wp_id, $attribute, $productAttributes);
            }
            //$data->productAttribute = $productAttributes;
            //$data->specs = $this->getSpecsForProduct($data->wp_id);
           // $data->child = $this->getProductVariant($data->wp_id);

            unset($data->productattribute);

        }
        echo json_encode($review_data);exit;
    }
    public function getSpecsForProduct($productId)
    {
        $review_data = DB::table('specification_wp')->where('post_id',$productId)->orderBy('meta_key', 'asc')->get();

       

        $results = $review_data;

        $groupedResults = [];
        foreach ($results as $row) {
            if (preg_match('/specifications_(\d+)_sub_specifications_(\d+)_(.+)/', $row->meta_key, $matches)) {
                $groupIndex = $matches[1];
                $subIndex = $matches[2];
                $attribute = $matches[3];

                if (!isset($groupedResults[$groupIndex])) {
                    $groupedResults[$groupIndex] = [];
                }

                if (!isset($groupedResults[$groupIndex][$subIndex])) {
                    $groupedResults[$groupIndex][$subIndex] = [];
                }

                // Check if the attribute starts with 'spec_value'
                if (strpos($attribute, 'spec_value') === 0) {
                    // If it does, add the meta_value to an array under the 'spec_values' key
                    if (strpos($row->meta_value, 'a:') === 0) {
                        
                    } else {
                        $groupedResults[$groupIndex][$subIndex]['specs_child'][] = $row->meta_value;
                    }
                } else {
                    $groupedResults[$groupIndex][$subIndex][$attribute.'_child'] = $row->meta_value;
                }
            } elseif (preg_match('/specifications_(\d+)_specifications_group_title/', $row->meta_key, $matches)) {
                $groupIndex = $matches[1];
                $groupedResults[$groupIndex]['group_title'] = $row->meta_value;
            }
        }

      
        // Convert the associative array to an indexed array and sort it
        $groupedResults = array_values($groupedResults);

       

        // Convert the 'specs' object into an array
        $finalArray = [];
        foreach ($groupedResults as $key => $value) {
           // var_dump($key,$value);
            $groupValues = [];
            foreach ($value as $k => $v) {
                if ($k !== 'group_title') {
                    $groupValues[] = $v;
                }
            }
            $finalArray[] = ['attribute_master' => $value['group_title'], 'attribute_value' => $groupValues];
        }

        return $finalArray;
    }

    public function extracted(int|string $taxonomy, int $product, mixed $attribute, array $productAttributes): array
    {
        $terms = DB::table('taxanomy_wp')
            
            ->where('taxonomy', $taxonomy)
            ->where('object_id', $product)
            ->select('name')
            ->get();
        if (!empty($terms)) {
            $attribute['value'] = [];
            foreach ($terms as $term) {
                $attribute['value'][] = $term->name;
            }
        }
        $productAttributes[] = $attribute;
        return $productAttributes;
    }
    public function getProductVariant($productId)
    {
        $query = DB::table('variant_wp')
            ->where('wp_id', $productId);

        $results = $query->get();

        $brandData = DB::table('brand_wp')
            ->first();
        $a_serializedData = stripslashes($brandData->post_content);
       

      // $data_attr = json_decode($a_serializedData, true);

        $brandChoices = unserialize($a_serializedData)['choices'];

        $groupedResults = [];
        foreach ($results as $row) {
            if (preg_match('/external_prices_group_(\d+)_variant(?:s_(\d+))?_(.+)/', $row->meta_key, $matches)) {
                $groupIndex = $matches[1];
                $variantIndex = isset($matches[2]) ? $matches[2] : 0; // Use 0 as the default variant index
                $attribute = $matches[3];

                if (!isset($groupedResults[$groupIndex])) {
                    $groupedResults[$groupIndex] = [];
                }

                if (!isset($groupedResults[$groupIndex][$variantIndex])) {
                    $groupedResults[$groupIndex][$variantIndex] = [];
                }

                if (!isset($groupedResults[$groupIndex][$variantIndex][$attribute])) {
                    $groupedResults[$groupIndex][$variantIndex][$attribute] = [];
                }

                $groupedResults[$groupIndex][$variantIndex][$attribute][] = $row->meta_value;

                if ($attribute == 'brand' && isset($brandChoices[end($groupedResults[$groupIndex][$variantIndex][$attribute])])) {
                    end($groupedResults[$groupIndex][$variantIndex][$attribute]);
                    $lastKey = key($groupedResults[$groupIndex][$variantIndex][$attribute]);
                    $groupedResults[$groupIndex][$variantIndex][$attribute][$lastKey] = $brandChoices[$groupedResults[$groupIndex][$variantIndex][$attribute][$lastKey]];
                }
            }
        }

        // Convert the grouped results to a more readable format
        $readableResults = [];$test=[];
        foreach ($groupedResults as $groupIndex => $variants) {
            $group = ['group' => $groupIndex, 'variants' => []];
            foreach ($variants as $variantIndex => $attributes) {
               // if($variantIndex=='')
                $variant = ['variant' => $variantIndex];

                foreach ($attributes as $attribute => $values) {
                    if ($attribute == 'size' || $attribute == 'size_storage') {
                        // Fetch the actual term value from the 'wp_terms' table
                        if (!empty($values) && isset($values[0]) && $values[0]!='') {
                            $termValue = DB::table('terms_wp')
                                ->where('term_id', $values[0])
                                ->value('name');
                            $variant[$attribute] = $termValue;
                            if($attribute=='size')
                            {
                                $test['ram'][]=$termValue;

                            }
                            else
                            {
                                $test['storage'][]=$termValue;

                            }

                        }
                    }  else {
                        //if($variantIndex=='')
                        $variant[$attribute] = implode(', ', $values);
                    }
                }

                if(isset($variant))
                $group['variants'][] = $variant;
            }
            $readableResults[] = $group;
        }
        

        return $readableResults;
    }

    function patch_attributes()
    {
        ini_set('max_execution_time', 0);
        $results = DB::table('product_wp')
        ->whereNotIn('wp_id', function ($query) {
            $query->select('product_id')
                  ->distinct()
                  ->from('product_attributes');
        })
        ->limit(40)
        ->pluck('wp_id');
        foreach($results as $data)
        {
           
            $save = $this->getAtributes($data);
            echo 'Productid > '.$data.' _ '.$save.'<br/>';


        }
        

    }

    public function getAtributes($productId)
    {
        $review_data = DB::table('specification_wp')->where('post_id',$productId)->orderBy('meta_key', 'asc')->get();

       

        $results = $review_data;

        $groupedResults = [];
        foreach ($results as $row) {
            if (preg_match('/specifications_(\d+)_sub_specifications_(\d+)_(.+)/', $row->meta_key, $matches)) {
                $groupIndex = $matches[1];
                $subIndex = $matches[2];
                $attribute = $matches[3];

                if (!isset($groupedResults[$groupIndex])) {
                    $groupedResults[$groupIndex] = [];
                }

                if (!isset($groupedResults[$groupIndex][$subIndex])) {
                    $groupedResults[$groupIndex][$subIndex] = [];
                }

                // Check if the attribute starts with 'spec_value'
                if (strpos($attribute, 'spec_value') === 0) {
                    // If it does, add the meta_value to an array under the 'spec_values' key
                    if (strpos($row->meta_value, 'a:') === 0) {
                        
                    } else {
                        $groupedResults[$groupIndex][$subIndex]['specs_child'][] = $row->meta_value;
                    }
                } else {
                    $groupedResults[$groupIndex][$subIndex][$attribute.'_child'] = $row->meta_value;
                }
            } elseif (preg_match('/specifications_(\d+)_specifications_group_title/', $row->meta_key, $matches)) {
                $groupIndex = $matches[1];
                $groupedResults[$groupIndex]['group_title'] = $row->meta_value;
            }
        }

      
        // Convert the associative array to an indexed array and sort it
        $groupedResults = array_values($groupedResults);

       
        // Begin database transaction
        DB::beginTransaction();
        // Convert the 'specs' object into an array
        $finalArray = [];
        foreach ($groupedResults as $key => $value) {
           // var_dump($key,$value);
            $groupValues = [];
            foreach ($value as $k => $v) {
                if ($k !== 'group_title') {
                    $groupValues[] = $v;
                }
            }

            $check_attribute=Attribute::where('name',$value['group_title'])->first();
            if($check_attribute)
            {
                $id=$check_attribute->id;
            }
            else
            {
                $attribute_data=array('name'=>$value['group_title'],'slug'=>$this->createSlug($value['group_title']),'is_active'=>'Y','type'=>'product');
                $store_attribute = Attribute::create($attribute_data);
               $id= $store_attribute->id;
            }

            
           //$id=38;



           foreach($groupValues as $li)
           {
            if(isset($li['specs_child']))
            {
                $check_attributeoption=AttributeOption::where('name',$li['title_child'])->where('attribute_id',$id)->first();

                if($check_attributeoption)
                {
                    $ao_id=$check_attributeoption->id;
                }
                else
                {
                    $option_data=array('name'=>$li['title_child'],'attribute_id'=>@$id,'values'=>json_encode($li['specs_child']),'is_active'=>'Y');
                    $data = AttributeOption::create($option_data);
                    $ao_id=$data->id;
                }
               

                $insert_attr = [
                    'product_id' => $productId,
                    'attribute_option_id' => $ao_id,
                    'attribute_name' => $li['title_child'],
                    'values' => json_encode($li['specs_child']) // Ensure values are encoded if they are arrays
                ];

                ProductAttribute::create($insert_attr);

            }
           }
            //$finalArray[] = ['attribute_master' => $value['group_title'], 'attribute_value' => $groupValues];
        }

        // Check database transaction
        $transactionStatus = DB::transactionLevel();

        if ($transactionStatus > 0) {
            // Database transaction success
            DB::commit();
            return 'Success';
           } else {
            // Throw error
            DB::rollBack();
            return 'Failed';
        }
   
       
    }

    function createSlug($string) {
        // Convert the string to lowercase
        $slug = strtolower($string);
        // Replace spaces with hyphens
        $slug = str_replace(' ', '-', $slug);
        // Return the slug
        return $slug;
    }


    public function patch_product_post()
    {
        ini_set('max_execution_time', 0);

        $review_data = DB::table('wp_product_post')->where('product_id','>','169182')->orderBy('id', 'asc')->get();

        foreach($review_data as $data)
        {
            $a_serializedData = stripslashes($data->relatedposts);


            
            // Unserialize the data
            $unserializedData = unserialize($a_serializedData);

            // Check if unserialization was successful
            if ($unserializedData !== false) {

                foreach($unserializedData as $postid)
                {

                    $postId = DB::table('wp_news as n')
                    ->join('posts as p', 'n.post_slug', '=', 'p.slug')
                    ->where('n.wp_id', $postid)
                    ->select('p.id')
                    ->first();
                    if($postId)
                    {
                        $ins=array(
                            'post_id'=>$postId->id,
                            'product_id'=>$data->product_id,
                        );
    
                        ProductPost::create($ins);
                    }

                
                   
                    
               // echo $data->product_id.' -> '.$postid.'<br/>';
               }

            } else {
                // Handle case where unserialization failed
                echo "Error: Unable to unserialize data.";
            }
      }




    }

    function get_spec_name($type)
    {
        $filterMobileMapping = [
            'network' => 'pa_smartphone-network-type',
            'display' => 'pa_smartphone-display-type',
            'cameras' => 'pa_smartphone-cameras-filter',
            'battery' => 'pa_smartphone-battery-filter',
            'grade' => 'pa_smartphone-grade',
            'storage' => 'pa_smartphone-storage',
            'ram' => 'pa_smartphone-ram',
        ];

        $filterLaptopMapping = [
            'type' => 'pa_laptop-type',
            'weight' => 'pa_laptop-weight-range-filter',
            'cpuBrand' => 'pa_laptop-cpu-brand',
            'cpuType' => 'pa_laptop-cpu',
            'gpuBrand' => 'pa_laptop-gpu',
            'display'=>'pa_laptop-display',
            'gpuType' => 'pa_laptop-gpu-brand',
            'displayType' => 'pa_laptop-display-type',
            'displaySize' => 'pa_laptop-display-range',
            'ram' => 'pa_laptop-ram',
            'ssd' => 'pa_laptop-ssd',
            'hdd' => 'pa_laptop-hdd',
            'battery' => 'pa_laptop-battery-filter',
        ];

        $filterSmartwatchMapping = [
            'marketStatus' => 'pa_watch-market-status',
            'type' => 'pa_watch-type',
            'weight' => 'pa_watch-weight',
            'compatibility' => 'pa_watch-compatibility',
            'caseSize' => 'pa_watch-case-size',
            'caseMaterial' => 'pa_watch-case-material',
            'software' => 'pa_watch-software',
            'display' => 'pa_watch-display-filter',
            'phoneCallSupport' => 'pa_watch-phone-call-support',
            'battery' => 'pa_watch-battery-filter',
            'buildInGPS' => 'pa_watch-built-in-gps',
        ];

        $filterEarbudsMapping = [
            'fit' => 'pa_earbuds-type',
            'noiseCancellation' => 'pa_earbuds-noise-cancellation',
            'driver' => 'pa_earbuds-driver',
            'bluetoothVersion' => 'pa_earbuds-connectivity',
            'codecs' => 'pa_earbuds-codecs',
            'protection' => 'pa_earbuds-ingress-protection'
        ];

        $filterTabletMapping = [
            'marketStatus' => 'pa_tablet-market-status',
            'networkType' => 'pa_tablet-network-type',
            'chipsetBrand' => 'pa_tablet-chipset-brand',
            'displayType' => 'pa_tablet-display-type',
            'displaySize' => 'pa_tablet-display-size',
            'ram' => 'pa_tablet-ram',
            'camera' => 'pa_tablet-cameras',
            'storage' => 'pa_tablet-storage',
            'battery' => 'pa_tablet-battery-filter',
        ];

        $filterPcBuildMapping = [
            'cpuSeries' => 'pa_pc-cpu-series',
            'cpu' => 'pa_pc-cpu',
            'graphicsCardSeries' => 'pa_pc-gpu-series',
            'graphicsCardBrand' => 'pa_pc-gpu-brand',
            'graphicsCard' => 'pa_pc-gpu',
            'ramType' => 'pa_pc-ram-type',
            'ram' => 'pa_pc-ram',
            'ssd' => 'pa_pc-ssd'
        ];

       $reciprocalMobileMapping = array_flip($filterMobileMapping);
        $reciprocalLaptopMapping = array_flip($filterLaptopMapping);
        $reciprocalSmartwatchMapping = array_flip($filterSmartwatchMapping);
        $reciprocalEarbudsMapping = array_flip($filterEarbudsMapping);
        $reciprocalTabletMapping = array_flip($filterTabletMapping);
        $reciprocalPcBuildMapping = array_flip($filterPcBuildMapping);

        if(isset($reciprocalMobileMapping[$type]))
        {
            return $reciprocalMobileMapping[$type];
        }
        else if(isset($reciprocalLaptopMapping[$type]))
        {
            return $reciprocalLaptopMapping[$type];
        }
        else if(isset($reciprocalSmartwatchMapping[$type]))
        {
            return $reciprocalSmartwatchMapping[$type];
        }
        else if(isset($reciprocalEarbudsMapping[$type]))
        {
            return $reciprocalEarbudsMapping[$type];
        }
        else if(isset($reciprocalTabletMapping[$type]))
        {
            return $reciprocalTabletMapping[$type];
        }
        else if(isset($reciprocalPcBuildMapping[$type]))
        {
            return $reciprocalPcBuildMapping[$type];
        }
        else
        {
            return $type;
        }

    }

    function patch_specifications()
    {
        ini_set('max_execution_time', 0);
        $results = DB::table('product_wp')->select('wp_id','productattribute','expertrating')
        ->whereNotIn('wp_id', function ($query) {
            $query->select('product_id')
                  ->distinct()
                  ->from('product_specifications');
        })
        ->limit(100)
        ->get();
        DB::beginTransaction();

        foreach($results as $data)
        {
            $a_serializedData = stripslashes($data->productattribute);


            $attributes = unserialize($a_serializedData);

            $productAttributes = [];
            if(is_array($attributes))
            {

            
                foreach ($attributes as $taxonomy => $attribute) {
                    if($attribute['is_visible']=='1')
                    {
                        $productAttributes = $this->extracted($taxonomy, $data->wp_id, $attribute, $productAttributes);

                    }
                }
            }

            foreach($productAttributes as $li)
            {
                $spec_name=$this->get_spec_name($li['name']);
                $check_specification=Specification::where('name',$spec_name)->first();

                if($check_specification)
                {
                    $ao_id=$check_specification->id;
                }
                else
                {
                    $option_data=array('name'=>$spec_name,'image'=>'');
                    $insert_specs = Specification::create($option_data);
                    $ao_id=$insert_specs->id;
                }

                $ins=array(
                    'specification_id'=>$ao_id,
                    'values'=>json_encode($li['value']),
                    'product_id'=>$data->wp_id,
                );

                ProductSpecification::create($ins);
            }


            //echo 'Productid > '.$data.' _ '.$save.'<br/>';

            $product=Product::find($data->wp_id);
            $product->update(['expert_rating'=>$data->expertrating]);


        }
        $transactionStatus = DB::transactionLevel();

        if ($transactionStatus > 0) {
            // Database transaction success
            DB::commit();
            echo 'Success';
           } else {
            // Throw error
            DB::rollBack();
            echo 'Failed';
        }
        

    }

    function patch_variation_vendor()
    {
        ini_set('max_execution_time', 0);
        $results = DB::table('product_wp')->select('wp_id','productimagegallery')
        // ->where('wp_id','159115')
        ->whereNotIn('wp_id', function ($query) {
            $query->select('product_id')
                  ->distinct()
                  ->from('product_variations');
        })
        ->limit(10)
        ->get();
        DB::beginTransaction();

        foreach($results as $data)
        {
           
            $variant = $this->getProductVariant($data->wp_id);
            $final_data=[];
            $ram_data=[];
            $storage_data=[];
            $size_data=[];
            $i=0;
           foreach($variant as $li)
           {
               $variation=$li['variants'];

               foreach($variation as $variation_child)
               {
                    if($variation_child['variant']=='')
                    {
                        if(isset($variation_child['size']) && isset($variation_child['size_storage']))
                        {
                            $finaldata[$i]['ram']=$variation_child['size'];
                            $ram_data[]=$variation_child['size'];
                            $finaldata[$i]['storage']=$variation_child['size_storage'];
                            $storage_data[]=$variation_child['size_storage'];

                            //echo $i.'>Ram is '.$variation_child['size'].'& Storage is '.$variation_child['size_storage'];
                        }
                        else if(isset($variation_child['size']) && empty($variation_child['size_storage']))
                        {
                            $finaldata[$i]['size']=$variation_child['size'];
                            $size_data[]=$variation_child['size'];


                            //echo $i.'>Size is '.$variation_child['size'];
                        }
                        else if(empty($variation_child['size']) && isset($variation_child['size_storage']))
                        {
                            $finaldata[$i]['storage']=$variation_child['size_storage'];
                            $storage_data[]=$variation_child['size_storage'];

                           // echo $i.'>Storage is '.$variation_child['size_storage'];
                        }

                    }
                    else if(strpos($variation_child['price'], 'NPR') !== false || (strpos($variation_child['price'], 'INR') !== false && strtolower($variation_child['brand'])=='hukut'))
                    {
                            $finaldata[$i]['brand']=$variation_child['brand'];
                            $finaldata[$i]['price']=$variation_child['price'];
                            $finaldata[$i]['url']=$variation_child['buy_link'];
                        //echo $i.'> brand is '.$variation_child['brand'].' & Price is '.$variation_child['price'];

                    }
                   
               }
               $i++;
           }
        //    var_dump($finaldata);exit;

           if(count($ram_data) > 0)
           {
             $insert_variation[0] = [
                'product_id' => $data->wp_id,
                'variation_name' => 'Ram',
                'values' => json_encode($ram_data) 
            ];

           }
           if(count($storage_data) > 0)
           {
             $insert_variation[1] = [
                'product_id' => $data->wp_id,
                'variation_name' => 'Storage',
                'values' => json_encode($storage_data) 
            ];

           }
           if(count($size_data) > 0)
           {
             $insert_variation[2] = [
                'product_id' => $data->wp_id,
                'variation_name' => 'Size',
                'values' => json_encode($size_data) 
            ];

           }
           if(count($ram_data) < 1 && count($storage_data) < 1 && count($size_data) < 1)
           {
              $insert_variation[0] = [
                'product_id' => $data->wp_id,
                'variation_name' => 'Default',
                'values' => json_encode(['default'])
            ];

           }


           foreach($finaldata as $key=> $price)
           {

                if(isset($price['ram']))
                {
                    $title=$price['ram'].'+'.$price['storage'];

                    $variant_attributes[]=array(
                        'product_id'=>$data->wp_id,
                        'variant_slug'=>$title,
                        'attribute_name'=>'Ram',
                        'values'=>$price['ram'],
                    );

                    $variant_attributes[]=array(
                        'product_id'=>$data->wp_id,
                        'variant_slug'=>$title,
                        'attribute_name'=>'Storage',
                        'values'=>$price['storage'],
                    );
                }
                else if(isset($price['size']))
                {
                    $title=$price['size'];

                    $variant_attributes[]=array(
                        'product_id'=>$data->wp_id,
                        'variant_slug'=>$title,
                        'attribute_name'=>'Size',
                        'values'=>$price['size'],
                    );
                }
                else if(isset($price['storage']))
                {
                    $title=$price['storage'];

                    $variant_attributes[]=array(
                        'product_id'=>$data->wp_id,
                        'variant_slug'=>$title,
                        'attribute_name'=>'Storage',
                        'values'=>$price['storage'],
                    );
                }
                else
                {
                    $title='Default';

                    $variant_attributes[]=array(
                        'product_id'=>$data->wp_id,
                        'variant_slug'=>$title,
                        'attribute_name'=>'Default',
                        'values'=>'default',
                    );
                }

                if(isset($price['price']))
                {
                    $price_explode=explode(' ',$price['price']);
                    $priceWithoutComma = str_replace(',', '', $price_explode[1]);
                    $price['price'] = number_format((float)$priceWithoutComma, 2, '.', '');

                }
                else
                {
                    $price['price']=0;
                }
                $insert_variant[] = [
                    'product_id' => $data->wp_id,
                    'title' => $title,
                    'slug' => $title,
                    'sku_code' => $title,
                    'price' => $price['price'],
                    'qty' => '1',
                    'is_default' => 'Y',
                    'discount_price' =>  0,
                    'discount_price_in' =>  null,
                    'discount_price_start_date' => null,
                    'discount_price_end_date' => null
                ];

                if(isset($price['brand']))
                {
                    $check_vendor=Vendor::where('name',strtolower($price['brand']))->first();
                    if($check_vendor)
                    {
                        $vendor_id=$check_vendor->id;
                    }
                    else
                    {
                        $vendor_ins=['name'=>$price['brand'],'slug'=>$this->createSlug($price['brand']),'image'=>'','website_url'=>'','order_by'=>'1'];
                        $vendor_id=Vendor::create($vendor_ins)->id;
                    }
                    $variant_vendors[]=array(
                        'product_id'=>$data->wp_id,
                        'vendor_id'=>$vendor_id,
                        'variant_slug'=>$title,
                        'product_url'=>$price['url'],
                        'discount_price'=>0,
                    );
                }
                else
                {
                    $variant_vendors[]=array(
                        'product_id'=>$data->wp_id,
                        'vendor_id'=>2,
                        'variant_slug'=>$title,
                        'product_url'=>'https://hukut.com/contact',
                        'discount_price'=>0,
                    );
                }
               

                
           }

           $insert_image[] = [
            'product_id' => $data->wp_id,
            'variation_sku_code' => $insert_variant[0]['sku_code'],
            'image_url' =>json_encode(explode(',', $data->productimagegallery)),
         ];

           
        // var_dump($insert_variation);
        // var_dump($insert_variant);
        // var_dump($variant_attributes);
        // var_dump($variant_vendors);
        // var_dump($insert_image);
        
           ProductVariation::insert($insert_variation);
           ProductVariant::insert($insert_variant);
           ProductVariantAttribute::insert($variant_attributes);
           ProductVariantVendor::insert($variant_vendors);
           ProductImage::insert($insert_images);



        }
        $transactionStatus = DB::transactionLevel();

        if ($transactionStatus > 0) {
            // Database transaction success
            DB::commit();
            echo 'Success';
           } else {
            // Throw error
            DB::rollBack();
            echo 'Failed';
        }
        

    }

    
}
