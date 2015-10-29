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
            
            @if(count($manskeds) > 1)
            <a href="/task/mansked/{{date('Y',strtotime('now'))}}/week/{{date('W',strtotime('now'))}}" class="btn btn-default">
              <span class="gly gly-table"></span>
            </a>                 
            @endif
          </div>

          <div class="btn-group" role="group">
            <a href="/task/mansked/add" class="btn btn-default">
              <span class="glyphicon glyphicon-plus"></span>
            </a>
          </div>
      </div><!-- end btn-grp -->
      </div>
    </nav>

    @include('_partials.alerts')

    <div class="panel-group" id="accordion-week-days" role="tablist" aria-multiselectable="true">
      <div class="panel panel-default panel-warning">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title">
            <!--
            <a role="button" data-toggle="collapse" data-parent="#accordion-week-days" href="#collapse-week{{ $new['weekno'] }}" aria-expanded="false" aria-controls="collapse-week{{ $new['weekno'] }}" class="collapsed">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </a>
            <a role="button" data-toggle="collapse" data-parent="#accordion-week-days" href="#collapse-week{{ $new['weekno'] }}" aria-expanded="false" aria-controls="collapse-week{{ $new['weekno'] }}" class="collapsed">
              Week {{ $new['weekno'] }}
            </a-->
            Week {{ $new['weekno'] }}
            <span style="margin-left: 10%;">
              {{ $new['weekdays'][0]->format('D, M d') }} - 
              {{ $new['weekdays'][6]->format('D, M d') }}
            </span>
            @if(count($manskeds) > 1)
            <a href="#myModal" class="pull-right" data-toggle="modal" data-target="#myModal"><span class="glyphicon glyphicon-duplicate"></span></a>
            @endif
            <a href="/task/mansked/add" class="pull-right" style="margin-right:10%;"><span class="glyphicon glyphicon-plus"></span> create</a>
          </h4>
        </div>
        <div id="collapse-week{{ $new['weekno'] }}" class="panel-collapse collapse " role="tabpanel" aria-labelledby="week{{ $new['weekno'] }}">
          <div class="panel-body">
            @for($i=0; $i<6; $i++)
            <button class="btn btn-default" disabled><i class="fa fa-calendar-o"></i> {{ $new['weekdays'][$i]->format('D, M d') }}</button>
            @endfor
          </div>
        </div>
      </div>
      @if($manskeds[0] == null)

      </div>
      @else 

      @foreach($manskeds as $mansked)
      <div class="panel panel-default">
        <div class="panel-heading {{ session('new') ? 'new':'' }}" role="tab" id="week{{ $mansked->weekno }}">
          {{ session()->forget('new') }}
          <h4 class="panel-title">
            
            <a href="/task/mansked/{{ $mansked->year }}/week/{{ $mansked->weekno }}">
              Week {{ $mansked->weekno }}</a>

            <span style="margin-left: 10%;">
              {{ date('D, M j',strtotime($mansked['manskeddays'][0]->date)) }} - 
              {{ date('D, M j',strtotime($mansked['manskeddays'][6]->date)) }}
            </span>

            <a role="button" data-toggle="collapse" data-parent="#accordion-week-days" href="#collapse-week{{ $mansked->weekno }}" aria-expanded="false" aria-controls="collapse-week{{ $mansked->weekno }}" class="collapsed pull-right">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </a>
            <span class="pull-right" style="margin-right:10%;">
              {{ $mansked->refno }}
            </span>
          </h4>
        </div>
        <div id="collapse-week{{ $mansked->weekno }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="week{{ $mansked->weekno }}">
          <div class="panel-body">
            
            @foreach($mansked->manskeddays as $manday)
              <a href="/task/manday/{{$manday->lid()}}" class="btn alert-success"><i class="fa fa-calendar-o"></i> {{ date('D, M j',strtotime($manday->date)) }}</a>
            @endforeach
            
          </div>
        </div>
      </div>
      @endforeach
      </div>
      {!! $manskeds->render() !!}
     
     @endif
      

    
      
  
</div>

<!-- end main -->
</div>
@if(count($manskeds) > 1)
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      {!! Form::open(['url' => 'api/c/mansked', 'accept-charset'=>'utf-8']) !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Copy Manpower Schedule</h4>
      </div>
      <div class="modal-body">
      

      <input type="text" name="nweekno" id="nweekno" value="{{ $new['weekno'] }}">
      <input type="text" name="lweekno" id="lweekno" value="{{ $manskeds[0]->weekno }}">
      <input type="text" name="year" id="year" value="{{ $new['year'] }}">
      <input type="text" name="lmanskedid" id="lmanskedid" value="{{ $new['lmanskedid'] }}">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save changes</button>
      </div>
      {!! Form::close() !!}
    </div>
  </div>
</div>
@endif
@endsection


@section('js-external')
  @parent

  <script>
    $(".panel-heading.new").effect("highlight", {}, 2000);
    $('.alert').not('.alert-important').delay(5000).slideUp(300);
  </script>
@endsection
