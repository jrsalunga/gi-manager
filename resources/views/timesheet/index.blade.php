@extends('index')

@section('title', '- Timesheet')

@section('body-class', 'timesheet-index')

@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/dashboard">{{ $branch }}</a></li>
    <li>Time Sheet</li>
    <li class="active">{{ $dr->date->format('D, M j, Y') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->
          <div class="btn-group pull-right clearfix" role="group">
            <a href="/timesheet?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
            <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            <a href="/timesheet?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(count($data[1])>0)
      <div class="alert alert-important alert-warning">There is another timelog. Please contact us @ jefferson.salunga@gmail.com</div>
    @endif



    @if(count($data[0])>0)
    <div class="table-responsive">
    <table class="table table-hover table-striped">
      <thead>
        <tr>
          <th>Employee</th>
          <th class="text-right">Time In</th>
          <th class="text-right">Break In</th>
          <th class="text-right">Break Out</th>
          <th class="text-right">Time Out</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data[0] as $key => $e)
        <tr>
          <td>{{ $key+1}}. {{ $e['employee']->lastname }}, {{ $e['employee']->firstname }}</td>
            @foreach($e['timelogs'] as $key => $t)
              @if(is_null($t))
                <td class="text-right">-</td>
              @else
                <td class="text-right" title="{{ $t['datetime']->format('D, M j, Y h:m A') }}">
                  {{ $t['datetime']->format('h:m A') }}
                </td>
              @endif
           
            @endforeach
          
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>
    @else
      No data
    @endif


  
    

      
    



</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent

  @include('_partials.js-vendor-highcharts')

<script>
  $('document').ready(function(){

  	$('#dp-date').datetimepicker({
        defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        calendarWeeks: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        document.location.href = '/timesheet?date='+e.date.format('YYYY-MM-DD');
        
      });


      



   
  });
</script>
@endsection