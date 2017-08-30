<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>

<style type="text/css">
table td.nbtl {
    border-top: 1px solid #fff;
    border-left: 1px solid #fff;
    border-bottom: 1px solid #fff;
}

.text-right {
	text-align: right;
}

.prn {
	cursor: pointer;
	padding: 6px 12px;
	border: 1px solid #ccc;
	margin: 10px;
	display: inline-block;
	border-radius: 4px;
	text-decoration: none;
	color: #000;
}

.prn:hover {
	color: #333;
  background-color: #d4d4d4;
  border-color: #8c8c8c;
}

@media print {
 table {
 
 	font-size: 11px;
 }

 table td {
	padding: 2px;
 }

 .prn {
 	display: none;
 }
}
</style>
<body>

<a class="prn" href="javascript:window.print();">Print</a>


<?php
$arr = collect($days);
$f = $arr->first();
$l = $arr->last();
?>
<h4 style="margin: 5px 0 10px 0;">Mancom Schedule: {{ $f->format('m/d/Y') }} - {{ $l->format('m/d/Y') }} </h4>


@if(is_null($datas))

@else
  <table border="1" style="border-collapse: collapse; font-family: 'Source Code Pro', monospace; font-size: 11px;" cellpadding="5" cellspacing="0" >
    <thead>
      <tr>
        <th>Employees</th>
        @foreach($days as $day)
          <th class="text-center" style="{{ $day->dayOfWeek == Carbon\Carbon::SUNDAY ? 'background-color:#fcf8e3;':'' }}">
              <div class="text-center" style="{{ $day->dayOfWeek == Carbon\Carbon::SUNDAY ? 'background-color:#fcf8e3;':'' }}">
                  {{ $day->format('D') }}
              </div>
              <div class="text-center" style="{{ $day->dayOfWeek == Carbon\Carbon::SUNDAY ? 'background-color:#fcf8e3;':'' }}">
                  {{ $day->format('j') }}
              </div>
          </th>
        @endforeach
        <th class="bg-primary">
          <div class="text-center">Total</div>
          <div class="text-center">Days</div>
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach($datas as $d)
        <tr>
          <td>
            {{ $d['employee']->lastname }}, {{ $d['employee']->firstname }}
           
            <span style="float:right; margin-left:5px; color: #ccc;">
              {{ $d['employee']->position->code or '' }}
            </span>
          </td>
          @foreach($d['timelogs'] as $t)
            <td style="text-align: center; {{ $t['date']->dayOfWeek == Carbon\Carbon::SUNDAY ? 'background-color:#fcf8e3;':'' }}">
              @if($t['count']>0)
                  <span>{{ $t['count'] }}</span>
              @else

              @endif
            </td>
          @endforeach
          <td style="text-align: center;">
            @if($d['total_days']>0)
              <b>{{ $d['total_days'] }}</b>
            @endif

          </td>
        </tr>
    
      @endforeach
    </tbody>
  </table>

@endif


<script type="text/javascript">
	

</script>

</body>
</html>