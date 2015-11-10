<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/> 
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <title>Giligan's Restaurant @yield('title')</title>

  <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />
@if(app()->environment() == 'local')
  <link rel="stylesheet" href="/css/normalize-3.0.3.min.css">
  <link rel="stylesheet" href="/css/font-awesome.min.css">
  <link rel="stylesheet" href="/css/bootstrap-3.3.5.css">
  <link rel="stylesheet" href="/css/bootstrap-select.min.css">
  <link rel="stylesheet" href="/css/dashboard.css">
  <link rel="stylesheet" href="/css/bt-override.css">
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="stylesheet" href="/css/common.css">
@else 
  <link rel="stylesheet" href="/css/styles-all.min.css">
@endif


</head>
<body class="tk">
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="/">
        <img src="/images/giligans-logo.png" class="img-responsive header-logo" style="max-height: 44px;">
      </a>
    </div>
    <div id="navbar" class="navbar-collapse collapse">
      
    </div>
  </div>
</nav>

<div class="container-fluid">
	<div class="tk-block row">
		<div class="l-pane col-sm-5">
      
      <div class="ts-group">
              
        <div class="ts">{{  strftime('%I:%M:%S', strtotime('now')) }}</div>
        <div class="am">{{  strftime('%p', strtotime('now')) }}</div>
        <div style="clear: both;"></div>
               
      </div>
      
      <div class="date-group">
        <div id="date">
          <span class="glyphicon glyphicon-calendar"> </span>       
          <time>{{  date('F j, Y', strtotime('now')) }}</time>
          
        </div>
        <div>
          <span>
            <span class="day">{{  date('l', strtotime('now')) }}</span>
          </span>
        </div>
      </div>

      <div class="emp-group">
        <div class="img-cont">
          <img  id="emp-img" src="/images/employees/{{ $timelogs[0]->employee->code }}.jpg" >
        </div>
        <div class="emp-cont">
          <p id="emp-code">{{ $timelogs[0]->employee->code }}</p>
          <h1 id="emp-name">{{ $timelogs[0]->employee->lastname }}, {{ $timelogs[0]->employee->firstname }}</h1>
          <p id="emp-pos">{{ $timelogs[0]->employee->position->descriptor }}</p>
        </div>
        <div style="clear: both;"></div>
      </div>
      
      <div class="message-group"></div>
      
      

		</div>
		<div class="r-pane col-sm-7">
      <div class="container-table">
        <table class="table table-condensed" role="table">
          <thead>
            <tr>
              <th>Emp No</th><th>Name</th><th>Date Time</th><th>Type</th><th>Branch</th>
            </tr>
          </thead>
          <tbody class="emp-tk-list">
            @foreach($timelogs as $timelog)
            <tr class="txncode{{ $timelog->txncode }}">
              <td>{{ $timelog->employee->code }}</td>
              <td>{{ $timelog->employee->lastname }}, {{ $timelog->employee->firstname }}</td>
              <td>
                <span>
                  {{ strftime('%b %d', strtotime($timelog->datetime)) }}
                </span>
                &nbsp;
                {{ strftime('%I:%M:%S %p', strtotime($timelog->datetime)) }}
              </td>
              <td>
                {{ $timelog->getTxnCode() }}   
              </td>
              <td>
                {{ $timelog->employee->branch->code }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
		</div>
	</div>	
</div>


<!-- modal ti/to -->	
<div class="modal fade" id="TKModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        
        <h4 class="modal-title" id="myModalLabel">Good day!</h4>
      </div>
      <div class="modal-body">
        <div class="emp-group">
        <div class="img-cont">
          <img  id="mdl-emp-img" src="">
        </div>
        <div class="emp-cont">
          <p id="mdl-emp-code"></p>
          <h1 id="mdl-emp-name"></h1>
          <p id="mdl-emp-pos"></p>
        </div>
        <div style="clear: both;"></div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" id="btn-time-in" class="btn btn-success btn-tk" data-dismiss="modal">
          press <strong>I</strong> for Time In
        </button>
        <button type="button" id="btn-break-start" class="btn btn-info btn-tk" data-dismiss="modal">
          press <strong>B</strong> for Break Start
        </button>
        <button type="button" id="btn-break-end" class="btn btn-warning btn-tk" data-dismiss="modal">
          press <strong>N</strong> for Break End
        </button>
        <button type="button" id="btn-time-out" class="btn btn-primary btn-tk" data-dismiss="modal">
          press <strong>O</strong> for Time Out
        </button>
        
      </div>
        <div class="mdl-f-options">
          <!--
          <p>Options:</p>
          <button type="button" class="btn btn-default btn-xs">press <strong>T</strong> to view timelog for the current month</button>
          -->
        <button type="button" class="btn btn-default btn-xs">press <strong>Esc</strong> to escape</button>
        </div>
    </div>
  </div>
</div>


<script src="/js/vendors-all.js"></script>
  <script src="/js/tk.js"></script>

</div>



