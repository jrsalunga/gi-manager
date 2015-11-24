@extends('master')

@section('title', ' - Dashboard')

@section('navbar-2')
<ul class="nav navbar-nav navbar-right"> 
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-cog"></span>
      <b class="caret"></b>
    </a>
    <ul class="dropdown-menu">
      <li><a href="/logout">Log Out</a></li>     
    </ul>
  </li>
</ul>
<p class="navbar-text navbar-right">{{ $name }}</p>
@endsection


@section('container-body')
<div class="container-fluid">
	
  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">Dashboard</li>
  </ol>
	
  <div style="margin-top:100px;" class="hidden-xs"></div>
  <div class="row row-centered">
    <div class="col-sm-6 col-md-5 col-centered">
	    <div id="panel-tasks" class="panel panel-success">
			  <div class="panel-heading">
			    <h3 class="panel-title"><span class="gly gly-notes-2"></span> Tasks</h3>
			  </div>
			  <div class="panel-body">
			    <div class="list-group">
					  <a href="/task/mansked" class="list-group-item">Manpower Scheduling</a>
					  <a href="/dtr/generate" class="list-group-item">Daily Time Record Generation</a>
					</div>
			  </div>
			</div>
		</div>
		<div class="col-sm-6 col-md-5 col-centered">
	    <div id="panel-reports" class="panel panel-success">
			  <div class="panel-heading">
			    <h3 class="panel-title"><span class="gly gly-charts"></span> Reports</h3>
			  </div>
			  <div class="panel-body">
			    <div class="list-group">
					  <a href="/const" class="list-group-item">Projected vs Actual Man-Hours</a>
					  <a href="/dtr" class="list-group-item">DTR Summary</a>
					  <a href="/const" class="list-group-item">Absences</a>
					  <a href="/const" class="list-group-item">Tardiness</a>
					  <a href="/const" class="list-group-item">Overloads</a>
					  <a href="/const" class="list-group-item">Underloads</a>
					</div>
			  </div>
			</div>
		</div>
	</div>




</div>
@endsection














@section('js-external')
  
  @if(app()->environment() == 'local')
    @include('_partials.js-vendors')
  @else 
    @include('_partials.js-vendors-common-min')
  @endif

  
@endsection