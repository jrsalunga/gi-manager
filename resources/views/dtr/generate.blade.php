@extends('index')

@section('title', '- DTR')

@section('body-class', 'branch-dtr')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">DTR Generation</li>
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
              <span class="glyphicon glyphicon-th-list"></span>
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
            <h3 class="panel-title"><span class="gly gly-history"></span> Process DTR</h3>
          </div>
          <div class="panel-body">
            {!! Form::open(['method'=>'POST', 'url'=>'dtr/generate', 'class'=>'form-horizontal']) !!}
              <div class="form-group">
                <label for="fr" class="col-sm-4 control-label">Date From:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="fr" name="fr" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime('yesterday')) }}" required>
                </div>
              </div>
              <div class="form-group">
                <label for="to" class="col-sm-4 control-label">Date To:</label>
                <div class="col-sm-8">
                  <input type="text" class="form-control" id="to" name="to" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime('yesterday')) }}" required>
                </div>
              </div>
              <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                  <!--<button type="submit" class="btn btn-primary">Generate</button>-->
                  <button type="button" id="btn-dtr-generate" data-loading-text="Generating..." class="btn btn-primary" autocomplete="off">
                    Generate
                  </button>

                </div>
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
  <script>
    $.ajaxSetup({
      headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      beforeSend: function(jqXHR, obj) {
        
      }
    });


    $(".panel-heading.new").effect("highlight", {}, 2000);
    $('.alert').not('.alert-important').delay(5000).slideUp(300);


  $(document).ready(function(){
    $('#btn-dtr-generate').on('click', function (e) {
      e.preventDefault();
      $('.alert').remove();
      var $btn = $(this).html('Generating... <span class="glyphicon glyphicon-refresh rotate"></span>');
      $btn.prop('disabled', true);

      var formData = {};
      formData.fr = $('#fr').val();
      formData.to = $('#to').val();
      

      $.ajax({
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded',
        url: '/dtr/generate',
        dataType: "json",
        //async: false,
        data: formData,
        beforeSend: function(jqXHR, obj) {
          console.log(obj.data);
        }
      })
      .done(function(data, textStatus, jqXHR){

          var html = '<div class="alert '+data.alert+'" role="alert">';
              html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
              html += '<span aria-hidden="true">×</span>';
              html += '</button>';
              html += '<ul>';
              html += '<li>'+ data.data.message +'</li>';
              html += '</ul>';
              html += '</div>';

          
          $('#nav-action').after(html);
          $('.alert').delay(5000).slideUp(300);
          $btn.prop('disabled', false).html('Generate');
          $('.close').focus();

        
          console.log(data);
          console.log(textStatus);
          console.log(jqXHR);
      })
      .fail(function(jqXHR, textStatus, errorThrown){
            var html = '<div class="alert alert-danger" role="alert">';
              html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
              html += '<span aria-hidden="true">×</span>';
              html += '</button>';
              html += '<ul>';
              html += '<li>Error! contact system administrator...</li>';
              html += '</ul>';
              html += '</div>';

          $('.alert').remove();
          $('#nav-action').after(html);
          $btn.html('Generate').prop('focus', false);
            console.log(textStatus);
            console.log(errorThrown);
            console.log(jqXHR);
      }); 
    });


  })
  

  </script>
@endsection
