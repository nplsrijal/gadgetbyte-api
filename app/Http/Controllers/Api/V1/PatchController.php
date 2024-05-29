<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use App\Models\PostReview;
use App\Models\Post;
use App\Models\PostFaq;



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


}
