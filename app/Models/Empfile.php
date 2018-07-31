<?php namespace App\Models;

use App\Models\BaseModel;

class Empfile extends BaseModel {
 
	//protected $connection = 'hr';
	protected $table = 'empfile';
 	protected $fillable = ['branch_id', 'employee_id', 'filename', 'type', 'file_upload_id', 'remarks', 'update_at', 'downloaded_at'];
 	protected $dates = ['created_at', 'updated_at', 'downloaded_at'];

  
}
