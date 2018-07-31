@if(isset($field))
	@if($errors->has($field))
		{{ 'has-error has-feedback' }}
	@endif

	{{ $field }}
@endif