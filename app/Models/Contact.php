<?php namespace App\Models;

use App\Models\BaseModel;

class Contact extends BaseModel {

	protected $table = 'contact';
  public $timestamps = false;
  protected $fillable = ['type', 'number'];
  //protected $guarded = ['id'];
  

	public function contactable() {
    return $this->morphTo();
  }



  public function getNumber() {

  	if (empty($this->number))
      return NULL;  
    $s = str_replace(['-', '(', ')', ' ', '.'], '',$this->number);

    switch ($this->type) {
    	case 1:
    		return substr($s, -11, 4).' '.substr($s, -7, 3).' '.substr($s, -4, 4);
    		break;
    	case 2:
    		return '('.substr($s, -10, 2).') '.substr($s, -8, 4).' '.substr($s, -4, 4);
    		break;
    	default:
    		return $this->number;
    		break;
    }

  	
    
  }
  
}
