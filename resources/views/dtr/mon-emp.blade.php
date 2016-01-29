@extends('index')

@section('title', '- Employee DTR')

@section('body-class', 'employee-dtr')

<?php
  $back_date = empty($_GET['day'])?now('day'):isDayNow($_GET['day'],'01');
  $prev = $date->copy()->subMonth();
  $next = $date->copy()->addMonth();
?>

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/dtr/{{$date->format('Y')}}">DTR {{$date->format('Y')}}</a></li>
    <li><a href="/dtr/{{$date->format('Y')}}/{{$date->format('m')}}">{{$date->format('M')}}</a></li>
    <li class="active">{{ $employee->code }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dtr/{{$date->format('Y')}}/{{$date->format('m')}}/{{ $back_date }}" class="btn btn-default">
              <span class="fa fa-calendar-o"></span>
            </a> 
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <a href="/dtr/{{$date->format('Y')}}/{{$date->format('m')}}/{{ $back_date }}/{{ $employee->lid() }}" class="btn btn-default">
              <span class="glyphicon glyphicon-user"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar"></span>
            </button>
          </div> <!-- end btn-grp -->

          <div class="btn-group pull-right" role="group">
            <a href="/dtr/{{$prev->format('Y')}}/{{$prev->format('m')}}/{{$employee->lid()}}?day={{$back_date}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a href="/dtr/{{$next->format('Y')}}/{{$next->format('m')}}/{{$employee->lid()}}?day={{$back_date}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div> <!-- end btn-grp -->

        </div>
      </div>
    </nav>

    @include('_partials.alerts')

  <div class="row">
    <div class="col-sm-6">
      <h3>{{ $employee->lastname }}, {{ $employee->firstname }} <small>{{ $employee->code }}</small></h3>  
      <p><em>DTR for the month of {{ $date->format('F Y') }} </em></p>
    </div>
    <div class="col-sm-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Total Work Hours</h3>
        </div>
        <div class="panel-body text-right">
          <h3>{{ (number_format($data['reghrs'],2)+0) }}
            <span class="small"> Hrs</span>
          </h3>
        </div>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Total Tardy Hours</h3>
        </div>
        <div class="panel-body text-right">
        <h3>{{ (number_format($data['tardy'],2)+0) }}
            <span class="small"> Hrs</span>
          </h3>
        </div>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col-md-12">
     
      <table id="tb-dtr" class="table table-bordered table-responsive">
        <thead>
          <tr>
            <th>Date</th>
            <th>Work Hrs</th>
            <th>Tardy Hrs</th>
            <th>Time In</th>
            <th>Break In</th>
            <th>Break Out</th>
            <th>Time Out</th>

          </tr>
        </thead>
        <tbody>
          @foreach($data['data'] as $dtr)
          <tr class="{{ $dtr->date->dayOfWeek=='0' ? 'alert-warning':'' }}">
            <td>
              <a href="/dtr/{{$dtr->date->format('Y')}}/{{$dtr->date->format('m')}}/{{$dtr->date->format('d')}}/{{$employee->id}}">
              {{ $dtr->date->format('m/d/Y') }}
              </a>
            </td>
            @if(is_null($dtr->dtr))
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            <td class="text-right">-</td>
            @else
            <td class="text-right">{{ (number_format($dtr->dtr->reghrs,2)+0)    != '0' ? (number_format($dtr->dtr->reghrs,2)+0) : '-' }}</td>
            <td class="text-right">{{ (number_format($dtr->dtr->tardyhrs,2)+0)  != '0' ? (number_format($dtr->dtr->tardyhrs,2)+0) :'-' }}</td>
            <td class="text-right">
              <div>{{ $dtr->dtr->timein->format('H:i')   == '00:00' ? '-': $dtr->dtr->timein->format('h:i A') }}</div>
              <div class="tooltip-mansked">{{ $dtr->dtr->timestart->format('H:i')  == '00:00' ? '-': $dtr->dtr->timestart->format('h:i A') }}</div>
            </td>
            <td class="text-right">
              <div>{{ $dtr->dtr->breakin->format('H:i')  == '00:00' ? '-': $dtr->dtr->breakin->format('h:i A') }}</div>
              <div class="tooltip-mansked">{{ $dtr->dtr->breakstart->format('H:i') == '00:00' ? '-': $dtr->dtr->breakstart->format('h:i A') }}</div>
            </td>
            <td class="text-right">
              <div>{{ $dtr->dtr->breakout->format('H:i') == '00:00' ? '-': $dtr->dtr->breakout->format('h:i A') }}</div>
              <div class="tooltip-mansked">{{ $dtr->dtr->breakend->format('H:i')   == '00:00' ? '-': $dtr->dtr->breakend->format('h:i A') }}</div>
            </td>
            <td class="text-right">
              <div>{{ $dtr->dtr->timeout->format('H:i')  == '00:00' ? '-': $dtr->dtr->timeout->format('h:i A') }}</div>
              <div class="tooltip-mansked">{{ $dtr->dtr->timeend->format('H:i')    == '00:00' ? '-': $dtr->dtr->timeend->format('h:i A') }}</div>
            </td>
            @endif
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>


   
   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent

  <script>
    $(document).ready(function(){
      $('.tooltip-mansked').hide();
    });
  </script>
  
@endsection
