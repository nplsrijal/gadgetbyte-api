<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;


class PatchController extends Controller
{
    //

    public function index()
    {
        $review = DB::table('wp_reviews')->first();

        if ($review) {
            $serializedData = $review->reviews;

            try {
                $reviews = $serializedData ? unserialize($serializedData) : null;

                if ($reviews !== false) {
                    // Successfully unserialized the data
                    var_dump($reviews);
                } else {
                    // Error occurred during unserialization
                    echo "Error: Unable to unserialize data.";
                }
            } catch (Throwable $e) {
                // Log the error or handle it as needed
                echo "Error: " . $e->getMessage();
            }
        } else {
            // Handle case where no records are found
            echo "Error: No records found in wp_reviews table.";
        }



    }
}
