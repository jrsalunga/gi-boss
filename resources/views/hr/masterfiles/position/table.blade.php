<tr>
	<td>
		<a href="/hr/masterfiles/position/{{ strtolower($position->code) }}">{{ $position->code }}</a> 
	</td>
	<td>
		<a href="/hr/masterfiles/position/{{ $position->lid() }}">{{ $position->descriptor }}</a>
	</td>
</tr>