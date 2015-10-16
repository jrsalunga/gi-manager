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
    <div class="panel-group" id="accordion-week-days" role="tablist" aria-multiselectable="true">
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

            <a href="/task/mansked/week/{{$mansked->weekno}}" class="pull-right"><span class="gly gly-table"></span></a>
            
          </h4>
        </div>
        <div id="collapse-week{{ $mansked->weekno }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="week{{ $mansked->weekno }}">
          <div class="panel-body">
            
            @foreach($mansked->manskeddays as $manday)
              <a href="/task/manday/{{$manday->lid()}}/edit" class="btn alert-success"><i class="fa fa-calendar-o"></i> {{ date('D, M j',strtotime($manday->date)) }}</a>
            @endforeach
            
          </div>
        </div>
      </div>
    @endforeach
     </div>
      
      

    
      
  
</div>

<!-- end main -->
</div>
@endsection


