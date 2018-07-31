<?php namespace App\Traits;

use Prettus\Repository\Events\RepositoryEntityCreated;

trait Repository {

  protected $skipOrder = false;

  public function skipOrder($value=true) {
    $this->skipOrder = $value;
    return $this;
  }

  public function checkSkipOrder(){
    return $this->skipOrder;
  }

  public function order($order=null, $asc='asc'){
    if(!$this->checkSkipOrder()) {

      if(!is_null($order)) {
        if (is_array($order)) 
          foreach ($order as $key => $field) 
            $this->orderBy($field, $asc);
        else 
          $this->orderBy($field, $asc);
      } else {
        if (property_exists($this, 'order'))
          $this->order($this->order);
      }
    }
    return $this;
  }

  public function codeID($id, $field=['*']) {
    $datas = $this->skipCache()->scopeQuery(function($q) use ($id, $field) {
      return $q->select($field)->where('id', $id)->orWhere('code', $id);
    })->all();

    return is_null($datas)
      ? NULL
      : $datas->first();
  }


  /*****
  * search/match $field from $attributes and DB before creating 
  * @param: array attributes
  * @param: array/string field to be matched from attr and db 
  * @return: model
  * 
  */
  public function findOrNew($attributes, $field) {
    $attr_idx = [];

    if (is_array($field)) 
      foreach ($field as $value) 
        $attr_idx[$value] = array_get($attributes, $value);
    else 
      $attr_idx[$field] = array_get($attributes, $field);

    $obj = $this->findWhere($attr_idx)->first();

    return !is_null($obj) ? $obj : $this->create($attributes);
  }



  public function firstOrNewField($attributes, $field) {
      
    $attr_idx = [];
    
    if (is_array($field)) {
      foreach ($field as $value) {
        $attr_idx[$value] = array_pull($attributes, $value);
      }
    } else {
      $attr_idx[$field] = array_pull($attributes, $field);
    }

    $m = $this->model();
    // Retrieve by the attributes, or instantiate a new instance...
    $model = $m::firstOrNew($attr_idx);
    //$this->model->firstOrNew($attr_idx);
    
    foreach ($attributes as $key => $value) {
      $model->{$key} = $value;
    }

    return $model->save() ? $model : false;

  }


  public function modelCreate(array $attributes) {

    $model = new $this->model();

    foreach ($attributes as $key => $value) {
      if (!empty($value))
        $model->{$key} = $value;
    }

    event(new RepositoryEntityCreated($this, $model));

    return $model->save() ? $model : NULL;
  }
  

  public function deleteWhere(array $where){
    return $this->model->where($where)->delete();
  }
  
}
