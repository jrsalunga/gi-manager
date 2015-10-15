@extends('index')

@section('title', '- Man Schedule')

@section('body-class', 'branch-mansked')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">Manpower Schedule</li>
  </ol>

  <div class="">
    

    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
            </button>
            <a href="/task/mansked/week/{{ str_pad(date('W',strtotime('now')),2,'0', STR_PAD_LEFT) }}" class="btn btn-default">
              <span class="glyphicon glyphicon-calendar"></span>
            </a>   
          </div>

          <div class="btn-group" role="group">
            <a href="/task/mansked/add" class="btn btn-default">
              <span class="glyphicon glyphicon-plus"></span>
            </a>
          </div>
      </div><!-- end btn-grp -->
      </div>
    </nav>

    <table class="table table-condensed tb-brand">
    <thead>
      <tr>
        <th>Week</th>
        <th>Descriptor</th>  
      <tr>
    </thead>
    <tbody class=''>
      @foreach($weeks as $week)
      <tr>

        <td><a href="/task/mansked/week/{{ str_pad($week['week'] , 2, '0', STR_PAD_LEFT) }}">Week {{ $week['week'] }}</a></td>
        <td></td>
      </tr>
      @endforeach
      
      
    </tbody>
    </table>
      {!! $weeks->render() !!}

    
      
  
</div>

<!-- end main -->
</div>
@endsection


