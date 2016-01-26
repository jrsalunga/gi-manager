@extends('index')

@section('title', '- DTR View')

@section('body-class', 'view-dtr')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/dtr/{{ $dtrs[0]->date->year }}/{{ pad($dtrs[0]->date->month) }}">Daily Time Record</a></li>
    <li class="active">{{ $dtrs[0]->date->format('D, M d') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dtr/{{ $dtrs[0]->date->year }}/{{ pad($dtrs[0]->date->month) }}" class="btn btn-default">
              <span class="gly gly-table"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
            </button>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    <h3>Daily DTR Summary - {{ $date->format('D, F d, Y') }} </h3>

    @include('_partials.alerts')



    

    @if(count($dtrs)>0)
      <table class="table table-bordered table-responsive">
      <thead>
        <tr>
          <th>Employee</th>
          <th>Total Hours</th>
          <th>Reg Hours</th>
          <th>OT Hours</th>
          <th>Tardy Hours</th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php $x = 1; ?>
      @foreach($dtrs as $dtr)
        <tr>
          <td>
            <a href="/dtr/{{$dtr->date->year}}/{{pad($dtr->date->month)}}/{{pad($dtr->date->day)}}/{{$dtr->employee->lid()}}">
              {{ $x }}. {{ $dtr->employee->lastname }}, {{ $dtr->employee->firstname }}
            </a>
          </td>
          <td class="text-right">
            {{ number_format($dtr->totworkhrs(),2) }}
            
          </td>
          <td class="text-right">

            {{ number_format($dtr->workhrs(),2) }}

          </td>
          <td class="text-right">

            {{ number_format($dtr->othrs(),2) }}

          </td>
          <td class="text-right">

            {{ number_format($dtr->tardyhrs,2) }}
          </td>
          <td class="text-right">
            {{ $dtr->timestart->format('H:i') == '00:00' ? '-': $dtr->timestart->format('h:i A') }}<br>
            {{ $dtr->timein->format('H:i') == '00:00' ? '-': $dtr->timein->format('h:i A') }}
          </td>
          <td class="text-right">
            {{ $dtr->breakstart->format('H:i') == '00:00' ? '-': $dtr->breakstart->format('h:i A') }}<br>
            {{ $dtr->breakin->format('H:i') == '00:00' ? '-': $dtr->breakin->format('h:i A') }}
          </td>
          <td class="text-right">
            {{ $dtr->breakend->format('H:i') == '00:00' ? '-': $dtr->breakend->format('h:i A') }}<br>
            {{ $dtr->breakout->format('H:i') == '00:00' ? '-': $dtr->breakout->format('h:i A') }}
          </td>
          <td class="text-right">
            {{ $dtr->timeend->format('H:i') == '00:00' ? '-': $dtr->timeend->format('h:i A') }}<br>
            {{ $dtr->timeout->format('H:i') == '00:00' ? '-': $dtr->timeout->format('h:i A') }}
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
