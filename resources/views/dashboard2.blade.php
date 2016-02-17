@extends('master')

@section('title', ' - Dashboard')

@section('css-internal')
<style>

.graph {
  font: 10px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

</style>
@endsection

@section('navbar-2')
<ul class="nav navbar-nav navbar-right"> 
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-menu-hamburger"></span>
    </a>
    <ul class="dropdown-menu">
    	<li><a href="/settings"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
      <li><a href="/logout"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>     
    </ul>
  </li>
</ul>
<p class="navbar-text navbar-right">{{ $name }}</p>
@endsection


@section('container-body')
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><a href="/"><span class="gly gly-shop"></span></a></li>
    <li class="active">Dashboard</li>
  </ol>

    <div class="graph">

    </div>
	
  
    <a href="/dashboard?date={{ $date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default">
      {{ $date->copy()->subDay()->format('Y-m-d') }}
    </a>
    <button class="btn btn-success">{{ $date->format('D, M j, Y') }}</button>
    <a href="/dashboard?date={{ $date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default">
      {{ $date->copy()->addDay()->format('Y-m-d') }}
    </a>

    <table class="table">
      <thead>
        <tr>
          <th>Branch</th>
          <th>Sales</th>
          <th>Customer</th>
          <th>Head Spend</th>
          <th>Tips</th>
          <th>Tips %</th>
          <th>Emp Count</th>
          <th>Manpower %</th>
          <th>Cost of Food</th>
          <th>Cost of Food %</th>
        </tr>
      </thead>
      <tbody>
        @foreach($dailysales as $key => $ds) 
  			<tr>
  				<td>{{ $key }} - {{ $ds['br']->descriptor }}</td>
          @if(is_null($ds['ds']))
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          @else
            <td class="text-right">{{ number_format($ds['ds']->sales,2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->custcount,0) }}</td>
            <td class="text-right">{{ $ds['ds']->custcount==0 ? 0:number_format($ds['ds']->sales/$ds['ds']->custcount, 2) }}</td>
            <td class="text-right">{{ number_format($ds['ds']->tips,2) }}</td>
            <td class="text-right">{{ $ds['ds']->custcount==0 || $ds['ds']->tips=='0.00' ? 0:number_format(($ds['ds']->sales/$ds['ds']->custcount)/$ds['ds']->tips, 3) }}</td>
            <td class="text-right">{{ $ds['ds']->empcount }}</td>
            <td class="text-right">{{ $ds['ds']->sales=='0.00' ? 0:number_format(($ds['br']->mancost*$ds['ds']->empcount)/$ds['ds']->sales,2) }}</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
          @endif
          
  			</tr>
  			@endforeach
  		</tbody>
		</table>
  



</div>
@endsection














@section('js-external')
  
<script src="/js/vendors-common.min.js"></script>

<script src="//d3js.org/d3.v3.min.js"></script>

<script>
var margin = {top: 20, right: 80, bottom: 30, left: 50},
    width = 1100 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y%m%d").parse;

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var color = d3.scale.category10();

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.temperature); });

var svg = d3.select(".graph").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.tsv("api/tsv?date={{ $date->format('Y-m-d') }}", function(error, data) {
  if (error) throw error;

  color.domain(d3.keys(data[0]).filter(function(key) { return key !== "date"; }));

  data.forEach(function(d) {
    d.date = parseDate(d.date);
  });

  var cities = color.domain().map(function(name) {
    return {
      name: name,
      values: data.map(function(d) {
        return {date: d.date, temperature: +d[name]};
      })
    };
  });

  x.domain(d3.extent(data, function(d) { return d.date; }));

  y.domain([
    d3.min(cities, function(c) { return d3.min(c.values, function(v) { return v.temperature; }); }),
    d3.max(cities, function(c) { return d3.max(c.values, function(v) { return v.temperature; }); })
  ]);

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Sales");

  var city = svg.selectAll(".city")
      .data(cities)
    .enter().append("g")
      .attr("class", "city");

  city.append("path")
      .attr("class", "line")
      .attr("d", function(d) { return line(d.values); })
      .style("stroke", function(d) { return color(d.name); });

  city.append("text")
      .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
      .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
      .attr("x", 3)
      .attr("dy", ".35em")
      .text(function(d) { return d.name; });
});

</script>

  
@endsection