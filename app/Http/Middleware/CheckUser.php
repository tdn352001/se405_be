<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckUser
{
    public function handle(Request $request, Closure $next)
    {
        $authorization = $request->header('Authorization');

        if (empty($authorization)) {
            return response([
                "code" => 401,
                "message" => "Authentication failed"
            ], 401);
        }

        $access_token = trim(ltrim($authorization, 'Bearer'));
        $user = DB::table('users')->where('access_token', $access_token)
            ->select('id', 'avatar', 'name', 'token', 'type', 'access_token', 'expire_date')
            ->first();

        if (empty($user)) {
            return response([
                "code" => 401,
                "message" => "User does not exist"
            ], 401);
        }
        $expire_date = $user->expire_date;

        if (empty($expire_date)) {
            return response([
                "code" => 401,
                "message" => "You must login again"
            ], 401);
        }

        if ($expire_date < Carbon::now()) {
            return response([
                "code" => 401,
                "message" => "Your token has expired"
            ], 401);
        }

        $add_time = Carbon::now()->addDays(5);
        if ($expire_date < $add_time) {
            $add_time_expire = Carbon::now()->addDays(30);
            DB::table('users')
                ->where('access_token', $access_token)
                ->update(['expire_date' => $add_time_expire]);
        }

        $request->user_id = $user->id;
        $request->user_type = $user->type;
        $request->user_name = $user->name;
        $request->user_avatar = $user->avatar;
        $request->user_token = $user->token;

        return $next($request);
    }
}
