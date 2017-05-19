@extends('index')

@section('title', '- DTR View')

@section('body-class', 'view-dtr')

<?php
  $prev = $date->copy()->subDay();
  $next = $date->copy()->addDay();
?>

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/dtr/{{ $date->format('Y') }}">DTR {{ $date->format('Y') }}</a></li>
    <li><a href="/dtr/{{ $date->format('Y') }}/{{ $date->format('m') }}">{{ $date->format('M') }}</a></li>
    <li class="active">{{ $date->format('d') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dtr/{{ $date->format('Y') }}/{{ $date->format('m') }}" class="btn btn-default">
              <span class="gly gly-table"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
            </button>
          </div> <!-- end btn-grp -->

          <div class="btn-group pull-right" role="group">
            <a href="/dtr/{{$prev->format('Y')}}/{{$prev->format('m')}}/{{$prev->format('d')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a href="/dtr/{{$next->format('Y')}}/{{$next->format('m')}}/{{$next->format('d')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    <h3>DTR Summary - {{ $date->format('D, F d, Y') }} </h3>
    <p><em>Daily time record summary of all employees for the day.</em></p>

    @include('_partials.alerts')



    

    @if(count($dtrs)>0)
      <table class="table table-bordered table-responsive">
      <thead>
        <tr>
          <th class="bg-default">Employee</th>
          <th class="bg-default">Time Start/In</th>
          <th class="bg-default">Break Start/In</th>
          <th class="bg-default">Break End/Out</th>
          <th class="bg-default">Time End/Out</th>
          <th class="bg-default">Work Hours</th>
          <th class="bg-default">Tardy Hours</th>
        </tr>
      </thead>
      <tbody>
        <?php $x = 1; ?>
      @foreach($dtrs as $dtr)
        <tr>
          <td>
            <a href="/dtr/{{$dtr->date->year}}/{{pad($dtr->date->month)}}/{{pad($dtr->date->format('d'))}}/{{$dtr->employee->id}}">
              {{ $x }}. {{ $dtr->employee->lastname }}, {{ $dtr->employee->firstname }}
            </a>
          </td>
          <td class="text-right">
            <small>{{ $dtr->timein->format('H:i') == '00:00' ? '-': $dtr->timein->format('h:i A') }}</small><br>
            <small>{{ $dtr->timestart->format('H:i') == '00:00' ? '-': $dtr->timestart->format('h:i A') }}</small>
          </td>
          <td class="text-right">
            <small>{{ $dtr->breakin->format('H:i') == '00:00' ? '-': $dtr->breakin->format('h:i A') }}</small><br>
            <small>{{ $dtr->breakstart->format('H:i') == '00:00' ? '-': $dtr->breakstart->format('h:i A') }}</small>
          </td>
          <td class="text-right">
            <small>{{ $dtr->breakout->format('H:i') == '00:00' ? '-': $dtr->breakout->format('h:i A') }}</small><br>
            <small>{{ $dtr->breakend->format('H:i') == '00:00' ? '-': $dtr->breakend->format('h:i A') }}</small>
          </td>
          <td class="text-right">
            <small>{{ $dtr->timeout->format('H:i') == '00:00' ? '-': $dtr->timeout->format('h:i A') }}</small><br>
            <small>{{ $dtr->timeend->format('H:i') == '00:00' ? '-': $dtr->timeend->format('h:i A') }}</small>
          </td>
          <td class="text-right">
            {{ $dtr->reghrs == '0.00' ? '-':number_format($dtr->reghrs,2) }}
          </td>
          <td class="text-right">
            {{ $dtr->tardyhrs == '0.00' ? '-':number_format($dtr->tardyhrs,2) }}
          </td>
        </tr>
        <?php $x++; ?>
      @endforeach
      </tbody>
      </table>
    @else 
      no record(s) found!
    @endif

   
   

    
      
  
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
