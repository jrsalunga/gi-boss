<?php
	if (isset($model) && !is_null($model)) {

		$previous = $model->previousByField($field);
		$next = $model->nextByField($field);
?>
<nav aria-label="pn">
  <ul class="pager">
  	@if($previous!='false')
    <li class="previous">
    	<a href="/hr/{{$controllerName}}/{{$subActive}}/{{ $previous->lid() }}" data-toggle="loader">
    		<span aria-hidden="true">&larr;</span> {{ $previous->{$field} }} 
    	</a>
    </li>
    @endif

    @if($next!='false')
    <li class="next">
    	<a href="/hr/{{$controllerName}}/{{$subActive}}/{{ $next->lid() }}" data-toggle="loader">{{ $next->{$field} }} 
    		<span aria-hidden="true">&rarr;</span>
    	</a>
    </li> 
    @endif
  </ul>
</nav>

<?php
}
?>