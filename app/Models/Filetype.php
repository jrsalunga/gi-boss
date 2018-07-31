<?php namespace App\Models;

use App\Models\BaseModel;
use App\Traits\CodeCaps;

class Filetype extends BaseModel {

	use CodeCaps;

	protected $table = 'filetype';
 	protected $guarded = ['id'];


  
}
