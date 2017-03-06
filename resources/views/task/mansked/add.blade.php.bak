@extends('index')

@section('title', '- Create Man Schedule')

@section('body-class', 'mansked-create')

@section('container-body')
<div class="container-fluid">
  
  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li class="active">Add</li>
  </ol>

  <div class="">
    
    

    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            
            <a href="/task/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-file"></span>
            </button>  
          </div>
      </div><!-- end btn-grp -->
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


    {!! Form::open(['url' => 'api/t/mansked', 'accept-charset'=>'utf-8', 'id'=>'frm-manskedhdr', 'name'=>'frm-manskedhdr', 'class'=>'table-manskedhdr']) !!}
    <div class="panel panel-success">
        <div class="panel-heading">Manpower Schedule Header</div>
        <div class="panel-body row">
        <div class="col-md-3 col-sm-3">
          <div class="form-group">
            <label for="refno" class="control-label">Ref No</label>
            <input type="text" class="form-control" id="refno" placeholder="Ref No" readonly>
          </div>
        </div>   
        
        <div class="col-md-2 col-md-offset-2 col-sm-3">
          <div class="form-group">
            <label for="date" class="control-label">Date</label>
            <input type="text" class="form-control" name="date" id="date" placeholder="YYYY-MM-DD" value="{{ date('Y-m-d', strtotime('now')) }}" maxlength="10" >
            <input type="hidden" id="year" name="year" value="{{ $data['year'] }}" >
          </div>
        </div>
        <div class="col-md-2 col-sm-3">
          <div class="form-group">
            <label for="weekno" class="control-label">Week No</label>
            <input type="text" class="form-control" name="weekno" id="weekno" value="{{ $data['weekno'] }}" placeholder="Week No" readonly>
          </div>
        </div>
        <div class="col-md-3 col-sm-3">
          <div class="form-group">
            <label for="mancost" class="control-label">Man Cost</label>
            <div class="input-group">
              <span class="input-group-addon">&#8369;</span>
              <input type="text" class="form-control text-right" name="mancost" id="mancost" value="{{ $data['mancost'] }}" placeholder="0" maxlength="10" >
            </div>
          </div>
        </div>
          

        <div class="col-md-5 col-sm-6">
          <div class="form-group">
            <label for="branch" class="control-label">Branch</label>
            <div class="input-group">
              <span class="input-group-addon"><span class="gly gly-shop"></span></span>
              <input type="text" class="form-control" id="branch"  value="{{ $data['branch'] }}" readonly>
            </div>
            <input type="hidden" id="branchid" name="branchid"  value="{{ $data['branchid'] }}">
          </div>
        </div>  
        
        <div class="col-md-5 col-md-offset-2 col-sm-6">
          <div class="form-group">
            <label for="manager" class="control-label">Manager</label>
            <div class="input-group">
              <span class="input-group-addon"><span class="gly gly-user"></span></span>
              <input type="text" class="form-control" id="manager" value="{{ $data['manager'] }}"  readonly>
            </div>
            <input type="hidden" id="managerid" name="managerid" value="{{ $data['managerid'] }}">
            
          </div>
        </div>

        <div class="col-md-5 col-sm-7">
          <div class="form-group">
            <label for="manager" class="control-label">Notes</label>
            
            <textarea id="notes" name="notes" class="form-control"></textarea>
            
          </div>
        </div>      
        
        
      </div>
    </div>
    <div class="row button-container">
      <div class="col-md-6">
        <a href="{{ isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'/branch/mansched' }}" class="btn btn-default">Cancel</a>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
    {!! Form::close() !!}
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



     $("#date").datepicker({ minDate: 0, dateFormat: 'yy-mm-dd',  beforeShow: function() {
        setTimeout(function(){
            $('.ui-datepicker').css('z-index', 3);
        }, 0);
    }});
  });
</script>
@endsection


