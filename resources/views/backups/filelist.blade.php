@extends('index')

@section('title', '- Backups')

@section('body-class', 'generate-dtr')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">Backups</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
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
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" >
          
          @if(count($data['breadcrumbs'])>0)
            <?php array_shift($data['breadcrumbs']) ?>
            <ol class="breadcrumb">
            @foreach($data['breadcrumbs'] as $path => $folder)
              <li><a href="/backups{{ $path }}">{{ $folder }}</a></li>
            @endforeach
            <li>{{ $data['folderName'] }}</li>
            </ol>
          @endif



        <table id="tb-backups" class="table">
          @if(count($data['subfolders'])>0)
            @foreach($data['subfolders'] as $path => $folder)
            <tr>
              <td><a href="/backups{{ $path }}">{{ $folder }}</a></td>
            </tr>
            @endforeach
          @endif


          @if(count($data['files'])>0)
            @foreach($data['files'] as $path => $file)
            <tr>
              <td>{{ $file['name'] }}</td>
            </tr>
            @endforeach
          @endif

        </table>
      </div>
      <div role="tabpanel" class="tab-pane" id="files">...</div>
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
