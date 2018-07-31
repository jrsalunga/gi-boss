<tr>
	<td>
		<a href="/masterfiles/lessor/{{ $lessor->lcode() }}">
			{{ $lessor->code }}
		</a> -
		<a href="/masterfiles/lessor/{{ $lessor->lid() }}">
			{{ $lessor->descriptor }}
		</a>
	</td>
	<td>
		@if($lessor->branches->count()>0)
			<span class="badge text-info">{{ $lessor->branches->count() }}</span>
		@endif
	</td>
</tr>