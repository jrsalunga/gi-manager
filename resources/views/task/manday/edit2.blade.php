@extends('index')

@section('title', '- Create Daily Man Schedule')

@section('body-class', 'mansked-create')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li><a href="/task/mansked/{{$manday->date->year}}/week/{{$manday->date->weekOfYear}}">Week {{$manday->date->weekOfYear}}</a></li>
    <li><a href="/task/manday/{{$manday->lid()}}">{{ $manday->date->format('D, M j') }}</a></li>
    <li class="active">Edit</li>
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
            <a href="/task/mansked/{{$manday->date->year}}/week/{{$manday->date->weekOfYear}}" class="btn btn-default">
              <span class="gly gly-table"></span>
            </a>
            -->
            <a href="/task/manday/{{$manday->lid()}}" class="btn btn-default">
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


    <form method="post" action="/api/t/manskedday/{{$manday->lid()}}" id="frm-manskedday" name="frm-manskedday" role="form" data-table="manskedday">
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
            <input type="hidden" id="brmancost" value="{{ $manday->manskedhdr->mancost }}">
            {{-- <input type="hidden" id="brmancost" value="{{ session('user.branchmancost') }}"> --}}
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

    <table class="table table-bordered">
      <tbody id="tb-houlryman">
          <tr class="t1">
          @foreach ($hours as $key => $value) 
            <td title="{{ $key }}"> {{ date('g:i A', strtotime($key.'.00')) }}</td>
          @endforeach
          </tr>
          <tr class="t2">
          @foreach ($hours as $key => $value)  
            <td class="text-right">{{ $value }}</td>
          @endforeach
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
                      <?php $p = str_pad($j,2,'0',STR_PAD_LEFT)  ?>
                      @if($dept['employees'][$i]['manskeddtl']['timestart'] == date('H:i', strtotime( $j .':00')))
                        <option selected value="{{ $p }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $p }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif

                      <?php 
                      /*
                      @if($dept['employees'][$i]['manskeddtl']['timestart'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                      */?>
                    @endfor
                  </select>
                </td>
                <td class="text-right text-input {{ $disabled }}">
                  <select name="manskeddtls[{{ $ctr }}][breakstart]" class="frm-ctrl tk-select breakstart" data-index="{{$ctr}}" {{ $disabled }} tabindex="-1"> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      <?php $p = str_pad($j,2,'0',STR_PAD_LEFT)  ?>
                      @if($dept['employees'][$i]['manskeddtl']['breakstart'] == date('H:i', strtotime( $p .':00')))
                        <option selected value="{{ $p }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $p }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif

                      <?php 
                      /*
                      @if($dept['employees'][$i]['manskeddtl']['breakstart'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                      */?>
                    @endfor
                  </select>
                </td>
                <td class="text-right text-input {{ $disabled }}">
                  <select name="manskeddtls[{{ $ctr }}][breakend]" class="frm-ctrl tk-select breakend" data-index="{{$ctr}}" {{ $disabled }} tabindex="-1"> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      <?php $p = str_pad($j,2,'0',STR_PAD_LEFT)  ?>
                      @if($dept['employees'][$i]['manskeddtl']['breakend'] == date('H:i', strtotime( $p .':00')))
                        <option selected value="{{ $p }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $p }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif
                      
                      <?php 
                      /*
                      @if($dept['employees'][$i]['manskeddtl']['breakend'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                      */?>
                    @endfor
                  </select>
                </td>
                <td class="text-right text-input {{ $disabled }}">
                  <select name="manskeddtls[{{ $ctr }}][timeend]" class="frm-ctrl tk-select timeend" data-index="{{$ctr}}" {{ $disabled }} tabindex="-1"> 
                    <option value="off">-</option>
                    @for ($j = 1; $j <= 24; $j++)
                      @if($dept['employees'][$i]['manskeddtl']['timeend'] == date('H:i', strtotime( $j .':00')))
                        <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @else
                        <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                      @endif
                      
                      <?php 
                      /*
                      @if($dept['employees'][$i]['manskeddtl']['timeend'] == date('G:i', strtotime( $j .':30')))
                        <option selected value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @else
                        <option value="{{ $j }}:30">{{ date('g:i A', strtotime( $j .':30')) }}</option>
                      @endif
                      */?>
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

var arr = [];

var updateMancost = function(){
  //console.log('value:  '+ typeof $('#empcount')[0].value);
  var e = (isNaN($('#empcount')[0].value)) ? 0: parseFloat($('#empcount')[0].value);
  var m = (isNaN($('#brmancost')[0].value)) ? 0: parseFloat($('#brmancost')[0].value);
  var c = (isNaN($('#custcount')[0].value)) ? 0: parseFloat($('#custcount')[0].value);
  var h = (isNaN($('#headspend')[0].value)) ? 0: parseFloat($('#headspend')[0].value);
  var mancost = ((e*m)/(c*h)*100);
  console.log('empcount:' +e);
  console.log('mancost:' +m);
  console.log((e*m));
  console.log((c*h));
  mancost = (isNaN(mancost) || !isFinite(mancost)) ? 0 : mancost;
  console.log('mancost: '+ mancost);
  $('.tb-mancost').text(mancost.toFixed(2)+' %');
}

var getHour = function(s, e){
  s = parseInt(s, 10);
  var arr = [];
  for(i = s; i < e; i++){
    arr.push(i);
  }
  return arr;
}

var getHour2 = function(s, e){
  s = parseInt(s, 10);
  e = parseInt(e, 10);
  e = e < s ? e+24:e; 
  var arr = [];
  for(i = s; i < e; i++){
    arr.push(i);
  }
  return arr;
}

var calc = function (fr, to) {
  var timestart = moment(today+' '+fr);
  var breakstart = moment(today+' '+to);
  return breakstart.diff(timestart, 'hours', true);
}

var calc2 = function (fr, to) {
  var ts = moment(today+' '+fr);
  var te = moment(today+' '+to);
  var diff = te.diff(ts, 'hours', true);
  return parseInt(diff,0)>0?diff:parseInt(diff,0)+24;
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
      u += ins;//u++; 
    else if(ins > 0)
      o += ins;//o++;
    else 
      continue; //console.log('loading: '+ ins +' zero'); 
  }
  $('#overload').val(o);
  $('.tb-overload').text(o);
  $('#underload').val(u);
  $('.tb-underload').text(u);
}

var updateManPerHour = function(el){
  var arr = [];
  var tb = el.parent().parent().parent();
  tb.children('tr').each(function(idx) {
    if(idx!=0){
      var tr = $(this);
      var ts = tr.children('td').children('.timestart').val();
      var bs = tr.children('td').children('.breakstart').val();
      var be = tr.children('td').children('.breakend').val();
      var te = tr.children('td').children('.timeend').val();

      if(ts=='off'){
        be='0.00';
        te='0.00';
      }

      if(ts!='off' && bs!='0.00'){
        var i = getHour(ts.split(':')[0], bs.split(':')[0]);
        i.forEach(function(el, idx, array) {
            if(arr.hasOwnProperty(el)){
              arr[el] += 1;
            } else {
              arr[el] = 1;
            }
        });
      }

      if(be!='0.00' && te!='0.00'){
        var j = getHour2(be.split(':')[0], te.split(':')[0]);
        j.forEach(function(el, idx, array) {
            if(arr.hasOwnProperty(el)){
              arr[el] += 1;
            } else {
              arr[el] = 1;
            }
        });
      }

      if(ts!='0.00' && te!='0.00' && bs=='off' && be=='off'){
        var j = getHour2(ts.split(':')[0], te.split(':')[0]);
        j.forEach(function(el, idx, array) {
            if(arr.hasOwnProperty(el)){
              arr[el] += 1;
            } else {
              arr[el] = 1;
            }
        });
      }

      if(ts!='0.00' && te!='0.00' && bs!='0.00' && be!='0.00'){
        var j = getHour(bs.split(':')[0], be.split(':')[0]);
        j.forEach(function(el, idx, array) {
            if(arr.hasOwnProperty(el)){
              arr[el] += 0;
            } else {
              arr[el] = 0;
            }
        });
      }
        
    }
  })

  console.log(arr);
  $('.t1').html('');
  $('.t2').html('');
  arr.forEach(function(el, idx, array) {
    if(parseInt(idx,10)>=24)
      idx = parseInt(idx,10)-24;
    $('.t1').append('<td>'+ moment('{{ $manday->date->format("Y-m-d") }} '+idx+':00').format("h:00 A") + '</td>');
    $('.t2').append('<td>'+ el + '</td>');
  });
}


var updateWorkhrs = function(el){

  var tr = el.parent().parent();
  var ts = tr.children('td').children('.timestart');
  var bs = tr.children('td').children('.breakstart');
  var be = tr.children('td').children('.breakend');
  var te = tr.children('td').children('.timeend');
  var workhrs = 0;
  var time1 = 0;
  var time2 = 0;
  var time3 = 0;

  
  if(ts.val()!='off' && bs.val()!='off'){
    //console.log('time1 on');
    time1 = calc(ts.val(), bs.val());
    updateManPerHour(el);
  }
  
  if(be.val()!='off' && te.val()!='off'){
    //console.log('time2 on');

    
    time2 = calc2(be.val(), te.val());
    updateManPerHour(el);
  }
  if(ts.val()!='off' && te.val()!='off' && bs.val()=='off' && bs.val()=='off'){
    //console.log('time3 on');
    time3 = calc2(ts.val(), te.val());
    //workhrs = calc(ts.val(), te.val());
    console.log(workhrs);
    updateManPerHour(el);
  }

  if(time3==0){
    //console.log('pasok')
    workhrs = parseFloat(time1) + parseFloat(time2);
  } else {
    workhrs = time3;
  }
    
  
  if(ts.val()=='off'){
    workhrs = 0;
    updateManPerHour(el);
  }
    
  console.log('workhrs: '+ workhrs);
  $('#manskeddtl'+el.data('index')+'workhrs').val(workhrs);
  var d = (workhrs==0) ? '-':workhrs;
  el.parent().siblings('.td-workhrs').text(d); 
  var l = parseFloat(workhrs) - 8;
  if(ts.val()=='off'){
    updateManPerHour(el);
    l = 0;
  }
    
  $('#manskeddtl'+el.data('index')+'loading').val(l);
  if(l < 0){
    el.parent().siblings('.td-loading').removeClass('text-info');
    el.parent().siblings('.td-loading').addClass('text-danger');
  } else if(l > 0){
    el.parent().siblings('.td-loading').removeClass('text-danger');
    el.parent().siblings('.td-loading').addClass('text-info');
  } else {
    el.parent().siblings('.td-loading').removeClass('text-danger');
    el.parent().siblings('.td-loading').removeClass('text-info');
  }
  l = (l==0) ? '-':l;
  el.parent().siblings('.td-loading').text(l);

  updateTotWorkhrs();
  updateLoads();
  updateMancost();
}




  $('document').ready(function(){

    updateMancost();

    $('select.timestart').on('change', function(e){
      var x = ($(this)[0].value=='off') ? 0:1; 
      $('#manskeddtl'+$(this).data('index')+'daytype').val(x); 
      if(x==0){  
        var d = true;
        $('#manskeddtl'+$(this).data('index')+'breakhrs').val(x); 
      } else {
        var d = false;
      }

      var that = $(this);
      
      
      updateWorkhrs($(this))

      $(this).parent().siblings('td').children('.frm-ctrl').each(function(el){
        
        if (d) {
          $(this)[0].value = 'off';
          $(this).parent().addClass('disabled');
        } else {
          $(this).parent().removeClass('disabled');

          if (that.val()=='09:00') {
            if ($(this).parent().index()==3 && $(this)[0].value=='off') 
              $(this)[0].value = '13:00';
            if ($(this).parent().index()==4 && $(this)[0].value=='off') 
              $(this)[0].value = '15:00';
            if ($(this).parent().index()==5 && $(this)[0].value=='off') 
              $(this)[0].value = '21:00';
            $(this).trigger('change');
          }

          if (that.val()=='10:00') {
            if ($(this).parent().index()==3 && $(this)[0].value=='off') 
              $(this)[0].value = '14:00';
            if ($(this).parent().index()==4 && $(this)[0].value=='off') 
              $(this)[0].value = '16:00';
            if ($(this).parent().index()==5 && $(this)[0].value=='off') 
              $(this)[0].value = '22:00';
            $(this).trigger('change');
          }

          if (that.val()=='11:00') {
            if ($(this).parent().index()==3 && $(this)[0].value=='off') 
              $(this)[0].value = '15:00';
            if ($(this).parent().index()==4 && $(this)[0].value=='off') 
              $(this)[0].value = '17:00';
            if ($(this).parent().index()==5 && $(this)[0].value=='off') 
              $(this)[0].value = '23:00';
            $(this).trigger('change');
          }

          if (that.val()=='12:00') {
            if ($(this).parent().index()==3 && $(this)[0].value=='off') 
              $(this)[0].value = '16:00';
            if ($(this).parent().index()==4 && $(this)[0].value=='off') 
              $(this)[0].value = '18:00';
            if ($(this).parent().index()==5 && $(this)[0].value=='off') 
              $(this)[0].value = '24:00';
            $(this).trigger('change');
          }
          
          console.log(parseInt(that.val()));
        }
        $(this)[0].disabled = d;
        $(this)[0].readonly = d;
      });

      
      updateEmpcount();
      updateMancost();
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


    $('.label').on('dblclick', function(){
      var tr = $(this).parent().parent();
      var ts = tr.children('td').children('.timestart');
      var bs = tr.children('td').children('.breakstart');
      var be = tr.children('td').children('.breakend');
      var te = tr.children('td').children('.timeend');

      $('#manskeddtl'+tr.index()+'daytype').val(1); 
      ts[0].value = '10:00';
      bs[0].value = '14:00';
      bs[0].disabled = false;
      bs[0].readonly = false;
      bs.parent().removeClass('disabled');
      be[0].value = '16:00';
      be[0].disabled = false;
      be[0].readonly = false;
      be.parent().removeClass('disabled');
      te[0].value = '22:00';
      te[0].disabled = false;
      te[0].readonly = false;
      te.parent().removeClass('disabled');

      updateWorkhrs(ts);
      updateEmpcount();
      updateMancost();
    });
  });

</script>
@endsection

