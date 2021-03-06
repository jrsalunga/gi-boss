@if (count($errors) > 0)
    <div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
      <ul>
      @foreach($errors->all() as $message) 
        <li>{{ $message }}</li>
      @endforeach
    </ul>
    
    </div>
 
@endif


@if(session()->has('alert-success'))
  <div class="alert alert-success {{ session()->has('alert-important') ? 'alert-important':'' }}">
    {{ session('alert-success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if(session()->has('alert-error'))
  <div class="alert alert-danger {{ session()->has('alert-important') ? 'alert-important':'' }}">
    {{ session('alert-error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if(session()->has('alert-warning'))
  <div class="alert alert-warning {{ session()->has('alert-important') ? 'alert-important':'' }}">
    {{ session('alert-warning') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if(session()->has('depslp.delete'))
  <div class="alert alert-success {{ session()->has('alert-important') ? 'alert-important':'' }}" style="margin-bottom: 0;">
   The record of <b>{{ session('depslp.delete')->fileUpload->filename }}</b> was deleted and removed from the server!
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if(session()->has('setslp.success'))
  <div class="alert alert-success {{ session()->has('alert-important') ? 'alert-important':'' }}">
    <b><a href="/{{brcode()}}/setslp/{{session('setslp.success')->lid()}}">{{ session('setslp.success')->fileUpload->filename }}</a></b> 
    saved on server as 
    <b><a href="/{{brcode()}}/setslp/{{session('setslp.success')->lid()}}">{{ session('setslp.success')->filename }}</a></b>. 
    <small class="label label-primary"><a href="/{{brcode()}}/setslp/log?rdr=alert" style="color:#fff;">view logs</a></small>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif


@if(session()->has('setslp.delete'))
  <div class="alert alert-success {{ session()->has('alert-important') ? 'alert-important':'' }}" style="margin-bottom: 0;">
   The record of <b>{{ session('setslp.delete')->fileUpload->filename }}</b> was deleted and removed from the server!
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

