<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    public function reviewPush(Request $request)
    {
        $rules = [
            'feedback' => 'required|string|max:500',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response([
                'error' => $validator->errors()
            ]);
        }

        $review = new UserReview();
        $review->listing_id = $request->listingId;
        $review->user_id = auth()->id();
        $review->rating = $request->rating;
        $review->review = $request->feedback;
        $review->save();

        $data['review'] = $review->review;
        $data['review_user_info'] = $review->review_user_info;
        $data['rating'] = $review->rating;
        $data['date_formatted'] = dateTime($review->created_at, 'd M, Y h:i A');

        return response([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
