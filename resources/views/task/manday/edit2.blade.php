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
    <input type="hidden" id="id" name="id" value="{{ $manday->id }}">
    <table class="table table-bordered" id="tb-manday">
      <tbody>
        <tr>
          <td rowspan="2" colspan="2">
            {{ date('F j, Y', strtotime($manday->date)) }}
            
            <input type="hidden" name="empcount" id="empcount" value="{{ $manday->empcount }}">
            <input type="hidden" name="workhrs" id="workhrs" value="{{ $manday->workhrs }}">
            <input type="hidden" name="breakhrs" id="breakhrs" value="{{ $manday->breakhrs }}">
            <input type="hidden" name="overload" id="overload" value="{{ $manday->overload }}">
            <input type="hidden" name="underload" id="underload" value="{{ $manday->underload }}">
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
          <td class="text-right text-input">
            
            <input type="text" name="custcount" id="custcount" class="frm-ctrl text-right" value="{{ $manday->custcount }}" autofocus onfocus="this.value = this.value">
          </td>
          <td class="text-right text-input">
            
            <input type="text" name="headspend" id="headspend" class="frm-ctrl text-right" value="{{ $manday->headspend }}">
          </td>
          <td class="text-right tb-empcount">
            {{ $manday->empcount }}
          </td>
          <td class="text-right tb-mancost">
           {{ $manday->mancost }} %
          </td>
          <td colspan="2" class="text-right tb-workhrs">
            {{ $manday->workhrs+0 }}
          </td>
          <td class="text-right tb-overload">
            {{ $manday->overload+0 }}
          </td>
          <td class="text-right tb-underload">
            {{ $manday->underload+0 }}
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
            <?php
              $disabled = $dept['employees'][$i]['manskeddtl']['daytype'] == 0 ? 'disabled':'';
            ?>
            <tr>
              <td><?=strtolower($dept['name'])=='dining'?'DIN':'KIT';?>
                <input type="hidden" id="manskeddtl{{ $ctr }}id" name="manskeddtls[{{ $ctr }}][id]" value="{{ $dept['employees'][$i]['manskeddtl']['id'] }}">
                <input type="hidden" id="manskeddtl{{ $ctr }}daytype" name="manskeddtls[{{ $ctr }}][daytype]" class="daytype" value="{{ $dept['employees'][$i]['manskeddtl']['daytype'] }}">
                <input type="hidden" id="manskeddtl{{ $ctr }}employeeid" name="manskeddtls[{{ $ctr }}][employeeid]" value="{{ $dept['employees'][$i]->id }}">
                <input type="hidden" id="manskeddtl{{ $ctr }}workhrs" name="manskeddtls[{{ $ctr }}][workhrs]" value="{{ empty($dept['employees'][$i]['manskeddtl']['workhrs']) ? 0:$dept['employees'][$i]['manskeddtl']['workhrs'] }}" class="workhrs">
                <input type="hidden" id="manskeddtl{{ $ctr }}breakhrs" name="manskeddtls[{{ $ctr }}][breakhrs]" value="{{ empty($dept['employees'][$i]['manskeddtl']['breakhrs']) ? 0:$dept['employees'][$i]['manskeddtl']['breakhrs'] }}" class="breakhrs">
                <input type="hidden" id="manskeddtl{{ $ctr }}loading" name="manskeddtls[{{ $ctr }}][loading]" value="{{ empty($dept['employees'][$i]['manskeddtl']['loading']) ? 0:$dept['employees'][$i]['manskeddtl']['loading'] }}" class="loading">  
              </td>
              <td>{{ $ctr }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              @if($dept['employees'][$i]['manskeddtl']['daytype']==2)
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['timestart'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['breakstart'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['breakend'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['timeend'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['workhrs'] }}</td>
                <td class="text-right">{{ $dept['employees'][$i]['manskeddtl']['loading'] }}</td>
              @else
                
                <td class="text-right text-input">
                  <select name="manskeddtls[{{ $ctr }}][timestart]" class="frm-ctrl tk-select timestart" data-index="{{$ctr}}"> 
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
                <td class="text-right text-input {{ $disabled }}">
                  <select name="manskeddtls[{{ $ctr }}][breakstart]" class="frm-ctrl tk-select breakstart" data-index="{{$ctr}}" {{ $disabled }}> 
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
                <td class="text-right text-input {{ $disabled }}">
                  <select name="manskeddtls[{{ $ctr }}][breakend]" class="frm-ctrl tk-select breakend" data-index="{{$ctr}}" {{ $disabled }}> 
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
                <td class="text-right text-input {{ $disabled }}">
                  <select name="manskeddtls[{{ $ctr }}][timeend]" class="frm-ctrl tk-select timeend" data-index="{{$ctr}}" {{ $disabled }}> 
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
                <td class="text-right td-workhrs">
                  @if($dept['employees'][$i]['manskeddtl']['workhrs']==0)
                    -
                  @else 
                    {{ $dept['employees'][$i]['manskeddtl']['workhrs']+0 }}
                  @endif
                </td>
                <td class="text-right td-loading">
                  @if($dept['employees'][$i]['manskeddtl']['loading']==0)
                    -
                  @else 
                    {{ $dept['employees'][$i]['manskeddtl']['loading']+0 }}
                  @endif
                </td>
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
var today = moment().format("YYYY-MM-D");

var updateMancost = function(){
  //console.log('mancost');
  var m = 0;
  var e = (isNaN($('#empcount')[0].value)) ? 0: parseFloat($('#empcount')[0].value);
  var m = 500;
  var c = (isNaN($('#custcount')[0].value)) ? 0: parseFloat($('#custcount')[0].value);
  var h = (isNaN($('#headspend')[0].value)) ? 0: parseFloat($('#headspend')[0].value);
  var mancost = ((e*m)/(c*h)*100);
  mancost = (isNaN(mancost) || !isFinite(mancost)) ? 0 : mancost;
  $('.tb-mancost').text(mancost.toFixed(2)+' %');
}

var calc = function (fr, to) {
  var timestart = moment(today+' '+fr);
  var breakstart = moment(today+' '+to);
  return breakstart.diff(timestart, 'hours', true);
}

var updateWorkhrs = function(el){

  var tr = el.parent().parent();
  var ts = tr.children('td').children('.timestart');
  var bs = tr.children('td').children('.breakstart');
  var be = tr.children('td').children('.breakend');
  var te = tr.children('td').children('.timeend');

  var time1 = 0;
  var time2 = 0;
  
  if(ts.val()!='off' && bs.val()!='off'){
    //console.log('time1 on');
    time1 = calc(ts.val(), bs.val());
  }
  if(be.val()!='off' && te.val()!='off'){
    //console.log('time2 on');
    time2 = calc(be.val(), te.val());
  }
  var workhrs = parseFloat(time1) + parseFloat(time2);
  //console.log('workhrs: '+ workhrs);
  $('#manskeddtl'+el.data('index')+'workhrs').val(workhrs);
  var d = (workhrs==0) ? '-':workhrs;
  el.parent().siblings('.td-workhrs').text(d); 
  var l = parseFloat(workhrs) - 8;
  $('#manskeddtl'+el.data('index')+'loading').val(l);
  if(l < 0){
    el.parent().siblings('.td-loading').addClass('text-danger');
  } else {
    el.parent().siblings('.td-loading').removeClass('text-danger');
  }
  l = (l==0) ? '-':l;
  el.parent().siblings('.td-loading').text(l);

  updateTotWorkhrs();
  updateLoads();
  updateMancost();
}


var updateBreakhrs = function(el){

  var tr = el.parent().parent();
  var be = tr.children('td').children('.breakend');
  var bs = tr.children('td').children('.breakstart');
  var bh = 0;
  if(bs.val()!='off' && be.val()!='off'){
    bh = calc(bs.val(), be.val());
  }
  $('#manskeddtl'+el.data('index')+'breakhrs').val(bh);
}


var updateEmpcount = function() {
  var ins = 0;
  for(i=0; i<$('.daytype').length; i++){
    if($('.daytype')[i].value == 1)
      ins++;
  }
  $('#empcount').val(ins);
  $('.tb-empcount').text(ins);
}

var updateTotWorkhrs = function() {

  var ins = 0;
  for(i=0; i<$('.workhrs').length; i++)
    ins += (isNaN($('.workhrs')[i].value)) ? 0: parseFloat($('.workhrs')[i].value);
  //console.log('tot workhrs: '+ ins);
  $('#workhrs').val(ins);
  $('.tb-workhrs').text(ins);
}

var updateLoads = function(){
  var ins = 0,
      o = 0,
      u = 0;
  for(i=0; i<$('.loading').length; i++){
    ins = (isNaN($('.loading')[i].value)) ? 0: parseFloat($('.loading')[i].value);
    if(ins < 0)
      u++;
    else if(ins > 0)
      o++;
    else 
      console.log('loading: '+ ins +' zero'); 
  }
  $('#overload').val(o);
  $('.tb-overload').text(o);
  $('#underload').val(u);
  $('.tb-underload').text(u);
}




  $('document').ready(function(){

    updateMancost();

    $('select.timestart').on('change', function(e){
      //console.log();
      var x = ($(this)[0].value=='off') ? 0:1; 
      $('#manskeddtl'+$(this).data('index')+'daytype').val(x); 
      if(x==0){  
        var d = true;
      } else {
        var d = false;
      }

      updateWorkhrs($(this))

      $(this).parent().siblings('td').children('.frm-ctrl').each(function(el){
        
        if(d){
           $(this)[0].value = 'off'
           $(this).parent().addClass('disabled');
        } else {
          $(this).parent().removeClass('disabled');
        }
        $(this)[0].disabled = d;
        $(this)[0].readonly = d;
      });

      
      updateEmpcount();
    });


    

    $('select.breakstart').on('change', function(e){
      updateWorkhrs($(this));
      updateBreakhrs($(this));
      updateEmpcount();
    });

    $('select.breakend').on('change', function(e){
      updateWorkhrs($(this));
      updateBreakhrs($(this));
      updateEmpcount();
    });

    $('select.timeend').on('change', function(e){
      updateWorkhrs($(this));
      updateEmpcount();
    });

    $('#custcount').on('keypress blur change keyup', function(e){
      updateMancost();
    })

    $('#headspend').on('keypress blur change keyup', function(e){
      updateMancost();
    })
  });

</script>
@endsection

