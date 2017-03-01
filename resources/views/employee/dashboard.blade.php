@extends('index')

@section('title', '- Employee Dashboard ('.strtoupper(brcode()).')')

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

  <div class="row">
    <div class="col-md-3">
      @if(!is_null($data['positions']['datas']))
        <table class="table table-condensed">
        <thead>
          <tr><th>Position</th><th>Count</th></tr>
        </thead>
        <tbody>
        @foreach($data['positions']['datas'] as $key => $e)
          <tr>
            <td>{{ $e['descriptor'] }}</td>
            <td>
              <a href="/{{brcode()}}/employee/list?search=position.code:{{$key}}">
                {{ $e['count'] }}
              </a>
            </td>
          </tr>
        @endforeach
        </tbody>
        <thead><tr><td></td><td><a href="/{{brcode()}}/employee">{{ $data['positions']['total'] }}</a></td></tr></thead>
        </table>
      @endif
    </div>
  </div>

 
  
</div>
<!-- end: container-fluid -->
@endsection








