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
              <span class="gly gly-table"></span>
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
            <span style="margin-left: 100px;">
              {{ $new['weekdays'][0]->format('D, M d') }} - 
              {{ $new['weekdays'][6]->format('D, M d') }}
            </span>
            <a href="" class="pull-right"><span class="glyphicon glyphicon-duplicate"></span></a>
            <a href="#" class="pull-right" style="margin-right:100px;"><span class="glyphicon glyphicon-plus"></span> create</a>
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
      @if(count($manskeds) > 1)
      @foreach($manskeds as $mansked)
      <div class="panel panel-default">
        <div class="panel-heading {{ session('new') ? 'new':'' }}" role="tab" id="week{{ $mansked->weekno }}">
          {{ session()->forget('new') }}
          <h4 class="panel-title">
            
            <a href="/task/mansked/week/{{ $mansked->weekno }}">
              Week {{ $mansked->weekno }}
            </a>

            <span style="margin-left: 100px;">
              {{ date('D, M j',strtotime($mansked['manskeddays'][0]->date)) }} - 
              {{ date('D, M j',strtotime($mansked['manskeddays'][6]->date)) }}
            </span>

            <a role="button" data-toggle="collapse" data-parent="#accordion-week-days" href="#collapse-week{{ $mansked->weekno }}" aria-expanded="false" aria-controls="collapse-week{{ $mansked->weekno }}" class="collapsed pull-right">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </a>
            <span class="pull-right" style="margin-right:100px;">
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
     @else 
      </div>
     @endif
      

    
      
  
</div>

<!-- end main -->
</div>
@endsection


@section('js-external')
  @parent

  <script>
    $(".panel-heading.new").effect("highlight", {}, 2000);
  </script>
@endsection
