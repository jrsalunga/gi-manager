@extends('index')

@section('title', '- Timelog List ('.strtoupper(brcode()).')')

@section('body-class', 'timelog-list')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/{{brcode()}}/dashboard">{{ $branch }}</a></li>
    <li class="active">Timelog</li>
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
            <button type="button" class="btn btn-default active">
              <span class="gly gly-stopwatch"></span>
              <span class="hidden-xs hidden-sm">Timelogs</span>
            </button>
            <a href="/{{brcode()}}/timesheet" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Timesheet</span>
            </a>
          </div> <!-- end btn-grp -->
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

    {{-- $timelogs->total() --}}

    <table class="table table-condensed table-striped">
      <thead>
        <tr>
          <th colspan="2">Employee</th><th>&nbsp;</th><th>Log</th><th>Date/Time</th><th>Entry Type</th><th>IP Address</th>
        </tr>
      </thead>
      <tbody>
        @foreach($timelogs as $timelog)
        <tr>
          <td style="width: 30px;">
            <span class="label label-default help" data-toggle="tooltip" title="{{ $timelog->employee->position->descriptor or 'd'}}">
              {{ $timelog->employee->position->code or 'd'}}
            </span>
          </td>
          <td>
            <?php 
              $src=$timelog->employee->photo?'employees/'.$timelog->employee->code.'.jpg':'login-avatar.png';
            ?>
            <a href="/{{brcode()}}/timelog/employee/{{stl($timelog->employeeid)}}?date={{$timelog->datetime->format('Y-m-d')}}"  rel="popover-img" data-img="http://cashier.giligansrestaurant.com/images/{{$src}}">
              {{ $timelog->employee->lastname }}, {{ $timelog->employee->firstname }}
            </a>
          </td>
          <td>
            <a href="/{{brcode()}}/timesheet?date={{$timelog->datetime->format('Y-m-d')}}&employeeid={{stl($timelog->employeeid)}}" style="color: #ccc;" data-toggle="tooltip" title="Go to {{ $timelog->branch->code }}'s {{ $timelog->datetime->format('D, M j, Y') }} Timesheet">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
          </td>
          <td> 
            <span class="label label-{{ $timelog->txnClass() }}" data-toggle="tooltip" title="{{ $timelog->getTxnCode() }}">
              {{ $timelog->getTxnCode() }} 
            </span>
          
          </td>
          <td>
            <span class="help" data-toggle="tooltip" title="{{ $timelog->datetime->format('m/d/Y h:i:s A') }}">
              @if($timelog->datetime->format('Y-m-d')==now())
                
                {{ $timelog->datetime->format('h:i A') }}

                <em class="hidden-xs">
                  <small class="text-muted">
                  {{ diffForHumans($timelog->datetime) }}
                  </small>
                </em>
              @else
                {{ $timelog->datetime->format('h:i A') }}
                <small class="text-muted">
                {{ $timelog->datetime->format('M j, D') }}
                </small>
              @endif
            </span> 
          </td>
          <td>
            <span class="label label-{{ $timelog->entryClass() }} help" data-toggle="tooltip" title="{{ $timelog->getEntry() }}">
              {{ $timelog->getEntry() }}
            </span>
          </td>
          <td>
            <small class="text-muted">{{ $timelog->terminalid }}</small>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    
    {!! $timelogs->render() !!}

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent
  
  <script>
  
    
 
  </script>
@endsection
