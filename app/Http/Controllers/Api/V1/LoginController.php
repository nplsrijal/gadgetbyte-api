<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints related to user authentication"
 * )
 */
class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="User login",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", format="email", example="user@example.com",description="The email or username of the user"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK", 
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", description="The access token"),
     *             @OA\Property(property="token_type", type="string", example="Bearer"),
     *             @OA\Property(property="expires_at", type="string", format="date-time", example="2023-05-14 10:00:00"),
     *         )
     *     ),
     *     @OA\Response(response="401", description="Unauthorized", 
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Invalid username or password")
     *         )
     *     ),
     * )
     */
    public function login(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            
            return $this->error('Username or password missing', Response::HTTP_UNPROCESSABLE_ENTITY);

        }
        
        
        $loginType = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        $credentials = [
            $loginType => $request->input('username'),
            'password' => $request->input('password'),
        ];

        if (!Auth::attempt($credentials)) {
            return $this->error('Invalid username or password', Response::HTTP_UNPROCESSABLE_ENTITY);

        }

        
        
        $user = $request->user();
        $token = $user->createToken('Access Token');

        $user = auth()->user();
        if ($user) {
            $cacheKey = 'user_info_' . $user->id;
            Cache::forget($cacheKey);
            Cache::put($cacheKey, $user, 1295999);
            info(auth()->check());
        }



        return $this->success([
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => $token->token->expires_at->toDateTimeString(),
            'user' => [
                'userid'=>$user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'username' => $user->username,
                'user_type_id' => $user->user_type_id,
            ],
        ], Response::HTTP_OK);
    }


    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="User logout",
     *     tags={"Authentication"},
     *     security={{"bearer_token":{}}},
     *     @OA\Response(response="200", description="OK", 
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", description="Successfully logged out")
     *         )
     *     ),
     * )
     */
    public function logout(): JsonResponse
    {
        try {
            if (auth()->check()) {
                $cacheKey = 'user_info_' . auth()->id(); // Generate a unique cache key
                Cache::forget($cacheKey);
                info($this->user);
                auth()->user()->token()->revoke();
                return response()->json(['message' => 'Successfully logged out']);
            } else {
                return response()->json(['message' => 'Something went wrong'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (\Exception $e) {
            info($e->getMessage());
            return response()->josn(['message' => 'Something went wrong'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}