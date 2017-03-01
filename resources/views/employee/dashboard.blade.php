@extends('index')

@section('title', '- Employee Dashboard('.strtoupper(brcode()).')')

@section('body-class', 'employee-dash')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/{{brcode()}}/dashboard">{{ $branch }}</a></li>
    <li class="active">Employee</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <!--
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="fa fa-dashboard"></span>
              <span class="hidden-xs">Dashboard</span>
            </button>
          </div>
          -->
          <div class="btn-group" role="group">
            <a href="/{{brcode()}}/dashboard" class="btn btn-default">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs">Back</span>
            </a>
          </div>
          <div class="btn-group" role="group">
            <a href="/{{brcode()}}/employee/list" class="btn btn-default">
              <span class="fa fa-users"></span>
              <span class="hidden-xs">List</span>
            </a>
          </div>
      </div><!-- end btn-grp -->
      </div>
    </nav>
  </div>

 
  
</div>
<!-- end: container-fluid -->
@endsection








