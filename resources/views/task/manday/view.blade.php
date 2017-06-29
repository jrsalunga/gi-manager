@extends('index')

@section('title', '- View Mansked')

@section('body-class', 'mansked-view')


@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li><a href="/task/mansked/{{$manday->date->year}}/week/{{$manday->date->weekOfYear}}">Week {{$manday->date->weekOfYear}}</a></li>
    <li class="active">{{ $manday->date->format('D, M j') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <!--
            <a href="/task/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
            -->
            <a href="/task/mansked/{{$manday->date->year}}/week/{{$manday->date->weekOfYear}}" class="btn btn-default">
              <span class="fa fa-calendar"></span>
              <span class="hidden-sm hidden-xs">{{$manday->date->year}}-W{{$manday->date->weekOfYear}}</span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
              <span class="hidden-sm hidden-xs">{{ $manday->date->format('M j') }}</span>
            </button>   
          </div>
          <div class="btn-group" role="group">
            @if(strtotime($manday->date) > strtotime('now'))
            <a href="/task/manday/{{strtolower($manday->id)}}/edit" class="btn btn-primary">
              <span class="glyphicon glyphicon-edit"></span>
              <span class="hidden-sm hidden-xs">{{ $edit ? 'Edit':'Create' }}</span>
            </a>
            @else
            <button type="button" class="btn btn-default" disabled>
              <span class="glyphicon glyphicon-edit"></span>
              <span class="hidden-sm hidden-xs">{{ $edit ? 'Edit':'Create' }}</span>
            </button>
            @endif
          </div><!-- end btn-grp -->
          
          <div class="btn-group pull-right" role="group">
            @if($manday->previous()==='false')
              <a href="/task/manday/" class="btn btn-default disabled">
            @else
              <a href="/task/manday/{{$manday->previous()->lid()}}" class="btn btn-default">
            @endif
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @if($manday->next()==='false')
              <a href="/task/manday/" class="btn btn-default disabled">
            @else
              <a href="/task/manday/{{$manday->next()->lid()}}" class="btn btn-default">
            @endif  
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    @include('_partials.alerts')
    
    <table class="table table-bordered">
      <tbody>
        <tr>
          <td rowspan="2" colspan="2">
            <div>
            {{ $manday->date->format('F j, Y') }}
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
              {{ number_format((($manday->empcount*$manday->manskedhdr->mancost)/($manday->custcount*$manday->headspend)*100),2) }} %
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
          @foreach ($hours as $key => $value) 
            <?php 
              $idx=$key>=24?$key-24:$key
            ?>
            <td data-value={{ date('g:i A', strtotime($idx.':00')) }}"" title="{{ $idx }}" class="text-center"> 
              {{ date('g A', strtotime($idx.':00')) }}
            </td>
          @endforeach
          </tr>
          <tr>
          @foreach ($hours as $key => $value)  
            <td class="text-right">{{ $value }}</td>
          @endforeach
          </tr>
      </tbody>
    </table>

    <table id="tb-mandtl" class="table table-bordered">
      <tbody>
        <tr>
          <td>Dept</td><td>Employee</td><td>Time Start</td><td>Break Start</td>
          <td>Break End</td><td>Time End</td><td>Work Hrs</td><td>Loading</td>
        </tr>
        <?php $ctr=1 ?>
        @foreach($depts as $dept)
          @for($i = 0; $i < count($dept['employees']); $i++)
          <?php 
            $bg = $dept['employees'][$i]->lid()==request()->input('employeeid') ? 'bg-success':'';
          ?>
            <tr class="{{$bg}}"  data-mandtl-id="{{ $dept['employees'][$i]['manskeddtl']['id'] }}">
              <td>{{ strtoupper($dept['code']) }}</td>
              <td>{{ $ctr }}. 
              
              <a href="/{{brcode()}}/timelog/employee/{{$dept['employees'][$i]->lid()}}?date={{$manday->date->format('Y-m-d')}}">
                {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} 
              </a>


              <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              @if($dept['employees'][$i]['manskeddtl']['daytype']==1)
                <td class="text-right">
                  <?php
                    $d = $dept['employees'][$i]['manskeddtl']['timestart'];
                    if($d=='off')
                      echo '-';
                    else
                      echo date('g:i A', strtotime($d)); 
                    ?>
                </td>
                <td class="text-right">
                  <?php
                    $d = $dept['employees'][$i]['manskeddtl']['breakstart'];
                    if($d=='off')
                      echo '-';
                    else
                      echo date('g:i A', strtotime($d)); 
                    ?>
                </td>
                <td class="text-right">
                  <?php
                    $d = $dept['employees'][$i]['manskeddtl']['breakend'];
                    if($d=='off')
                      echo '-';
                    else
                      echo date('g:i A', strtotime($d)); 
                    ?>
                </td>
                <td class="text-right">
                  <?php
                  $d = $dept['employees'][$i]['manskeddtl']['timeend'];
                  if($d=='off')
                    echo '-';
                  else
                    echo date('g:i A', strtotime($d)); 
                  ?>
                </td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['workhrs'] + 0 }}</td>
                <?php $l = $dept['employees'][$i]['manskeddtl']['loading'] ?>
                @if($l < 0)
                  <td class="text-right text-danger">{{ $l+0 }}</td>
                @elseif($l > 0)
                  <td class="text-right text-info">{{ $l+0 }}</td>
                @else
                  <td class="text-right">-</td>
                @endif

              @else
                @if($dept['employees'][$i]['manskeddtl']['daytype']>1)
                  <td colspan="6" class="text-center">
                    <span style="color: #bbb;">
                      {{ dayDesc($dept['employees'][$i]['manskeddtl']['daytype']) }}
                    </span>
                  </td>
                @else
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                  <td class="text-right">-</td>
                @endif
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

