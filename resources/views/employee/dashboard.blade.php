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
    <div class="col-sm-6 col-md-3">
      
      <div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title">Branch Manpower</h3>
        </div>
        <div class="panel-body">
          @if(!is_null($data['positions']['datas']))
            <table class="table table-condensed">
            <thead>
              <tr><th>Position</th><th class="text-right">#</th></tr>
            </thead>
            <tbody>
            @foreach($data['positions']['datas'] as $key => $e)
              <tr>
                <td>
                  @if($key==='temp')
                    {{ $e['descriptor'] }}
                  @else
                    <a href="/{{brcode()}}/employee/list?search=position.code:{{$key}}">
                      {{ $e['descriptor'] }}
                    </a>
                  @endif
                </td>
                <td class="text-right">
                  @if($key==='temp')
                    {{ $e['count'] }}
                  @else
                    <a href="/{{brcode()}}/employee/list?search=position.code:{{$key}}">
                      {{ $e['count'] }}
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
            <thead><tr><td></td><td class="text-right"><a href="/{{brcode()}}/employee/list">{{ $data['positions']['total'] }}</a></td></tr></thead>
            </table>
          @endif
        </div><!-- end: .panel-body-->
      </div><!-- end: .panel.panel-default-->

      
    </div><!-- end: .col-md-3-->
  </div><!-- end: .row-->

 
  
</div>
<!-- end: container-fluid -->
@endsection








