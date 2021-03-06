@extends('index')

@section('title', '- Upload Backup')

@section('body-class', 'generate-dtr')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="#">Upload</a></li>
    <li class="active">Backup</li>
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
              <span class="glyphicon glyphicon-cloud-upload"></span>
            </button>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="row row-centered dtr-daterange">
      <div class="col-sm-7 col-md-6 col-centered">
        <div id="panel-tasks" class="panel panel-success">
          <div class="panel-heading">
            <h3 class="panel-title"><span class="glyphicon glyphicon-cloud-upload"></span> Upload Backup</h3>
          </div>
          <div class="panel-body">
            {!! Form::open(['method'=>'PUT', 'url'=>'upload/postfile', 'class'=>'form-horizontal', 'enctype'=>'multipart/form-data']) !!}
            <div class="dropbox-container">
              <div id="dropbox" class="prod-image">
                <span class="message">Drop backup file here to upload. <br />
                <i>(they will only be visible to you)</i>
                </span>
              </div>
              <!--
              <label for="file_upload" class="lbl-file_upload">Upload</label> 
              -->
              <input type="file" id="file_upload" style="display: none" />

              <div class="row">
                <div class="col-lg-12">
                  <div class="input-group">
                    <span class="input-group-btn">
                      

                      <select class="btn btn-default" type="button" name="year" id="year">
                        @for($c=2015; $c<(now('Y')+2); $c++)
                          <option <?=(now('Y')==$c) ? 'selected':''?> value="{{$c}}">{{$c}}</option>
                        @endfor
                      </select>
                      <select class="btn btn-default" type="button" name="month" id="month">
                        @for($c=1; $c<13; $c++)
                          <option <?=(pad(now('M'),2)==pad($c,2)) ? 'selected':''?> value="{{ pad($c,2) }}">{{ pad($c,2) }}</option>
                        @endfor
                      </select>
                      <button id="attached" class="btn btn-default" type="button">
                        <span class="glyphicon glyphicon-paperclip"></span>
                      </button>
                    
                    </span>
                    <input type="text" class="form-control" id="filename" name="filename" readonly required>
                  </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
              </div>
              <div class="row" style="margin-top: 10px;">
                <div class="col-lg-12">
                  
                  <textarea class="form-control" id="notes" name="notes" placeholder="Notes" style="max-width:100%;"></textarea>
                </div><!-- /.col-lg-6 -->
              </div>
              <div class="row" style="margin-top: 10px;">
                <div class="col-lg-12">
                  
                  <button id="btn-upload" class="btn btn-primary" type="submit" disabled="disabled">Submit</button>
                  <a class="btn btn-default" href="/upload/backup">Cancel</a>  
                </div><!-- /.col-lg-6 -->
              </div>
            {!! Form::close() !!}
          </div>
        </div>
      </div>
    </div>
   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent
  <script src="/js/vendors/jquery.filedrop-0.1.0.js"></script>
  <script src="/js/filedrop.js"></script>
  <script>
  $('#attached').on('click', function(){
      //console.log($('.lbl-file_upload'));
      $('#file_upload').click();
    });
  $(document).ready(function(){
    
  });
  </script>
@endsection
