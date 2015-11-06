@extends('index')

@section('title', '- Create Daily Man Schedule')

@section('body-class', 'mansked-create')


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
            </a>
            <button type="button" class="btn btn-default active">
              <span class="gly gly-table"></span>
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
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    

    <table class="table table-bordered table-responsive">
      <tbody>
        <tr>
          <td colspan="2" class="nbtl">
            <i class="fa fa-calendar"></i> {{ $mansked->year }} - Week {{ $mansked->weekno }}
          </td>
          @for($i=0;$i<7;$i++)
          <td>
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
            <abbr title="{{ session('user.branch') }} - &#8369 {{ session('user.branchmancost') }}">Manpower Cost</abbr> %</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->computeMancost($mansked->mancost, true) }}</td>
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
            <td>
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
              <td><?=strtolower($dept['name'])=='dining'?'DIN':'KIT';?></td>
              <td>{{ $ctr }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              
                @foreach($dept['employees'][$i]['manskeddays'] as $manday)
                  @if(!empty($manday['mandtl']['daytype']))
                    <td>
                      <div>
                        {{ empty($manday['mandtl']['timestart']) ? '':date('g:i', strtotime($manday['mandtl']['timestart'])) }} - 
                        {{ empty($manday['mandtl']['timeend']) ? '':date('g:i', strtotime($manday['mandtl']['timeend'])) }}
                      </div>
                      <div>
                        @if($manday['mandtl']['loading'] > 0)
                          <span class="label label-primary" style="letter-spacing: 2px;">+{{ $manday['mandtl']['loading']+0 }}</span>
                        @elseif($manday['mandtl']['loading'] < 0)
                          <span class="label label-danger" style="letter-spacing: 2px;">{{ $manday['mandtl']['loading']+0 }}</span>
                        @else
                           - 
                        @endif
                      </div>
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

