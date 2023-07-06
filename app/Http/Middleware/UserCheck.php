<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UserCheck
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $Authorization = $request->header('Authorization');

        if (empty($Authorization)) {

            return response(['code'=>401,'message'=>"Authentication failed！"],401);
        }
        $access_token = trim(ltrim($Authorization, 'Bearer'));
        $res_user = DB::table("users")
        ->where('access_token', $access_token)
        ->select("id","avatar","name","type","token","access_token","expire_date")
        ->first();
        if (empty($res_user)) {
           return response(['code'=>401,'message'=>"User does not exist, please log in！"],401);
        }
        $expire_date = $res_user->expire_date;
        if(empty($expire_date)){
           return response(['code'=>401,'message'=>"The token has expired, please log in again！"],401);
        }
        if($expire_date<Carbon::now()){
            return response(['code'=>401,'message'=>"The token has expired, please log in again！"],401);
        }
         $addtime = Carbon::now()->addDays(5);
        if($expire_date<$addtime){
            $add_expire_date = Carbon::now()->addDays(30);
            DB::table("users")->where('access_token', $access_token)->update(["expire_date"=>$add_expire_date]);
        }

        $request->user_id = $res_user->id;
        $request->user_type = $res_user->type;
        $request->user_avatar = $res_user->avatar;
        $request->user_name = $res_user->name;
        $request->user_token = $res_user->token;

        return $next($request);
    }

}
