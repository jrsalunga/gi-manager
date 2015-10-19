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
    <li><a href="/task/manday/{{strtolower($manday->id)}}">{{ date('D, M j',strtotime($manday->date)) }}</a></li>
    <li class="active">Edit</li>
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
            <a href="/task/manday/{{$manday->id}}" class="btn btn-default">
              <span class="fa fa-calendar-o"></span>
            </a>   
          </div><!-- end btn-grp -->
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-edit"></span>
            </button>  
          </div><!-- end btn-grp -->
        </div>
      </div>
    </nav>


    <form method="post" action="/api/t/manskedday/{{strtolower($manday->id)}}" id="frm-manskedday" name="frm-manskedday" role="form" data-table="manskedday">
    <input type="hidden" name="_method" value="PUT">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="panel panel-default">
        <div class="panel-heading">Forecasted  Information</div>
        <div class="panel-body row">
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="date" class="control-label">Date</label>
            <div class="input-group">
              <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
              <input type="text" class="form-control" id="date" placeholder="YYYY-MM-DD" value="{{ $manday->date }}" readonly tabindex="-1">
              <input type="hidden" id="id" name="id" value="{{ $manday->id }}" readonly>
            </div>
          </div>
        </div>   
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="custcount" class="control-label">Forecasted Customers</label>
            <input type="text" class="form-control text-right" id="custcount" name="custcount" value="{{ $manday->custcount }}">
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="empcount" class="control-label">Total Crew on Duty</label>
            <input type="text" class="form-control text-right" name="empcount" id="empcount" value="{{ $manday->empcount }}" tabindex="-1" readonly>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="manpower" class="control-label">Manpower - Short/(Over)</label>
            <input type="text" class="form-control text-right" id="manpower" readonly tabindex="-1">
          </div>
        </div>  

        <div class="col-md-3 col-md-offset-3 col-sm-6">
          <div class="form-group">
            <label for="headspend" class="control-label">Forecasted Ave Spending</label>
            <div class="input-group">
              <span class="input-group-addon">&#8369;</span>
              <input type="text" class="form-control text-right" id="headspend" name="headspend" value="{{ $manday->headspend }}">
            </div>
            
          </div>
        </div>  
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="mancost" class="control-label">Manpower Cost %</label>
            <div class="input-group">
              <input type="text" class="form-control text-right" id="mancost" readonly tabindex="-1">
              <span class="input-group-addon">%</span>
            </div>
          </div>
        </div>   
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="comment" class="control-label">Comment</label>
            <input type="text" class="form-control" id="comment" readonly tabindex="-1">
          </div>
        </div>  
        
      </div>
    </div>

    <div class="row">
      <?php
        $ctr=0;
      ?>
      @foreach($depts as $dept)
        <div class="col-md-12">
          <div class="panel panel-default">
            <div class="panel-heading">{{ $dept['name'] }} Schedule</div>
            <div class="panel-body row">
              <table class="table tb-manday">
              @for ($i = 0; $i < count($dept['employees']); $i++)
                <?php
                  $disabled = $dept['employees'][$i]['manskeddtl']['daytype'] == 0 ? 'disabled':'';
                ?>

                <tr>
                  <td>{{ $i+1 }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }}</td>
                  <td>{{ $dept['employees'][$i]->position->code }}</td>
                  <td>
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.id" name="manskeddtls[{{ $ctr }}][id]" value="{{ $dept['employees'][$i]['manskeddtl']['id'] }}">
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.daytype" name="manskeddtls[{{ $ctr }}][daytype]" class="daytype" value="{{ $dept['employees'][$i]['manskeddtl']['daytype'] }}">
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.employeeid" name="manskeddtls[{{ $ctr }}][employeeid]" value="{{ $dept['employees'][$i]->id }}">
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.workhrs" name="manskeddtls[{{ $ctr }}][workhrs]" value="{{ $dept['employees'][$i]['manskeddtl']['workhrs'] }}">
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.breakhrs" name="manskeddtls[{{ $ctr }}][breakhrs]" value="{{ $dept['employees'][$i]['manskeddtl']['breakhrs'] }}">
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.loading" name="manskeddtls[{{ $ctr }}][loading]" value="{{ $dept['employees'][$i]['loading']['workhrs'] }}">

                      <div class="input-group pull-right {{$dept['name']}}.no{{$i}}">
                      <select name="manskeddtls[{{ $ctr }}][timestart]" class="form-control tk-select timestart"> 
                        <option value="off">DAY OFF</option>
                        @for ($j = 1; $j <= 24; $j++)
                          @if($dept['employees'][$i]['manskeddtl']['timestart'] == date('G:i', strtotime( $j .':00')))
                            <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @else
                            <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @endif
                        @endfor
                      </select>

                      <select name="manskeddtls[{{ $ctr }}][breakstart]" class="form-control tk-select" {{ $disabled }}> 
                        <option value="off">BREAK</option>
                        @for ($j = 1; $j <= 24; $j++)
                          @if($dept['employees'][$i]['manskeddtl']['breakstart'] == date('G:i', strtotime( $j .':00')))
                            <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @else
                            <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @endif
                        @endfor
                      </select>

                      <select name="manskeddtls[{{ $ctr }}][breakend]" class="form-control tk-select" {{ $disabled }}> 
                        <option value="off">PM IN</option>
                        @for ($j = 1; $j <= 24; $j++)
                          @if($dept['employees'][$i]['manskeddtl']['breakend'] == date('G:i', strtotime( $j .':00')))
                            <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @else
                            <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @endif
                        @endfor
                      </select>

                      <select name="manskeddtls[{{ $ctr }}][timeend]" class="form-control tk-select" {{ $disabled }}> 
                        <option value="off">TIME OUT</option>
                        @for ($j = 1; $j <= 24; $j++)
                          @if($dept['employees'][$i]['manskeddtl']['timeend'] == date('G:i', strtotime( $j .':00')))
                            <option selected value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @else
                            <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                          @endif
                        @endfor
                      </select>
                    </div>
                  </td>
                </tr>
                <?php
                  $ctr++;
                ?>
              @endfor
              </table>
            </div>  
          </div>
        </div>
      @endforeach
      
      
    </div>

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
      console.log(e);

      var x = ($(this)[0].value=='off') ? 0:1; 
      $(this).parent().parent().children('.daytype').val(x);  // set daytype 0 or 1
      console.log('last value: '+ x);
      if(x==0){
        //$(this).removeClass('alert-success');
        //$(this).addClass('alert-warning');
        //$(this).siblings()[0].prop("checked", true);
        //console.log($(this).siblings()[0].disabled);
        $(this).siblings().each(function(el){
          console.log($(this).disabled);
          $(this)[0].disabled = true;
        });
      } else {
        //$(this).removeClass('alert-warning');
        //$(this).addClass('alert-success');
        $(this).siblings().each(function(el){
          $(this)[0].disabled = false;
        });
      }
        
      
        

      var ins = 0;
      for(i=0; i<$('.daytype').length; i++){
        //$('.daytype').css('border', '1px solid red');
        if($('.daytype')[i].value == 1)
          ins++;
      }
      console.log(ins);
      $('#empcount')[0].value = ins;
     

    });



     //$("#date").datepicker({ minDate: 1, dateFormat: 'yy-mm-dd',});
  });
</script>
@endsection

