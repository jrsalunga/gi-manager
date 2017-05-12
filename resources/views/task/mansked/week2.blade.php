@extends('index')

@section('title', '- Weekly Mansked')

@section('body-class', 'mansked-week')


@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li class="active">Week {{$mansked->weekno}}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/task/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs hidden-sm">Mansked List</span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="gly gly-table"></span>
              <span class="hidden-xs hidden-sm">{{ $mansked->year }}-W{{ $mansked->weekno }}</span>
            </button>   
          </div>

          
          <div class="btn-group pull-right" role="group">
            @if($mansked->previous()==='false')
              <button class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-chevron-left"></span></button>
            @else
              <a href="/task/mansked/{{$mansked->previous()->year}}/week/{{$mansked->previous()->weekno}}" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span></a>
            @endif
            
            @if($mansked->next()==='false')
              <button class="btn btn-default" disabled="disabled"><span class="glyphicon glyphicon-chevron-right"></span></button>
            @else
              <a href="/task/mansked/{{$mansked->next()->year}}/week/{{$mansked->next()->weekno}}" class="btn btn-default"><span class="glyphicon glyphicon-chevron-right"></span></a>
            @endif  
             
          </div>

          <div class="btn-group pull-right" role="group" style="margin-right: 5px;">
            <a href="{{ request()->url() }}?print=true" class="btn btn-default" target="_blank">
              <span class="glyphicon glyphicon-print"></span>
              <span class="hidden-xs hidden-sm">Print</span>
            </a>   
          </div>
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    
    <div class="table-responsive">
    <table class="table table-bordered">
      <tbody>
        <tr>
          <td colspan="2" class="nbtl">
            <i class="fa fa-calendar"></i> {{ $mansked->year }} - Week {{ $mansked->weekno }}
          </td>
          @for($i=0;$i<7;$i++)
          <td class="text-center">
            <a href="/task/manday/{{  $mansked->manskeddays[$i]->lid() }}">
              {{ $mansked->manskeddays[$i]->date->format('D, M d') }}
            </a>
          </td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Forecasted Customer</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->custCount() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Forecasted Ave Spending</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->headSpend() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Total Crew On-duty</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->empCount() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Total Work Hours</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->workHrs() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">
            Manpower Cost %</td>
          @for($i=0;$i<7;$i++)
            <td class="text-right">
              <abbr title="Man Cost: &#8369 {{ $mansked->mancost }}">  
                {{ $mansked->manskeddays[$i]->computeMancost($mansked->mancost, true) }}
              </abbr>
            </td>
          @endfor
        </tr>
        <!--
        <tr>
          <td colspan="2" class="text-right nbtl">
            <abbr title="{{ session('user.branch') }} - &#8369 {{ session('user.branchmancost') }}/8">Work Hour Cost</abbr> %</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->computeHourcost($mansked->mancost, true) }}</td>
          @endfor
        </tr>
      -->
        <tr>
          <td colspan="2" class="text-right nbtl">Loading</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{!! $mansked->manskeddays[$i]->loadings() !!}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="9" style="border-left: 1px solid #fff; border-right: 1px solid #fff;">&nbsp;</td>
        </tr>
        <tr>
          <td>Dept</td><td>Employee</td>
          @foreach($mansked->manskeddays as $manday)
            <td class="text-center">
              <a href="/task/manday/{{ $manday->lid() }}">
                {{ $manday->date->format('D, M d') }}
              </a>
            </td>
          @endforeach
        
        </tr>
        <?php $ctr=1 ?>
        @foreach($depts as $dept)
          @for($i = 0; $i < count($dept['employees']); $i++)
            <tr>
              <td>{{ strtoupper($dept['code']) }}</td>
              <td>{{ $ctr }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} <span class="label label-default pull-right">{{ empty($dept['employees'][$i]->position->code) ? '':$dept['employees'][$i]->position->code }}</span></td>
              
                @foreach($dept['employees'][$i]['manskeddays'] as $manday)
                  @if(!empty($manday['mandtl']['daytype']))
                    <td>
                      <span class="pull-left">
                        {{ empty($manday['mandtl']['timestart']) || $manday['mandtl']['timestart']=='off' ? '':date('gA', strtotime($manday['mandtl']['timestart'])) }} 
                        - 
                        {{ empty($manday['mandtl']['timeend']) || $manday['mandtl']['timeend']=='off' ? '':date('gA', strtotime($manday['mandtl']['timeend'])) }}
                      </span>
                      
                        @if($manday['mandtl']['loading'] > 0)
                          <span class="label label-primary pull-right" style="letter-spacing: 2px;">+{{ $manday['mandtl']['loading']+0 }}</span>
                        @elseif($manday['mandtl']['loading'] < 0)
                          <span class="label label-danger pull-right" style="letter-spacing: 2px;">{{ $manday['mandtl']['loading']+0 }}</span>
                        @else
                           - 
                        @endif
                      
                    </td>
                  @else
                    <td>-</td>
                  @endif
                @endforeach
             
            </tr>
            <?php $ctr++ ?>
          @endfor
        @endforeach
      </tbody>
    </table>
    </div> <!-- end: .table-responsive -->
    
    
      
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

