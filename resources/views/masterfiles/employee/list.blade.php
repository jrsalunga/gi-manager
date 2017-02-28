@extends('index')

@section('title', '- Employees')

@section('body-class', 'branch-employees')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/{{brcode()}}/dashboard">{{ $branch }}</a></li>
    <li class="active">Employees</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
              <span class="hidden-xs">List</span>
            </button>
             
          </div>
          <!--
          <div class="btn-group" role="group">
            <a href="/task/mansked/add" class="btn btn-default">
              <span class="glyphicon glyphicon-plus"></span>
            </a>
          </div>
        -->
      </div><!-- end btn-grp -->
      </div>
    </nav>
  </div>

  @if(!is_null($employees))
    
    @foreach($employees as $key => $employee)
    <div class="row">
      <div class="col-sm-8">
        <span class="pull-left" style="margin-right: 5px;">
          <img src="http://cashier.giligans.net/images/{{$employee->photo?'employees/'.$employee->code.'.jpg':'login-avata.png'}}" style="max-width: 100px;" class="img-responsive">
        </span>
        <span class="pull-left">
          <h4>{{ $employee->lastname}}, {{ $employee->firstname}} {{ $employee->middlename }} <small>{{ $employee->code }}</small></h4>
          <p>{{ $employee->position->descriptor }}</p>
        </span>
      </div>
      <div class="col-sm-4">
        
      </div>
    </div>
    @endforeach
    
    {!! $employees->render() !!}
  @endif
  
</div>
<!-- end: container-fluid -->
@endsection








