@extends('master')

@section('title', '- Setslp Storage')

@section('body-class', 'depslp-storage')

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
    
    @if(count($data['breadcrumbs'])>0)
      <?php 
        $temp = $data['breadcrumbs'];
        array_shift($temp) 
      ?>
      <li><a href="/storage">Filing System</a></li>
      @foreach($temp as $path => $folder)
        <li><a href="{{ strtolower($path) }}">{{ $folder }}</a></li>
      @endforeach
      <li class="active">{{ $data['folderName'] }}</li>
    @else 
      <li class="active">Filing System</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          @include('_partials.menu.logs')
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
    <div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
        <a href="/backups" aria-controls="pos" role="tab">
          Card Settlement Slip Archive
        </a>
      </li>
    </ul>

    <!-- Tab panes -->
    <div class="file-explorer tab-content">
      <div role="tabpanel" class="tab-pane active" >
          

        <div style="height: 10px;"></div>
        <div class="navbar-form"  style="padding:0; margin: 8px 15px; border-top: 0;">
        @if(count($data['breadcrumbs'])>1)
        <a href="{{ strtolower(endKey($data['breadcrumbs'])) }}" class="btn btn-default" title="Back">
          <span class="gly gly-unshare"></span>
          <span class="gly gly-hdd"></span>{{ strtoupper(endKey($data['breadcrumbs'])) }}
        </a>
        @else

        <!--
        <button class="btn btn-default" type="button">
          <span class="glyphicon glyphicon-cloud"></span>
          backups
        </button> 
        -->
        @endif
        </div>

        <table id="tb-backups" class="table table-hover">
          <!--
          <thead>
            <tr>
              <th>File/Folder</th><th>Size</th><th>Type</th><th>Date Modified</th>
            </tr>
          </thead>
        -->
          <tbody>
          @if(count($data['subfolders'])>0)
            @foreach($data['subfolders'] as $path => $folder)
            <tr>
              <td colspan="5"><a href="{{ strtolower($data['folder']) }}/{{ strtolower($folder) }}"><span class="fa fa-folder-o"></span> {{ $folder }}</a></td>
            </tr>
            @endforeach
          @endif


          @if(count($data['files'])>0)
            @foreach($data['files'] as $path => $file)
            <tr>
              <td>
                @if($file['type']=='zip')
                  <span class="fa fa-file-archive-o"></span>
                @elseif($file['type']=='img')
                  <span class="fa fa-file-image-o"></span>
                @elseif($file['type']=='file')
                  <span class="fa fa-file-pdf-o"></span>
                @else
                  <span class="fa file-o"></span>

                @endif 

                {{ $file['name'] }}</td>
                
                <td>
                  
                  <a style="text-decoration: none; margin-right: 10px;" href="/download{{ $file['fullPath'] }}" target="SingleSecondaryWindowName" 
                    onclick="openRequestedSinglePopup(this.href); return false;" title="View">
                    <span class="fa fa-clone"></span>
                  </a>
                  
                  <a href="/download{{ $file['fullPath'] }}?download=true" title="Download"><span class="glyphicon glyphicon-download-alt"></span></a>

                </td>
                
                <td>{{ human_filesize($file['size']) }}</td>
                <td class="hidden-xs hidden-sm">{{ $file['type'] or 'Unknown' }}</td>
                <td class="hidden-xs">{{ $file['modified']->format('D, M j, Y g:i A') }}</td>
            </tr>
            @endforeach
          @endif
          </tbody>
        </table>
      </div>
    </div>

  </div>   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  
  <script type="text/javascript">
    var strWindowFeatures = "top=200,left=400,width=800,height=500,resizable,scrollbars=yes,status=no,location=no,menubar=no";
    var windowObjectReference = null; // global variable
    var PreviousUrl; /* global variable which will store the
                        url currently in the secondary window */

    function openRequestedSinglePopup(strUrl) {
      if(windowObjectReference == null || windowObjectReference.closed) {
        windowObjectReference = window.open(strUrl, "SingleSecondaryWindowName",
             strWindowFeatures);
      } else if(PreviousUrl != strUrl) {
        windowObjectReference = window.open(strUrl, "SingleSecondaryWindowName", strWindowFeatures);
        /* if the resource to load is different,
           then we load it in the already opened secondary window and then
           we bring such window back on top/in front of its parent window. */
        windowObjectReference.focus();
      } else {
        windowObjectReference.focus();
      };

      PreviousUrl = strUrl;
      /* explanation: we store the current url in order to compare url
         in the event of another call of this function. */
    }
  </script>
@endsection
