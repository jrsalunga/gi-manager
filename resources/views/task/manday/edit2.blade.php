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
            <a href="/task/manday/{{strtolower($manday->id)}}" class="btn btn-default">
              <span class="fa fa-calendar-o"></span>
            </a>   
          </div>
          <div class="btn-group" role="group">
            
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-edit"></span>
            </button>
            
          </div><!-- end btn-grp -->
        </div>
      </div>
    </nav>

    @foreach($errors->all() as $message) 
      <div class="alert alert-danger" role="alert">
      {{ $message }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      </div>
    @endforeach


    <form method="post" action="/api/t/manskedday/{{strtolower($manday->id)}}" id="frm-manskedday" name="frm-manskedday" role="form" data-table="manskedday">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <table class="table table-bordered">
      <tbody>
        <tr>
          <td rowspan="2" colspan="2">
            {{ date('F j, Y', strtotime($manday->date)) }}
          </td>
          <td>
            Forecast Pax
          </td>
          <td>
            Head Spend
          </td>
          <td>
            Emp Count
          </td>
          <td>
            Man Cost
          </td>
          <td colspan="2">
            Total Work Hrs
          </td>
          <td>
            Over Load
          </td>
          <td>
            Under Load
          </td>
        </tr>
        <tr>
          <td class="text-right">
            
            {{ number_format($manday->custcount,0) }}
          </td>
          <td class="text-right">
            &#8369; {{ number_format($manday->headspend, 2) }}
          </td>
          <td class="text-right">
            {{ $manday->empcount }}
          </td>
          <td class="text-right">
           {{ $manday->mancost }} %
          </td>
          <td colspan="2" class="text-right">
            {{ $manday->workhrs }}
          </td>
          <td class="text-right">
            {{ $manday->overload }}
          </td>
          <td class="text-right">
            {{ $manday->underload }}
          </td>
        </tr>
      </tbody>
    </table>

    <table id="tb-mandtl" class="table table-bordered">
      <tbody>
        <tr>
          <td>
            Dept
          </td>
          <td >
            Employee
          </td>
          <td>
            Time Start
          </td>
          <td>
            Break Start
          </td>
          <td>
            Break End
          </td>
          <td>
            Time End
          </td>
          <td>
            Work Hrs
          </td>
          <td>
            Loading
          </td>
        </tr>
        <?php $ctr=1 ?>
        @foreach($depts as $dept)
          @for($i = 0; $i < count($dept['employees']); $i++)
            <tr>
              <td><?=strtolower($dept['name'])=='dining'?'DIN':'KIT';?></td>
              <td>{{ $ctr }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              @if($dept['employees'][$i]['manskeddtl']['daytype']==1)
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['timestart'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['breakstart'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['breakend'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['timeend'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['workhrs'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['loading'] }}</td>
              @else
                
                <td class="text-right text-input">
                  <select name="manskeddtls[{{ $ctr }}][timestart]" class="frm-ctrl tk-select timestart"> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      @if($dept['employees'][$i]['manskeddtl']['timestart'] == date('G:i', strtotime( $j .':00')))
                        <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif

                      @if($dept['employees'][$i]['manskeddtl']['timestart'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                    @endfor
                  </select>
                </td>
                <td class="text-right text-input">
                  <select name="manskeddtls[{{ $ctr }}][breakstart]" class="frm-ctrl tk-select breakstart" disabled> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      @if($dept['employees'][$i]['manskeddtl']['breakstart'] == date('G:i', strtotime( $j .':00')))
                        <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif

                      @if($dept['employees'][$i]['manskeddtl']['breakstart'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                    @endfor
                  </select>
                </td>
                <td class="text-right text-input">
                  <select name="manskeddtls[{{ $ctr }}][breakend]" class="frm-ctrl tk-select breakend" disabled> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      @if($dept['employees'][$i]['manskeddtl']['breakend'] == date('G:i', strtotime( $j .':00')))
                        <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif
                      
                      @if($dept['employees'][$i]['manskeddtl']['breakend'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                    @endfor
                  </select>
                </td>
                <td class="text-right text-input">
                  <select name="manskeddtls[{{ $ctr }}][timeend]" class="frm-ctrl tk-select timeend" disabled> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      @if($dept['employees'][$i]['manskeddtl']['timeend'] == date('G:i', strtotime( $j .':00')))
                        <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif

                      @if($dept['employees'][$i]['manskeddtl']['timeend'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                    @endfor
                  </select>
                </td>
              
                <!--
                <td class="text-input"><input type="text" class="frm-ctrl"></td>
                <td class="text-input"><input type="text" class="frm-ctrl"></td>
                <td class="text-input"><input type="text" class="frm-ctrl"></td>
                <td class="text-input"><input type="text" class="frm-ctrl"></td>
                -->

                <td class="text-right">-</td>
                <td class="text-right">-</td>
              @endif
            </tr>
            <?php $ctr++ ?>
          @endfor
        @endforeach
      </tbody>
    </table>


    <div class="row button-container">
      <div class="col-md-6">
        <a href="{{ URL::previous() }}" class="btn btn-default">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
    </form>

    
    
      
  </div>


<!-- end main -->
</div>
@endsection




@section('js-external')
  @parent
  

<script>
  $('document').ready(function(){

   // $('#date').datepicker({'format':'yyyy-mm-dd'})

   $('select.timestart').on('change', function(e){
      
      var x = ($(this)[0].value=='off') ? 0:1; 
      console.log(x);
      if(x==0){  
        var d = true;
      } else {
        var d = false;
      }

      $(this).parent().siblings('td').children('.frm-ctrl').each(function(el){
        //console.log($(this)[0].value);
        if(d)
          $(this)[0].value = 'off'
        $(this)[0].disabled = d;
      });

      
    });

    $('select.breakstart').on('change', function(e){
      
      var x = ($(this)[0].value=='off') ? 0:1; 
      console.log(x);
      //$(this).parent().children('.daytype').val(x);
    });



     $("#date").datepicker({ minDate: 1, dateFormat: 'yy-mm-dd',});
  });
</script>
@endsection

