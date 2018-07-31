<?php namespace App\Traits;

trait CodeCaps {
  
  public function setCodeAttribute($value) {
    $this->attributes['code'] = strtoupper($value);
  }
}
