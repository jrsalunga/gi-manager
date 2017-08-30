@extends('index')

@section('title', '- Timesheet ('.strtoupper(brcode()).')')

@section('body-class', 'timesheet-index')

@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/{{brcode()}}/dashboard">{{ $branch }}</a></li>
    <li><a href="/{{brcode()}}/timesheet">Timesheet</a></li>
    <li class="active">{{ $dr->date->format('D, M j, Y') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/task/mansked" class="btn btn-default">
              <span class="gly gly-notes-2"></span>
              <span class="hidden-xs hidden-sm">Mansked</span>
            </a> 
            <a href="/{{brcode()}}/timelog" class="btn btn-default">
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </button>
          </div> <!-- end btn-grp -->
          <div class="btn-group pull-right clearfix" role="group">
            <a href="/{{brcode()}}/timesheet?date={{ $dr->date->copy()->subDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->subDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <input type="text" class="btn btn-default" id="dp-date" value="{{ $dr->date->format('m/d/Y') }}" style="max-width: 110px;" readonly>
            <label class="btn btn-default" for="dp-date"><span class="glyphicon glyphicon-calendar"></span></label>
            <a href="/{{brcode()}}/timesheet?date={{ $dr->date->copy()->addDay()->format('Y-m-d') }}" class="btn btn-default" title="{{ $dr->date->copy()->addDay()->format('Y-m-d') }}">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
          <div class="btn-group" role="group">
            <a href="/timelog/add" class="btn btn-default">
              <span class="glyphicon glyphicon-plus"></span>
              <span class="hidden-xs hidden-sm">Add Timelog</span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if(count($data[1])>0)
      <div class="alert alert-important alert-warning">
        <p>There is other employee timelog from other store. Please contact system administrator</p>
      <ul>
      @foreach($data[1] as $key => $f)
        <?php $f->load('employee.branch'); ?>
        <li>{{ $f->employee->lastname }}, {{ $f->employee->firstname }} of {{ $f->employee->branch->code }} - {{ $f->entrytype==2?'Manual':'Punched' }} {{ $f->getTxnCode() }} - 
          {{ $f->datetime->format('D, M j, Y h:m:s A') }} created at {{ $f->createdate->format('D, M j, Y h:m:s A') }}</li>
      @endforeach
    </ul>
      </div>
    @endif



    @if(isset($data[0]) && count($data[0])>0)


    <div>
      <span></span>

    </div>

    <div class="table-responsive">
    <table class="table table-hover table-bordered" style="margin-top: 0px;">
      <thead>
        <tr>
          <th>Employee</th>
          <th class="text-right">Work Hours</th>
          <th class="text-right">Time In</th>
          <th class="text-right">Break Start</th>
          <th class="text-right">Break End</th>
          <th class="text-right">Time Out</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data[0] as $key => $e)
        <tr>
          <td <?=$e['onbr']?'':'class="bg-danger"'?>>
            {{ $key+1}}. 
            <a href="/{{brcode()}}/timelog/employee/{{$e['employee']->lid()}}?date={{$dr->date->format('Y-m-d')}}">
              {{ $e['employee']->lastname or '-' }}, {{ $e['employee']->firstname or '-' }}
            </a>
            <span class="label label-default pull-right" title="{{ $e['employee']->position->descriptor or '' }}">{{ $e['employee']->position->code or '' }}</span>
          </td>
          <td class="text-right">
            @if($e['timesheet']->workHours->format('H:i')==='00:00')
              -
            @else
              <small class="text-muted"><em>
                ({{ $e['timesheet']->workHours->format('H:i') }})</em> 
              </small>
              <strong>
                {{ $e['timesheet']->workedHours }}
              </strong>
            @endif
          </td>
            @foreach($e['timelogs'] as $key => $t)
              @if(is_null($t))
                <td class="text-right">-</td>
              @else
                <td class="text-right {{ $t['entrytype']=='2'?'bg-warning':'bg-success' }}" 
                title="{{ $t['datetime']->format('D, M j, Y h:i:s A') }} @ {{ $t['createdate']->format('D, M j, Y h:i:s A') }}">
                  
                  @if($e['counts'][$key]>1)

                    <a href="/{{brcode()}}/timelog/employee/{{$e['employee']->lid()}}?date={{$dr->date->format('Y-m-d')}}&txncode={{$t['txncode']}}" class="text-danger">
                    <span class="label label-danger pull-left" style="font-size: 9px;">{{ $e['counts'][$key] }}</span>
                    </a>
                      {{ $t['datetime']->format('h:i A') }}
                  @else
                    {{ $t['datetime']->format('h:i A') }}
                  @endif

                </td>
              @endif
           
            @endforeach
          
        </tr>
        @endforeach
      </tbody>
    </table>
    </div>

    <div style="margin: 10px 0;  font-size: 11px;">
      <span>Legends:</span> 

      <ul style="list-style: none;">
        <li><div style="min-width: 30px; display: inline-block;" class="bg-success">&nbsp;</div> Punch In/Out</li>
        <li><div style="min-width: 30px; display: inline-block;" class="bg-warning">&nbsp;</div> Manual Time In/Out</li>
        <li><div style="min-width: 30px; display: inline-block;" class="bg-danger">&nbsp;</div> Not Assigned on this Branch / Resigned / (RM or AM or SKH) </li>
      </ul>
      
    </div>

    @else
      No employee data
    @endif


  
    

      
    



</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent

  @include('_partials.js-vendor-highcharts')

<script>
   moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});

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
        document.location.href = '/{{brcode()}}/timesheet?date='+e.date.format('YYYY-MM-DD');
        
      });


      



   
  });
</script>
@endsection