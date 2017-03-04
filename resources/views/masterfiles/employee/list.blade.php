@extends('index')

@section('title', '- Employees ('.strtoupper(brcode()).')')

@section('body-class', 'branch-employees')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/{{brcode()}}/dashboard">{{ $branch }}</a></li>
    <li><a href="/{{brcode()}}/employee">Employee</a></li>
    <li class="active">List</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/{{brcode()}}/employee" class="btn btn-default">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs">Back</span>
            </a>
          </div>
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="fa fa-users"></span>
              <span class="hidden-xs">List</span>
            </button>
          
          @if(request()->has('search'))
            <button type="button" class="btn btn-default active">
              <span class="fa fa-filter"></span>
              <span class="hidden-xs">{{ strtoupper(explode(':',request()->input('search'))[1]) }}</span>
            </button>
            <a type="button" class="btn btn-default" href="/{{brcode()}}/employee/list" title="Remove Filter"><span class="fa fa-close"></span></a>
            <!--
            <span class="label label-primary">{{ explode(':',request()->input('search'))[1] }} 
              <a href="/{{brcode()}}/employee/list" style="color: #ccc;" title="Remove filter">x</a>
            </span>
            -->
          @endif
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
          <img src="http://cashier.giligans.net/images/{{$employee->photo?'employees/'.$employee->code.'.jpg':'login-avatar.png'}}" style="max-width: 100px;" class="img-responsive">
        </span>
        <span class="pull-left">
          <h4>{{ $employee->lastname}}, {{ $employee->firstname}} {{ $employee->middlename }} <small>{{ $employee->code }}</small></h4>
          <span>
            @if(is_null($employee->position))
              Position Not Set
            @else 
              <a href="/{{brcode()}}/employee/list?search=position.code:{{strtolower($employee->position->code)}}">{{ $employee->position->descriptor }}</a>
            @endif
          </span>
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








