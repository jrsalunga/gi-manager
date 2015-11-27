@extends('index')

@section('title', '- DTR Month List')

@section('body-class', 'month-dtr')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">Daily Time Record</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
            </button>
            
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <a href="/dtr/generate" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-history"></span>
            </a>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    <h3>{{$year}}</h3>
    @foreach($months as $key => $month)
      <a href="/dtr/{{$year}}/{{pad($key)}}" class="btn alert-success" style="margin-bottom: 5px;">
        <i class="fa fa-calendar-o"></i> {{ $month['month'] }}  
        <span class="badge" style="background-color:#fff; color:green;">{{ number_format($month['total'],0) }}</span>
      </a>
      
    @endforeach
   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent

@endsection
