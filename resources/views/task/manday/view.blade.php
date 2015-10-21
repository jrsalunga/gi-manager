@extends('index')

@section('title', '- Create Daily Man Schedule')

@section('body-class', 'mansked-create')

<?php
  $wn = Carbon\Carbon::parse($manday->date)->weekOfYear;
?>

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li><a href="/task/mansked/week/{{$wn}}">Week {{$wn}}</a></li>
    <li class="active">{{ date('D, M j',strtotime($manday->date)) }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/task/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a href="/task/mansked/week/{{$wn}}" class="btn btn-default">
              <span class="gly gly-table"></span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
            </button>   
          </div>
          <div class="btn-group" role="group">
            @if(strtotime($manday->date) > strtotime('now'))
            <a href="/task/manday/{{strtolower($manday->id)}}/edit" class="btn btn-default">
              <span class="glyphicon glyphicon-edit"></span>
            </a>
            @else
            <button type="button" class="btn btn-default" disabled>
              <span class="glyphicon glyphicon-edit"></span>
            </button>
            @endif
          </div><!-- end btn-grp -->
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    <table class="table table-bordered">
      <tbody>
        <tr>
          <td rowspan="2" colspan="2">
            <div>
            {{ date('F j, Y', strtotime($manday->date)) }}
            </div>
            @if(strtotime($manday->date) < strtotime('now'))
              <span class="label label-warning">[ readonly ]</span>
            @endif
          </td>
          <td>Forecast Pax</td><td>Head Spend</td><td>Emp Count</td><td>Man Cost</td>
          <td colspan="2">Total Work Hrs</td><td>Over Load</td><td>Under Load</td>
        </tr>
        <tr>
          <td class="text-right text-input">
            
            {{ number_format($manday->custcount,0) }}
          </td>
          <td class="text-right text-input">
            &#8369; {{ number_format($manday->headspend, 2) }}
          </td>
          <td class="text-right">
            {{ $manday->empcount+0 }}
          </td>
          <td class="text-right">
            @if(($manday->custcount*$manday->headspend)!= 0)
              {{ number_format((($manday->empcount*500)/($manday->custcount*$manday->headspend)*100),2) }} %
            @else 
              -
            @endif
          </td>
          <td colspan="2" class="text-right">
            {{ $manday->workhrs+0 }}
          </td>
          <td class="text-right">
            {{ $manday->overload+0 }}
          </td>
          <td class="text-right">
            {{ $manday->underload+0 }}
          </td>
        </tr>
      </tbody>
    </table>

    <table class="table table-bordered">
      <tbody>
        <tr>
          <td>Dept</td><td>Employee</td><td>Time Start</td><td>Break Start</td>
          <td>Break End</td><td>Time End</td><td>Work Hrs</td><td>Loading</td>
        </tr>
        <?php $ctr=1 ?>
        @foreach($depts as $dept)
          @for($i = 0; $i < count($dept['employees']); $i++)
            <tr>
              <td><?=strtolower($dept['name'])=='dining'?'DIN':'KIT';?></td>
              <td>{{ $ctr }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              @if($dept['employees'][$i]['manskeddtl']['daytype']==1)
                <td class="text-right">{{ date('g:i A', strtotime($dept['employees'][$i]['manskeddtl']['timestart'])) }}</td>
                <td class="text-right">{{ date('g:i A', strtotime($dept['employees'][$i]['manskeddtl']['breakstart'])) }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['breakend'] }}</td>
                <td class="text-right">{{ date('g:i A', strtotime($dept['employees'][$i]['manskeddtl']['timeend'])) }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['workhrs'] + 0 }}</td>
                <?php $l = $dept['employees'][$i]['manskeddtl']['loading'] ?>
                <td class="text-right{{ ($l >= 0) ? '':' text-danger' }}">{{ ($l == 0) ? '-':$l+0 }}</td>
              @else
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>

                <td class="text-right">-</td>
                <td class="text-right">-</td>
              @endif
            </tr>
            <?php $ctr++ ?>
          @endfor
        @endforeach
      </tbody>
    </table>

    
    
      
  </div>


<!-- end main -->
</div>
@endsection




@section('js-external')
  @parent
  

<script>
  $('document').ready(function(){

   // $('#date').datepicker({'format':'yyyy-mm-dd'})

    $('select.form-control').on('change', function(e){
      //console.log(e);
      var x = ($(this)[0].value=='off') ? 0:1; 
     $(this).parent().children('.daytype').val(x);
    });



     $("#date").datepicker({ minDate: 1, dateFormat: 'yy-mm-dd',});
     $('.alert').not('.alert-important').delay(5000).slideUp(300);
  });
</script>
@endsection

