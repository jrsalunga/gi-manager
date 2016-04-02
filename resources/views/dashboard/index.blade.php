@extends('index')

@section('title', '- Create Daily Man Schedule')

@section('body-class', 'mansked-create')

@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> {{ $branch }}</li>
    <li class="active">Dashboard</li>
  </ol>

  @include('_partials.alerts')


  <div style="margin-top:50px;" class="hidden-xs"></div>
  <div style="margin-top:10px;" class="visible-xs-block"></div>
  <div class="row">
  	<div class="col-md-8">
  		<div id="panel-top-sales" class="panel panel-success">
        <div class="panel-heading">
          <h3 class="panel-title"><span class="fa fa-line-chart"></span> 
            Last 7 Days Stat
          </h3>
        </div>
        <div class="panel-body">
          <p class="text-right">

            <a href="/analytics" class="btn btn-success">
              <span class="gly gly-cardio"></span> 
              <span class="hidden-xs">Analytics</span>
            </a>

            <div class="table-responsive">
            <table class="table table-hover table-striped">
            	<thead>
            		<tr>
            			<td>Date</td>
            			<td class="text-right">Sales</td>
            			<td class="text-right">Purchased</td>
                  <td class="text-right">Customers</td>
                  <td class="text-right">Employees</td>
            			<td class="text-right">Man Cost %</td>
            			<td class="text-right">Sales per Emp</td>
            		</tr>
            	</thead>
            	<tbody>
            		<?php $ctr = 0 ?>
		            @foreach($dailysales as $ds)
		            	@if($ctr < 7)
		            	<tr>
		            		<td>{{ $ds->date->format('M j, D') }}</td>
		            		@if(!is_null($ds->dailysale))
                    <td class="text-right">{{ number_format($ds->dailysale->sales,2) }}</td>
		            		<td class="text-right">{{ number_format($ds->dailysale->purchcost,2) }}</td>
		            		<td class="text-right">{{ number_format($ds->dailysale->custcount,0,'',',') }}</td>
		            		<td class="text-right">{{ number_format($ds->dailysale->empcount,0,'',',') }}</td>
		            		<!--
                    <td class="text-right">{{ number_format($ds->dailysale->empcount*session('user.branchmancost'),2) }} </td> 
		            		-->
                    <td class="text-right">{{ number_format($ds->dailysale->mancostpct,2) }}%</td>
		            		<?php
			                $s = $ds->dailysale->empcount=='0' ? '0.00':($ds->dailysale->sales/$ds->dailysale->empcount);
			              ?>
			              <td class="text-right" data-sort="{{$s}}">{{number_format($s,2)}}</td>
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
      </div>
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
					</div>
  			</div>
  		</div> <!-- end: col-md-4 -->
  	</div>

				
</div><!-- end .container-fluid -->
@endsection




@section('js-external')
  @parent
  

<script>
  $('document').ready(function(){

   
  });
</script>
@endsection

