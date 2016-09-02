<?php namespace App\Http\Requests;

use App\Http\Requests\Request;


class GetComponentPurchasedRequest extends Request {

	public function authorize() {

       return true;
    }
	
	public function rules() {
  	return [
      'branchid' => 'required|regex:/^[A-Fa-f0-9]{32}+$/',
      'from' => 'required|date_format:Y-m-d',
      'to' => 'required|date_format:Y-m-d',
	  ];
	}
	   
	  
}
