@foreach($main as $position => $menu)
  <ul class="nav navbar-nav {{ $position }}">
  @foreach($menu as $controller => $option)
    @if($controller == $controllerName)
      <li class="active">
    @else
      <li>
    @endif

    
    @if(array_key_exists('dropdown', $option))
      
      <a class="dropdown-toggle hidden-md hidden-lg" data-toggle="dropdown" href="#">
      {{ $option['caption'] }} <b class="caret"></b></a>
      <ul class="dropdown-menu hidden-md hidden-lg">
        @foreach($option['dropdown'] as $ddk => $ddv)
          @if($subActive == $ddk)
            <li class="active">
          @else
            <li>
          @endif
          <a href="/ap/{{ $controller }}/{{ $ddk }}" data-toggle="loader">{{ $ddv['caption'] }}</a></li>
        @endforeach
      </ul>
      
      <a href="/ap/{{ $controller }}" class="hidden-xs hidden-sm">{{ $option['caption'] }}</a>
    @else   
        <a href="/ap/{{ $controller }}">{{ $option['caption'] }}</a>
    @endif
  
    </li>
  @endforeach
    <!--
    <li class="dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">Dropdown <span class="caret"></span></a>
      <ul class="dropdown-menu">
        <li><a href="#">Action</a></li>
        <li role="separator" class="divider"></li>
        <li class="dropdown-header">Nav header</li>
        <li><a href="#">Separated link</a></li>
      </ul>
    </li>
    -->
  </ul>
@endforeach