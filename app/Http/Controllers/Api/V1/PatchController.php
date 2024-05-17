<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\Models\PostReview;



class PatchController extends Controller
{
    //

    public function index()
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
}
