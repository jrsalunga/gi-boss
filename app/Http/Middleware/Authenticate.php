<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class Authenticate
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($this->auth->guest()) {
            if ($request->ajax()) {
                //return response('Unauthorized.', 401);
                return response(['status'=>'error', 'code'=>'401', 'message'=>'Unauthorized! Please login again..']);
            } else {
                //return redirect()->guest('auth/login');
                return redirect()->guest('/login');
            }
        }

        if($this->auth->user()->admin == 2)
                return redirect('/hr');
        else if($this->auth->user()->admin != 1)
                return redirect('/logout');

        return $next($request);
    }
}
