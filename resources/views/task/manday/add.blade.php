@extends('branch.master')

@section('title', '- Create Daily Man Schedule')

@section('body-class', 'mansked-create')

@section('container-body')
  <div class="container-fluid">
<!-- sidebar -->
<div class="col-sm-3 col-md-2 sidebar">
  <ul class="nav nav-sidebar">
    <li>
      <a href="/branch/manage/user">Manage User</a>
    </li>
    <li class="active">
      <a href="/branch/mansked">Man Schedule</a>
    </li>
  </ul>    
</div>

<!-- main -->
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
  <div class="row">
    
    <ol class="breadcrumb">
      <li><a href="/">Home</a></li>
      <li><a href="/branch">Branch</a></li>
      <li><a href="/branch/manday">Daily Man Schedule</a></li>
      <li class="active">Add</li>
    </ol>

    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/branch/manday" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-file"></span>
            </button>   
          </div>
      </div><!-- end btn-grp -->
      </div>
    </nav>


    

    {!! Form::open(['url' => 'api/t/manskedday', 'accept-charset'=>'utf-8', 'id'=>'frm-manskedday', 'name'=>'frm-manskedday', 'class'=>'table-manskedday']) !!}
    <div class="panel panel-default">
        <div class="panel-heading">Forecasted  Information</div>
        <div class="panel-body row">
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="date" class="control-label">Date</label>
            <input type="text" class="form-control" id="date" name="date" placeholder="YYYY-MM-DD" value="{{ $date }}" >
          </div>
        </div>   
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="custcount" class="control-label">Forecasted Customers</label>
            <input type="text" class="form-control" id="custcount" name="custcount" placeholder="No. of Customers" >
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="empcount" class="control-label">Total Crew on Duty</label>
            <input type="text" class="form-control text-right" id="empcount" name="empcount" placeholder="0" readonly>
          </div>
        </div>
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="manpower" class="control-label">Manpower - Short/(Over)</label>
            <input type="text" class="form-control text-right" id="manpower" placeholder="0" readonly>
          </div>
        </div>  

        <div class="col-md-3 col-md-offset-3 col-sm-6">
          <div class="form-group">
            <label for="headspend" class="control-label">Forecasted Ave Spending</label>
            <div class="input-group">
              <span class="input-group-addon">&#8369;</span>
              <input type="text" class="form-control text-right" id="headspend" name="headspend" placeholder="0" >
            </div>
            
          </div>
        </div>  
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="mancost" class="control-label">Manpower Cost %</label>
            <div class="input-group">
              <input type="text" class="form-control text-right" id="mancost" placeholder="0" readonly>
              <span class="input-group-addon">%</span>
            </div>
          </div>
        </div>   
        <div class="col-md-3 col-sm-6">
          <div class="form-group">
            <label for="comment" class="control-label">Comment</label>
            <input type="text" class="form-control" id="comment" placeholder="Ok, Over, High, Too High" readonly>
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
                <tr>
                  <td>{{ $i+1 }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }}</td>
                  <td>{{ $dept['employees'][$i]->position->code }}</td>
                  <td>
                    
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.daytype" name="manskeddtl[{{ $ctr }}][daytype]" class="daytype" value="0">
                      <input type="hidden" id="manskeddtl.{{ $ctr }}.employeeid" name="manskeddtl[{{ $ctr }}][employeeid]" value="{{ $dept['employees'][$i]->id }}">
                      <div class="input-group pull-right">

                      <select name="manskeddtl[{{ $ctr }}][timestart]" class="form-control "> 
                        <option value="off">DAY OFF</option>
                        @for ($j = 1; $j <= 24; $j++)
                          <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                        @endfor
                      </select>

                      <select name="manskeddtl[{{ $ctr }}][breakstart]" class="form-control"> 
                        <option value="off">BREAK</option>
                        @for ($j = 1; $j <= 24; $j++)
                          <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                        @endfor
                      </select>

                      <select name="manskeddtl[{{ $ctr }}][breakend]" class="form-control"> 
                        <option value="off">PM IN</option>
                        @for ($j = 1; $j <= 24; $j++)
                          <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
                        @endfor
                      </select>

                      <select name="manskeddtl[{{ $ctr }}][timeend]" class="form-control"> 
                        <option value="off">TIME OUT</option>
                        @for ($j = 1; $j <= 24; $j++)
                          <option value="{{ $j }}:00">{{ date('g:i A', strtotime( $j .':00')) }}</option>
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

    <div class="row">
      <div class="col-md-6">
        <a href="{{ isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'/branch/manday' }}" class="btn btn-default">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>

    {!! Form::close() !!}
    
      
  </div>
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
  });
</script>
@endsection

