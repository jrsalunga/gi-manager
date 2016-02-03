@extends('index')

@section('title', '- Employee DTR')

@section('body-class', 'employee-dtr')

<?php
  $prev = $date->copy()->subDay();
  $next = $date->copy()->addDay();
?>

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/dtr/{{$date->format('Y')}}">DTR {{$date->format('Y')}}</a></li>
    <li><a href="/dtr/{{$date->format('Y')}}/{{$date->format('m')}}">{{ $date->format('M') }}</a></li>
    <li><a href="/dtr/{{$date->format('Y')}}/{{$date->format('m')}}/{{$date->format('d')}}">{{ $date->format('d') }}</a></li>
    <li class="active">{{ $employee->code }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dtr/{{$date->year}}/{{pad($date->month)}}/{{pad($date->day)}}" class="btn btn-default">
              <span class="fa fa-calendar-o"></span>
            </a> 
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-user"></span>
            </button>
            <a href="/dtr/{{$date->year}}/{{pad($date->month)}}/{{$employee->lid()}}?day={{$date->format('d')}}" class="btn btn-default">
              <span class="fa fa-calendar"></span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group pull-right" role="group">
            <a href="/dtr/{{$prev->format('Y')}}/{{$prev->format('m')}}/{{$prev->format('d')}}/{{$employee->lid()}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a href="/dtr/{{$next->format('Y')}}/{{$next->format('m')}}/{{$next->format('d')}}/{{$employee->lid()}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    @include('_partials.alerts')
      
    @if($dtrs)
      <div class="row">
        
        <div class="col-sm-6">
          <h3>{{ $employee->lastname }}, {{ $employee->firstname }} <small>{{ $employee->code }}</small></h3>  
          <h4><span class=""></span> {{ $dtrs->date->format('l, F d, Y') }} <span class="small">{{ $dtrs->getDayType() }}</span></h4>

        </div>

        <div class="col-sm-3">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Work Hours</h3>
            </div>
            <div class="panel-body text-right">
              <h3>{{ (number_format($dtrs->reghrs,2)+0) }}
                <span class="small"> Hrs</span>
              </h3>
            </div>
          </div>
        </div>
        
        <div class="col-sm-3">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title">Tardy</h3>
            </div>
            <div class="panel-body text-right">
              <h3>{{ (number_format($dtrs->tardyhrs,2)+0) }}
                <span class="small"> Hrs</span>
              </h3>
            </div>
          </div>
          
        </div>
      </div>

      <table class="table table-bordered table-responsive">
        <tbody>
          <tr>
            <td>DTR Details

              <table class="table">
                <thead>
                  <tr>
                    <th></th>
                    <th>Time Start/In</td>
                    <th>Break Start/In</th>
                    <th>Break End/Out</th>
                    <th>Time End/Out</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Mansked</td>
                    <td>{{ $dtrs->timestart->format('H:i') == '00:00' ? '-': $dtrs->timestart->format('h:i A') }}</td>
                    <td>{{ $dtrs->breakstart->format('H:i') == '00:00' ? '-': $dtrs->breakstart->format('h:i A') }}</td>
                    <td>{{ $dtrs->breakend->format('H:i') == '00:00' ? '-': $dtrs->breakend->format('h:i A') }}</td>
                    <td>{{ $dtrs->timeend->format('H:i') == '00:00' ? '-': $dtrs->timeend->format('h:i A') }}</td>
                  </tr>
                  <tr>
                    <td>Timelog</td>
                    <td>{{ $dtrs->timein->format('H:i') == '00:00' ? '-': $dtrs->timein->format('h:i A') }}</td>
                    <td>{{ $dtrs->breakin->format('H:i') == '00:00' ? '-': $dtrs->breakin->format('h:i A') }}</td>
                    <td>{{ $dtrs->breakout->format('H:i') == '00:00' ? '-': $dtrs->breakout->format('h:i A') }}</td>
                    <td>{{ $dtrs->timeout->format('H:i') == '00:00' ? '-': $dtrs->timeout->format('h:i A') }}</td>
                  </tr>
                </tbody>
              </table>
            </td>
          </tr>
        </tbody>
      </table>
      <table class="table table-bordered table-responsive">
        <tbody>
          <tr>
            <td>Raw Timelog Details

              @if($timelogs)
              <table class="table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>RFID</th>
                    <th>Time Date</th>
                    <th>Txn Type</th>
                    <th>Terminal</th>
                  </tr>
                </thead>
                <tbody>
                  
                  <?php $ctr=1; ?>
                  @foreach($timelogs as $timelog)
                    <tr>
                      <td>{{ $ctr }}</td>
                      <td>{{ $timelog->rfid }}</td>
                      <td title="created at: {{ $timelog->createdate->format('h:i A m/d/Y') }}">{{ $timelog->datetime->format('h:i A m/d/Y') }}</td>
                      <td>{{ $timelog->getTxnCode() }}</td>
                      <td>{{ $timelog->terminalid }}</td>
                    </tr>
                    <?php $ctr++; ?>
                  @endforeach
                </tbody>
              </table>

              <div class="alert alert-info alert-important" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                <strong>Note:</strong> Timelog are captured between 6AM of the same day until 6AM of the following day.  
              </div>
              @else
                <h4>No Timelog!</h4>
              @endif
            </td>
          </tr>
        </tbody>
      </table>
    @else 
      <h4>No Record Found!</h4>
    @endif
    

    

    

   
   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent
  
@endsection
