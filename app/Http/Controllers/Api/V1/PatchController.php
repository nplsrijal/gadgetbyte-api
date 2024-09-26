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
        $review_data = DB::table('product_wp')->where('wp_id','141384')->orderBy('wp_id', 'asc')->get();

        foreach($review_data as $data)
        {
            //var_Dump($data->productattribute);
            // $data_attr = json_decode($data->productattribute, true);
            // var_dump($data_attr);exit;
            //$a_serializedData = stripslashes($data->productattribute);


            //$attributes = unserialize($a_serializedData);
           // var_Dump($attributes);exit;

            $product=$data->post_name;
            //$productAttributes = [];
            // foreach ($attributes as $taxonomy => $attribute) {
            //     $productAttributes = $this->extracted($taxonomy, $data->wp_id, $attribute, $productAttributes);
            // }
            //$data->productAttribute = $productAttributes;
            $data->specs = $this->getSpecsForProduct($data->wp_id);
            $data->child = $this->getProductVariant($data->wp_id);

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
        $readableResults = [];
        foreach ($groupedResults as $groupIndex => $variants) {
            $group = ['group' => $groupIndex, 'variants' => []];
            foreach ($variants as $variantIndex => $attributes) {
                $variant = ['variant' => $variantIndex];
                foreach ($attributes as $attribute => $values) {
                    if ($attribute == 'size' || $attribute == 'size_storage') {
                        // Fetch the actual term value from the 'wp_terms' table
                        if (!empty($values) && isset($values[0])) {
                            $termValue = DB::table('terms_wp')
                                ->where('term_id', $values[0])
                                ->value('name');
                            $variant[$attribute] = $termValue;
                        }
                    }  else {
                        $variant[$attribute] = implode(', ', $values);
                    }
                }
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
}
