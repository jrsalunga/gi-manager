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
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
          <h4 class="panel-title">
            <span class="glyphicon glyphicon-option-vertical"></span> Week {{ $manskeds->first()->weekno+1 }}

            <span style="margin-left: 100px;">
              {{ date('D, M j',strtotime($manskeds[0]->getDaysByWeekNo($manskeds->first()->weekno+1)[0])) }} - 
              {{ date('D, M j',strtotime($manskeds[0]->getDaysByWeekNo($manskeds->first()->weekno+1)[6])) }}
            </span>

            <a href="" class="pull-right"><span class="glyphicon glyphicon-duplicate"></span></a>

            <a href="#" class="pull-right" style="margin-right:100px;"><span class="glyphicon glyphicon-plus"></span> create</a>
          </h4>
        </div>
      </div>
    @foreach($manskeds as $mansked)
      <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="week{{ $mansked->weekno }}">
          <h4 class="panel-title">
            <a role="button" data-toggle="collapse" data-parent="#accordion-week-days" href="#collapse-week{{ $mansked->weekno }}" aria-expanded="false" aria-controls="collapse-week{{ $mansked->weekno }}" class="collapsed">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </a>
            <a role="button" data-toggle="collapse" data-parent="#accordion-week-days" href="#collapse-week{{ $mansked->weekno }}" aria-expanded="false" aria-controls="collapse-week{{ $mansked->weekno }}" class="collapsed">
              Week {{ $mansked->weekno }}
            </a>

            <span style="margin-left: 100px;">
              {{ date('D, M j',strtotime($mansked['manskeddays'][0]->date)) }} - 
              {{ date('D, M j',strtotime($mansked['manskeddays'][6]->date)) }}
            </span>

            <a href="/task/mansked/week/{{$mansked->weekno}}" class="pull-right"><span class="gly gly-table"></span></a>

            <span class="pull-right" style="margin-right:100px;">
              {{ $mansked->refno }}
            </span>
          </h4>
        </div>
        <div id="collapse-week{{ $mansked->weekno }}" class="panel-collapse collapse {{ (session('weekno')==$mansked->weekno) ? 'in':'' }}" role="tabpanel" aria-labelledby="week{{ $mansked->weekno }}">
          <div class="panel-body">
            
            @foreach($mansked->manskeddays as $manday)
              <a href="/task/manday/{{$manday->lid()}}" class="btn alert-success"><i class="fa fa-calendar-o"></i> {{ date('D, M j',strtotime($manday->date)) }}</a>
            @endforeach
            
          </div>
        </div>
      </div>
    @endforeach
     </div>
      {{ session()->forget('weekno') }}
      
      

    
      
  
</div>

<!-- end main -->
</div>
@endsection


