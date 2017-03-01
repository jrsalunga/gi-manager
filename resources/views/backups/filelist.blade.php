@extends('index')

@section('title', '- Filelist ('.strtoupper(brcode()).')')

@section('body-class', 'filelist')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/backups">Backups</a></li>
    @if(count($data['breadcrumbs'])>0)
      <?php array_shift($data['breadcrumbs']) ?>
      @foreach($data['breadcrumbs'] as $path => $folder)
        <li><a href="/backups{{ $path }}">{{ $folder }}</a></li>
      @endforeach
      <li class="active">{{ $data['folderName'] }}</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="glyphicon glyphicon-th-list"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-cloud"></span>
            </button>
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <a href="/backups/upload" class="btn btn-default">
              <span class="glyphicon glyphicon-cloud-upload"></span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
    <div>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="<?=($tab==='pos')?'active':''?>"><a href="/backups/pos" aria-controls="pos" role="tab">POS</a></li>
      <li role="presentation" class="<?=($tab==='files')?'active':''?>"><a href="/backups/files" aria-controls="files" role="tab">Files</a></li>
    </ul>

    <!-- Tab panes -->
    <div class="file-explorer tab-content">
      <div role="tabpanel" class="tab-pane active" >
          
         

        <div>&nbsp</div>
        <div class="navbar-form">
        @if(count($data['breadcrumbs'])>0)
        <a href="/backups{{ endKey($data['breadcrumbs']) }}" class="btn btn-default" title="Back">
          <span class="gly gly-unshare"></span>
          backups{{ endKey($data['breadcrumbs']) }}
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

        <table id="tb-backups" class="table">
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
              <td colspan="4"><a href="/backups{{ $path }}"><span class="fa fa-folder-o"></span> {{ $folder }}</a></td>
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
                @else
                  <span class="fa file-o"></span>

                @endif 

                {{ $file['name'] }}</td>
                <td><a href="/download/{{$tab}}/{{ $file['fullPath'] }}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span></a></td>
                <td>{{ human_filesize($file['size']) }}</td>
                <td>{{ $file['mimeType'] or 'Unknown' }}</td>
                <td>{{ $file['modified']->format('j-M-y g:ia') }}</td>
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
  @parent
  
  <script>
  
    
 
  </script>
@endsection
