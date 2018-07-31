<?php namespace App\Http\ViewComposers;

use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class MainMenuComposer
{
  public $menus = [];
  public $controllerName;
  public $segment = 3;
  public $subMenu;
  public $subMenuPos = 4;


  public function __construct(Request $request) {
    $this->menus = config('menu.main');
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