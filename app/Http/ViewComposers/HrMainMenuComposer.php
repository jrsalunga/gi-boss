<?php namespace App\Http\ViewComposers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class HrMainMenuComposer
{
  public $menus = [];
  public $controllerName;
  public $segment = 4;
  public $subMenu;
  public $subMenuPos = 5;


  public function __construct(Request $request) {
    $this->menus = config('menu.main-hr');
    $u = explode('/', $request->url());
    $this->controllerName = isset($u[$this->segment]) ? $u[$this->segment] : FALSE;
    $this->subMenu = isset($u[$this->subMenuPos]) ? $u[$this->subMenuPos] : FALSE;
  }

  public function compose(View $view) {
    $view->with('main', $this->menus);
    $view->with('controllerName', $this->controllerName);
    $view->with('subActive', $this->subMenu);
  }
}