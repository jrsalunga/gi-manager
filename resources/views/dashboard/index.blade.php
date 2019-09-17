@extends('index')

@section('title', '- Dashboard ('.strtoupper(brcode()).')')

@section('body-class', 'dashboard')

@section('container-body')
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> {{ $branch }}</li>
    <li class="active">Dashboard</li>
  </ol>

  @include('_partials.alerts')

  @if($inadequates)
    <div class="alert alert-warning alert-important">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <strong><span class="glyphicon glyphicon-warning-sign"></span> Warning</strong>: No backup uploaded on the following date(s) below. This may affect the report generation.
      <ul>
      @foreach($inadequates as $d) 
        <li>{{ $d->format('m/d/Y') }} - <b>GC{{ $d->format('mdy') }}.ZIP</b></li>
      @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
  	<div class="col-md-8">
  		<div id="panel-top-sales" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="fa fa-line-chart"></span> 
            Stats
          </h3>
        </div>
        <div class="panel-body">
          <p>

            <a href="/{{brcode()}}/analytics" class="btn btn-default">
              <span class="gly gly-cardio"></span> 
              <span class="hidden-xs">Analytics</span>
            </a>

            <span class="pull-right">
              Last backup:
             @if(is_null($backup))
              No backup uploaded
             @else
               <span class="{{ $backup->bg }}"> 
                 <span class="fa fa-file-archive-o"></span> 
                 <strong>{{ $backup->file->filename }}</strong>  
               </span> 
               <small><em>{{ $backup->diffForHumans }}</em></small>
               @if($backup->diffInDays>2)
                 <p class="text-danger"><span class="fa fa-exclamation-triangle"></span> 
                  <small><em>Your backup is {{ $backup->diffInDays }} days delayed. Kindly notify your cashier to upload your backup thru DropBox. </em></small>
                 </p>
               @endif
             @endif
            </span>

            <div class="table-responsive">
            <table class="table table-hover table-striped">
            	<thead>
            		<tr>
            			<td>Date</td>
            			<td class="text-right">Sales</td>
            			<td class="text-right">Purchased Goods</td>
                  <td class="text-right">OpEx</td>
                  <td class="text-right">Customers</td>
                  <td class="text-right">Employees</td>
                  <td class="text-right">Man Cost %</td>
            		</tr>
            	</thead>
            	<tbody>
            		<?php $ctr = 0 ?>
		            @foreach($dailysales as $ds)
		            	@if($ctr < 9)
		            	<tr>
		            		<td>{{ $ds->date->format('D, M j') }}</td>
		            		@if(!is_null($ds->dailysale))
                    <td class="text-right">
                      @if($ds->dailysale->slsmtd_totgrs>1)
                      <span class="help" data-toggle="tooltip" title="Net Sales: {{ number_format($ds->dailysale->sales,2) }}">
                        @if(number_format($ds->dailysale->sales,2)=='0.00')
                          -
                        @else
                          <a href="/{{brcode()}}/product/sales?fr={{$ds->date->format('Y-m-d')}}&to={{$ds->date->format('Y-m-d')}}" data-toggle="loader">
                          {{ number_format($ds->dailysale->sales,2) }}
                          </a>
                        @endif
                      </span>
                      @endif
                    </td>
                    <td class="text-right">
                      @if(number_format($ds->dailysale->cos,2)=='0.00')
                          -
                      @else
                        <a href="/{{brcode()}}/component/purchases?table=expscat&item=Food+Cost&itemid=7208aa3f5cf111e5adbc00ff59fbb323&fr={{$ds->date->format('Y-m-d')}}&to={{$ds->date->format('Y-m-d')}}" data-toggle="loader">
                          {{ number_format($ds->dailysale->cos,2) }}
                        </a>
                      @endif
                    </td>
                    <td class="text-right">
                      @if(number_format($ds->dailysale->getOpex(),2)=='0.00')
                          -
                      @else
                        <a href="/{{brcode()}}/component/purchases?table=expscat&item=Means+Operation&itemid=8a1c2ff95cf111e5adbc00ff59fbb323&fr={{$ds->date->format('Y-m-d')}}&to={{$ds->date->format('Y-m-d')}}" data-toggle="loader">
                          {{ number_format($ds->dailysale->getOpex(),2) }}
                        </a>
                      @endif
                    </td>
                    <!--
                    <td class="text-right">
                      <a href="/{{brcode()}}/component/purchases?fr={{$ds->date->format('Y-m-d')}}&to={{$ds->date->format('Y-m-d')}}" data-toggle="loader">
                        {{ number_format($ds->dailysale->purchcost,2) }}
                      </a>
                    </td>
                    -->
		            		<td class="text-right">
                      <?php $custcount = $ds->dailysale->custcount>0 ? number_format($ds->dailysale->custcount,0,'',','):'-' ?>
                      {{ $custcount }}
                    </td>
		            		<td class="text-right">
                      <?php $empcount = $ds->dailysale->empcount>0 ? number_format($ds->dailysale->empcount,0,'',','):'-' ?>
                      {{ $empcount }}
                    </td>
		            		<!--
                    <td class="text-right">{{ number_format($ds->dailysale->empcount*session('user.branchmancost'),2) }} </td> 
		            		-->
                    <td class="text-right">
                      <?php $mancostpct = $ds->dailysale->mancostpct>0 ? number_format($ds->dailysale->mancostpct,2).'%':'-' ?>
                      {{ $mancostpct }}
                    </td>
		            		<?php
			                $s = $ds->dailysale->empcount=='0' ? '0.00':($ds->dailysale->sales/$ds->dailysale->empcount);
			              ?>
                    <!--
			              <td class="text-right" data-sort="{{$s}}">{{number_format($s,2)}}</td>
                    -->
		            		@else
		            		<td class="text-right">-</td>
		            		<td class="text-right">-</td>
		            		<td class="text-right">-</td>
		            		<td class="text-right">-</td>
		            		<td class="text-right">-</td>
		            		<td class="text-right">-</td>
		            		@endif
		            	</tr>
		            	<?php $ctr++ ?>
		            	@endif
		            @endforeach
            	</tbody>
            </table>
        		</div>
            
          </p> 
          

          
        </div>
      </div><!-- end: .panel -->
    </div> <!-- end: col-md-8 -->
  	<div class="col-md-4">
  		<div id="panel-latest-backup" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="gly gly-notes-2"></span> 
            Task
          </h3>
        </div>
        <div class="panel-body">
          <div class="list-group">
            <a href="/task/mansked" class="list-group-item">Manpower Scheduling</a>
            <a href="/{{brcode()}}/timesheet" class="list-group-item">Daily Timesheet</a>
            <a href="#" class="list-group-item" style="color: #aaa; cursor: not-allowed;">DTR Generator</a>
            <a href="#" class="list-group-item" style="color: #aaa; cursor: not-allowed;">Weekly Branch Report</a>
          </div>
          <div class="list-group">
            <a href="/{{brcode()}}/employee" class="list-group-item"">Employees</a>
            <a href="#" class="list-group-item" style="color: #aaa; cursor: not-allowed;">Trainee Status Changes</a>
            <a href="#" class="list-group-item" style="color: #aaa; cursor: not-allowed;">Manpower Request</a>
            <a href="#" class="list-group-item" style="color: #aaa; cursor: not-allowed;">Requests for Employee Status Change</a>
            <a href="#" class="list-group-item" style="color: #aaa; cursor: not-allowed;">Notices of Resignation</a>
          </div>
          <div class="list-group">
            <a href="/{{brcode()}}/component/price/comparative" class="list-group-item"">Comparative Component Cost</a>
          </div>
  			</div>
  		</div> <!-- end: .panel -->
      <!--
      <div class="panel panel-default">
        <div class="panel-body">
           Last backup:
           @if(is_null($backup))
            No backup uploaded
           @else
             <span class="{{ $backup->bg }}"> 
               <span class="fa fa-file-archive-o"></span> 
               <strong>{{ $backup->file->filename }}</strong>  
             </span> 
             <small><em>{{ $backup->diffForHumans }}</em></small>
             @if($backup->diffInDays>1)
               <p class="text-danger"><span class="fa fa-exclamation-triangle"></span> 
                <small><em>Your backup is {{ $backup->diffInDays }} days delayed. Kindly notify your cashier to upload your backup thru DropBox. </em><small>
               </p>
             @endif
           @endif

        </div>
      </div>
      -->
    </div><!-- end: .col-md-4 -->
  </div><!-- end: .row -->

 

				
</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent
  

<script>
  $('document').ready(function(){

   
  });
</script>
@endsection

