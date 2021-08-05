@foreach($main as $position => $menu)
	@foreach($menu as $controller => $option)
    @if($controller == $controllerName)

    	<ul class="nav nav-sidebar">
      @foreach($option['dropdown'] as $table => $sub)
				@if($table == $subActive)
		      <li class="active">
		    @else
		      <li>
		    @endif
				<a href="/ap/{{$controller}}/{{$table}}" data-toggle="loader">{{$sub['caption']}}</a>
				</li>
			@endforeach
			</ul>
    @endif
	@endforeach
@endforeach