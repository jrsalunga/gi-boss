@extends('master')

@section('title', ' - Assign Branch')

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
    <li class="active">Settings</li>
  </ol>
	<hr>
  
  <div class="row">
  	<div class="col-sm-3">
  		<ul class="nav nav-pills nav-stacked">
        <li role="presentation"><a href="/settings">Profile</a></li>
        <li role="presentation"><a href="/settings/password">Change Password</a></li>
        <li role="presentation" class="active"><a href="/settings/bossbranch">Branch</a></li>
        <li role="presentation"><a href="/settings/emp-import">Import Employee</a></li>
			</ul>
  	</div>
  	<div class="col-sm-9">
      
      <h4>Assign Branch</h4>
      
      @include('_partials.alerts')


      
      <table class="table">
        <tbody>
          @foreach($datas as $data)
            <tr>
              <td>
                <div class="btn-group br-btn" data-toggle="buttons" data-branchid="{{ $data['branch']->id }}">
                  <label class="btn btn-default {{ is_null($data['assign']) ? '':'active' }}">
                    <input type="checkbox" autocomplete="off" {{ is_null($data['assign']) ? '':'checked' }}> 
                    <span class="glyphicon glyphicon{{ is_null($data['assign']) ? '-star-empty':'-star' }}"></span> 
                    {{ $data['branch']->code }} - {{ $data['branch']->descriptor }}
                  </label>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      
  		
  	</div>

  </div>



</div>
@endsection



@section('js-external')
  
  <script src="/js/vendors-common.min.js"></script>



  <script>

  var assignBranch = function(b, a){
    var formData = {
    branchid : b,
    assign : a,
  }
    return $.ajax({
          type: 'POST',
          contentType: 'application/x-www-form-urlencoded',
          url: '/settings/bossbranch',
          dataType: "json",
          data: formData,
          //async: false,
          success: function(data, textStatus, jqXHR){
              //aData = data;
              console.log(data);
          },
          error: function(jqXHR, textStatus, errorThrown){
        
              //alert(textStatus + ' Failed on posting data');
          }
      }); 
    
    //return aData;
  }




  $(document).ready(function(){
    
    $(".br-btn").on('click', function () {
      console.log($(this).children('label').hasClass('active'));
      console.log($(this).data('branchid'));

      var active = $(this).children('label').hasClass('active');

      if(active) {
        $(this).children('label').children('span').removeClass('glyphicon-star').addClass('glyphicon-star-empty');
      } else {
        $(this).children('label').children('span').removeClass('glyphicon-star-empty').addClass('glyphicon-star');
      }

      assignBranch($(this).data('branchid'), active);

      //$(this).button('toggle'); // button text will be "finished!"
    })
  });
</script>
@endsection










