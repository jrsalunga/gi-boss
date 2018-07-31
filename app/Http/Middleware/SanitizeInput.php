<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class SanitizeInput
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
        
      //return $next($request);
      //return $request->method();
       if (!in_array(strtolower($request->method()), ['post', 'put'])) {
            return $next($request);
        }

        $input = $request->all();


        array_walk_recursive($input, function(&$input) {
            //$input = strip_tags($input);
            $input = $this->sanitize($input);
        });

        $request->merge($input);

        return $next($request);
    }


    public function strip_tags_content($text, $tags = '', $invert = FALSE) { 

          preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags); 
          $tags = array_unique($tags[1]); 
            
          if(is_array($tags) AND count($tags) > 0) { 
            if($invert == FALSE) { 
              return preg_replace('@<(?!(?:'. implode('|', $tags) .')\b)(\w+)\b.*?>.*?</\1>@si', '', $text); 
            } 
            else { 
              return preg_replace('@<('. implode('|', $tags) .')\b.*?>.*?</\1>@si', '', $text); 
            } 
          } 
          elseif($invert == FALSE) { 
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text); 
          } 
          return $text; 
        } 

    public function cleanInput($input) {
     
      $search = array(
        '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
      );
     
        //$output = preg_replace($search, '', $input);
        foreach ($search as $src) {
            if(preg_match($src, $input, $matches)){
                //echo 'Match: '.$src.' = '.$input.'<br>';
            }
            //echo $matches[1].'<br>';
        }
        
        $output = preg_replace_callback($search, function($matches){
            //$this->sanitize_log(getenv('COMPUTERNAME'), 'input = '. $matches[0]);
            logAction(getenv('COMPUTERNAME'), 'input = '. $matches[0], storage_path().DS.'logs'.DS.'sanitize'.DS.c()->format('Y-m-d').'-sanitized.txt');
        }, $input);
        return $output;
    }

    public function sanitize($input) {
        
        if (is_array($input)) {
            foreach($input as $var=>$val) {
                $output[$var] = $this->sanitize($val);
            }
        } else {
            if (get_magic_quotes_gpc()) {
                $input = stripslashes($input);
            }
            $input  = $this->cleanInput($input);
            //$input = $this->escape($input);
            $output = htmlspecialchars($input);
        }
        return trim($output);
    }

    private function escape($inp)
    { 
        if(is_array($inp)) return array_map(__METHOD__, $inp);

        if(!empty($inp) && is_string($inp)) { 
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
        } 
        return $inp; 
    }


    

   
}
