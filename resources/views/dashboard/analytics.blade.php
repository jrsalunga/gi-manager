@extends('index')

@section('title', '- By Day Analytics')

@section('body-class', 'analytics-day')

@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/dashboard">{{ $branch }}</a></li>
    <li>Analytics</li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          
          <!--
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->

          <div class="btn-group btn-group pull-right clearfix" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => '/analytics', 'method' => 'get', 'id'=>'dp-form']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go"   }}>
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->fr->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->


          <!--
          <div class="btn-group" style="margin-left: 5px;">
            <div class="dropdown">
              <button id="dLabel" type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="gly gly-shop"></span>
                  <span class="br-code">{{ session('user.branchcode') }}</span>
                  <span class="br-desc hidden-xs hidden-sm">- {{ session('user.branch') }}</span>
              </button>
            </div> 
          </div>
          
          <div class="btn-group" role="group">
            <button class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
              <span class="hidden-xs hidden-sm">Daily</span> <span class="badge">{{ $dr->fr->diffInDays($dr->to, false)+1 }}</span>
            </button> 
            <a href="/analytics/month?fr={{$dr->now->copy()->subMonths(5)->endOfMonth()->format('Y-m-d')}}&to={{$dr->now->endOfMonth()->format('Y-m-d')}}" class="btn btn-default" title="Back to Main Menu">
              <span class="fa fa-calendar"></span>
              <span class="hidden-xs hidden-sm">Monthly</span> <span class="badge">{{ floor($dr->fr->diffInDays($dr->to, false)/30) }}</span>
            </a>
          </div> <!-- end btn-grp -->



          <div class="btn-group pull-right clearfix  dp-container" role="group">
            <label class="btn btn-default" for="dp-date-fr">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">
            <div class="btn btn-default" style="pointer-events: none;">-</div>
            <input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">
            <label class="btn btn-default" for="dp-date-to">
              <span class="glyphicon glyphicon-calendar"></span>
            </label>
          </div><!-- end btn-grp -->

          <div class="btn-group pull-right clearfix" role="group">
            <div class="btn-group date-type-selector" style="margin-left: 5px;">
              <div class="dropdown">
                <a class="btn btn-link" id="date-type" data-target="#" href="http://example.com" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span id="date-type-name">Daily</span>
                  <span class="caret"></span>
                </a>

                <ul class="dropdown-menu" aria-labelledby="date-type">
                  <li><a href="#" data-date-type="daily">Daily</a></li>
                  <li><a href="#" data-date-type="weekly">Weekly</a></li>
                  <li><a href="#" data-date-type="monthly">Monthly</a></li>
                  <li><a href="#" data-date-type="quarterly">Quarterly</a></li>
                  <li><a href="#" data-date-type="yearly">Yearly</a></li>
                </ul>
              </div>
            </div>
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')


    <div class="row">
      
      @if(is_null($dailysales))

      @else

      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Sales</p>
        <h3 id="h-tot-sales" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Purchased</p>
        <h3 id="h-tot-purch" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Total Manpower Cost</p>
        <h3 id="h-tot-mancost" style="margin:0">0</h3>
      </div>
      <div class="col-xs-6 col-md-3 text-right" style="margin-bottom: 10px;">
        <p style="margin-bottom:0">Sales per Employee</p>
        <h3 id="h-tot-tips" style="margin:0">0</h3>
      </div>

    </div>
    <div class="row">

      <div class="col-md-12">
        <div id="graph-container" style="overflow:hidden;">
          <div id="graph"></div>
        </div>
      </div>
    </div>
    <div class="row">

      <div class="col-md-12">
        <div class="table-responsive">
          <table class="table table-hover table-striped table-sort-data">
            <thead>
              <tr>
                  <th>Date</th>
                  <th class="text-right">Sales</th>
                  <th class="text-right">Purchased</th>
                  <th class="text-right">Customers</th>
                  <th class="text-right">Head Spend</th>
                  <th class="text-right">Emp Count</th>
                  <th class="text-right">Sales per Emp</th>
                  <th class="text-right">
                    <div style="font-weight: normal; font-size: 11px; cursor: help;">
                      <em title="Branch Mancost">{{ session('user.branchmancost') }}</em>
                    </div>
                    Man Cost
                  </th>
                  <th class="text-right">Man Cost %</th>
                  <th class="text-right">Tips</th>
                  <th class="text-right">Tips %</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $tot_sales = 0;
                $tot_purchcost = 0;
                $tot_custcount = 0;
                $tot_headspend = 0;
                $tot_empcount = 0;
                $tot_sales_emp = 0;
                $tot_mancost = 0;
                $tot_mancostpct = 0;
                $tot_tips = 0;
                $tot_tipspct = 0;

                $div_sales = 0;
                $div_purchcost = 0;
                $div_custcount = 0;
                $div_headspend = 0;
                $div_empcount = 0;
                $div_mancost = 0;
                $div_tips = 0;
              ?>
            @foreach($dailysales as $d)
            	<?php $div_sales+=($d->dailysale['sales']!=0)?1:0; ?>
            	<?php $div_purchcost+=($d->dailysale['purchcost']!=0)?1:0; ?>
            	<?php $div_custcount+=($d->dailysale['custcount']!=0)?1:0; ?>
            	<?php $div_headspend+=($d->dailysale['headspend']!=0)?1:0; ?>
            	<?php $div_empcount+=($d->dailysale['empcount']!=0)?1:0; ?>
            	<?php $div_tips+=($d->dailysale['tips']!=0)?1:0; ?>


            <tr {{ $d->date->dayOfWeek=='0' ? 'class=warning':''  }}>
              <td data-sort="{{$d->date->format('Y-m-d')}}">{{ $d->date->format('M j, D') }}</td>
              @if(!is_null($d->dailysale))
              <td class="text-right" data-sort="{{ number_format($d->dailysale['sales'], 2,'.','') }}">
                @if($d->dailysale['slsmtd_totgrs']>0)
                  <a href="#" class="text-primary btn-slsmtd-totgrs" data-id="{{strtolower($d->dailysale['id'])}}" data-date="{{$d->date->format('Y-m-d')}}">
                  {{ number_format($d->dailysale['sales'], 2) }}
                  </a>
                @else
                  {{ number_format($d->dailysale['sales'], 2) }}
                @endif
              </td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['purchcost'], 2,'.','') }}">
                @if($d->dailysale['purchcost']>0)
                <a href="#" data-date="{{ $d->date->format('Y-m-d') }}" data-id="{{strtolower($d->dailysale['id'])}}" class="text-primary btn-purch">
                  {{ number_format($d->dailysale['purchcost'], 2) }}
                  </a>
                @else
                  {{ number_format($d->dailysale['purchcost'], 2) }}
                @endif
                </a>
              </td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['custcount'], 0) }}">{{ number_format($d->dailysale['custcount'], 0) }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['headspend'], 2,'.','') }}">{{ number_format($d->dailysale['headspend'], 2) }}</td>
              <td class="text-right" data-sort="{{ $d->dailysale['empcount'] }}">{{ $d->dailysale['empcount'] }}</td>
              <?php
                $s = $d->dailysale['empcount']=='0' ? '0.00':($d->dailysale['sales']/$d->dailysale['empcount']);
              ?>
              <td class="text-right" data-sort="{{$s}}">{{number_format($s,2)}}</td>
              <?php
                $mancost = $d->dailysale['empcount']*session('user.branchmancost');
                $div_mancost+=($mancost!=0)?1:0; 
              ?>
              <td class="text-right" data-sort="{{ number_format($mancost,2,'.','') }}" 
                  title="runtime computation: {{$d->dailysale['empcount']}}*{{session('user.branchmancost')+0}} = {{ number_format($mancost,2) }}">
                {{ number_format($d->dailysale['mancost'],0) }}
              </td>
              <td class="text-right" data-sort="{{ $d->dailysale['mancostpct'] }}"
                @if(!empty($d->dailysale['sales']) && $d->dailysale['sales']!='0.00' && $d->dailysale['sales']!='0')   
                title="({{$d->dailysale['empcount']}}*{{session('user.branchmancost')}})/{{$d->dailysale['sales']}} 
                ={{(($d->dailysale['empcount']*session('user.branchmancost'))/$d->dailysale['sales'])*100}} "
                @endif
                >{{ $d->dailysale['mancostpct'] }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['tips'],2,'.','') }}">{{ number_format($d->dailysale['tips'],2) }}</td>
              <td class="text-right" data-sort="{{ number_format($d->dailysale['tipspct'],2,'.','') }}">{{ number_format($d->dailysale['tipspct'], 2) }}</td>
              <?php
                $tot_sales      += $d->dailysale['sales'];
                $tot_purchcost  += $d->dailysale['purchcost'];
                $tot_custcount  += $d->dailysale['custcount'];
                $tot_headspend  += $d->dailysale['headspend'];
                $tot_empcount   += $d->dailysale['empcount'];

                if($d->dailysale['empcount']!='0') {
                  $tot_sales_emp += number_format(($d->dailysale['sales']/$d->dailysale['empcount']),2, '.', '');
                }

                $tot_mancost    += $mancost;
                $tot_mancostpct += $d->dailysale['mancostpct'];
                $tot_tips       += $d->dailysale['tips'];
                $tot_tipspct    += $d->dailysale['tipspct'];
              ?>
              @else 
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              <td class="text-right" data-sort="-">-</td>
              @endif
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td>
                <strong>
                {{ count($dailysales) }}
                {{ count($dailysales) > 1 ? 'days':'day' }}
                </strong>
              </td>
              <td class="text-right">
                <strong id="f-tot-sales">{{ number_format($tot_sales,2) }}</strong>
                <div>
                <em><small title="{{$tot_sales}}/{{$div_sales}}">
                  {{ $div_sales!=0?number_format($tot_sales/$div_sales,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-purch">{{ number_format($tot_purchcost,2) }}</strong>
                <div>
                <em><small title="{{$tot_purchcost}}/{{$div_purchcost}}">
                  {{ $div_purchcost!=0?number_format($tot_purchcost/$div_purchcost,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_custcount, 0) }}</strong>
                <div>
                <em><small title="{{$tot_custcount}}/{{$div_custcount}}">
                  {{ $div_custcount!=0?number_format($tot_custcount/$div_custcount,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="{{$tot_headspend}}/{{$div_headspend}}">
                  {{ $div_headspend!=0?number_format($tot_headspend/$div_headspend,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_empcount,0) }}</strong>
                <div>
                <em><small title="{{$tot_empcount}}/{{$div_empcount}}">
                  {{ $div_empcount!=0?number_format($tot_empcount/$div_empcount,2):0 }}
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small id="f-tot-tips" title="{{$tot_sales}}/{{$tot_empcount}}" >
                  @if($tot_empcount!='0')
                    {{ number_format($tot_sales/$tot_empcount,2) }}
                    <!--
                    {{ number_format($tot_sales-($tot_purchcost+$tot_mancost),2) }}
                    -->
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong id="f-tot-mancost">{{ number_format($tot_mancost,2) }}</strong>
                <div>
                <em><small title="{{$tot_mancost}}/{{$div_mancost}}">
                	@if($div_mancost!='0')
                  {{ number_format($tot_mancost/$div_mancost,2) }}
                   @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp;</strong>
                <div>
                <em><small title="(({{$tot_empcount}}*{{session('user.branchmancost')}})/{{$tot_sales}})*100">
                  @if($tot_sales!='0')
                  {{ number_format((($tot_empcount*session('user.branchmancost'))/$tot_sales)*100,2) }}%
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>{{ number_format($tot_tips,2) }}</strong>
                <div>
                <em><small title="{{$tot_tips}}/{{$div_tips}}">
                	{{ $div_tips!=0?number_format($tot_tips/$div_tips,2):0 }}</small></em>
                </div>
              </td>
              <td class="text-right">
                <strong>&nbsp; </strong>
                <div>
                <em><small title="({{$tot_tips}}/{{$tot_sales}})*100 ">
                  @if($tot_sales!='0')
                  {{ number_format(($tot_tips/$tot_sales)*100,2) }}%
                  @else
                    0
                  @endif
                </small></em>
                </div>
              </td>
            </tr>
          </tfoot>
        </table>

        <table id="datatable" class="tb-data" style="display:none;">
          <thead>
            <tr>
                <th>Date</th>
                <th>Sales</th>
                <th>Purchased</th>
                <th>Emp Count</th>
                <th>Customer</th>
                <th>Man Cost</th>
                <th>Tips</th>
                <th>Sales per Emp</th>
            </tr>
          </thead>
          <tbody>
            @foreach($dailysales as $d)
            <tr>
              <td>{{ $d->date->format('Y-m-d') }}</td>
              @if(!is_null($d->dailysale))
              <td>{{ $d->dailysale['sales'] }}</td>
              <td>{{ $d->dailysale['purchcost'] }}</td>
              <td>{{ $d->dailysale['empcount'] }}</td>
              <td>{{ $d->dailysale['custcount'] }}</td>
              <td>{{ $d->dailysale['mancost'] }}</td>
              <td>{{ $d->dailysale['tips'] }}</td>
              <td>{{ $d->dailysale['empcount']=='0' ? 0:number_format(($d->dailysale['sales']/$d->dailysale['empcount']), 2, '.', '') }}</td>
              @else 
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              @endif
              </tr>
            @endforeach
          </tbody>
        </table>
      </div><!--  end: table-responsive -->
      </div>
          @endif
    </div>
  </div>



</div><!-- end .container-fluid -->




<div class="modal fade" id="mdl-purchased" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Purchased <small></small></h4>
      </div>
      <div class="modal-body">
        
        <ul class="nav nav-pills" role="tablist">
          <li role="presentation" class="active">
            <a href="#items" aria-controls="items" role="tab" data-toggle="tab">
              <span class="gly gly-shopping-cart"></span>
              <span class="hidden-xs hidden-sm">
                Components
              </span>
            </a>
          </li>
          
          <li role="presentation">
            <a href="#stats" aria-controls="stats" role="tab" data-toggle="tab">
              <span class="gly gly-charts"></span>
              <span class="hidden-xs hidden-sm">
                Stats
              </span>
            </a>
          </li>

          <li role="presentation">
            <a href="#" id="link-download">
              <span class="gly gly-disk-save"></span>
              <span class="hidden-xs hidden-sm">
              Download
              </span>
            </a>
          </li>
          <!--
          <li role="presentation">
            <a href="#" id="link-print" target="_blank">
              <span class="glyphicon glyphicon-print"></span>
              <span class="hidden-xs hidden-sm">
              Printer Friendly
              </span>
            </a>
          </li>
          -->
          <li role="presentation" style="float: right;">
            <div>
            Total Purchased Cost: 
            <h3 id="tot-purch-cost" class="text-right" style="margin:0 0 10px 0;"></h3>
            </div>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="items">
            
            <div class="table-responsive">
              <table class="tb-purchase-data table table-condensed table-hover table-striped table-sort">
                <thead>
                  <tr>
                    <th class="text-right">#</th>
                    <th>Component</th>
                    <th>Category</th>
                    <th>UoM</th>
                    <th class="text-right">Qty</th>
                    <th class="text-right">Unit Cost</th>
                    <th class="text-right">Total Cost</th>
                    <th class="text-right">Supplier</th>
                    <th class="text-right">Terms</th>
                    <th class="text-right">VAT</th>
                  </tr>
                </thead>
                <tbody class="tb-data">
                </tbody>
              </table>
            </div><!-- end: .table-responsive -->
            
          </div><!-- end: #items.tab-pane -->
          
          <div role="tabpanel" class="tab-pane" id="stats">
            
            <div class="panel panel-default">
              <div class="panel-heading">Category</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                    <div class="table-responsive">
                      <table id="category-data" class="tb-category-data table table-condensed table-hover table-striped table-sort">
                        <thead>
                          <tr>
                            <th class="text-right">#</th>
                            <th>Category</th>
                            <th style="display:none;">Cost</th>
                            <th>Total Cost</th>
                          </tr>
                        </thead>
                      </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                  <div class="col-md-6">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-category" data-table="#category-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
            <div class="panel panel-default">
              <div class="panel-heading">Expense Code</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                    <div class="table-responsive">
                      <table id="expense-data" class="tb-expense-data table table-condensed table-hover table-striped table-sort">
                        <thead>
                          <tr>
                            <th class="text-right">#</th>
                            <th>Expense Code</th>
                            <th style="display:none;">Cost</th>
                            <th>Total Cost</th>
                          </tr>
                        </thead>
                      </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                  <div class="col-md-6">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-expense" data-table="#expense-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->
            <div class="panel panel-default">
              <div class="panel-heading">Supplier</div>
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-6">
                    <div class="row">
                    <div class="table-responsive">
                      <table id="supplier-data" class="tb-supplier-data table table-condensed table-hover table-striped table-sort">
                        <thead>
                          <tr>
                            <th class="text-right">#</th>
                            <th>Supplier</th>
                            <th style="display:none;">Cost</th>
                            <th>Total Cost</th>
                          </tr>
                        </thead>
                      </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                  </div><!-- end: .col-md-7 -->
                  <div class="col-md-6">
                    <div class="graph-container pull-right">
                      <div id="graph-pie-supplier" data-table="#supplier-data"></div>
                    </div>
                  </div><!-- end: .col-md-5 -->
                </div><!-- end: .row -->
              </div>
            </div><!-- end: .panel.panel-default -->

          </div><!-- end: .tab-pane --> 

        </div><!-- end: .tab-content -->
        

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-link pull-right" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>


<div class="modal fade mdl-col-collapse" id="mdl-generic" tabindex="-1" role="dialog" aria-labelledby="bookModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Loading</h4>
      </div>
      <div class="modal-body">
        <p class="text-center"><img src="/images/spinner_google.gif"></p>
        <p class="text-center">Loading content...</p>
      </div>
    </div>
  </div>
</div>

@endsection




@section('js-external')
  @parent
  @include('_partials.js-vendor-highcharts')
  
<script>
  moment.locale('en', { week : {
      dow : 1 // Monday is the first day of the week.
    }});

    Highcharts.setOptions({
      lang: {
        thousandsSep: ','
    }});

    var getOptions = function(to, table) {
        var options = {
          data: {
            table: table,
            startColumn: 0,
            endColumn: 1,
          },
          chart: {
            renderTo: to,
            type: 'pie',
            height: 300,
            width: 300,
            events: {
              load: function (e) {
                //console.log(e.target.series[0].data);
              }
            }
          },
          title: {
              text: ''
          },
          style: {
            fontFamily: "Helvetica"
          },
          tooltip: {
            pointFormat: '{point.y:.2f}  <b>({point.percentage:.2f}%)</b>'
          },
          plotOptions: {
            pie: {
              allowPointSelect: true,
              cursor: 'pointer',
              dataLabels: {
                  enabled: false
              },
              showInLegend: true,
              point: {
                events: {
                  mouseOver: function(e) {    
                    var orig = this.name;
                    var tb = $(this.series.chart.container).parent().data('table');
                    var tr = $(tb).children('tbody').children('tr');
                     _.each(tr, function(tr, key, list){
                      var text = $(tr).children('td:nth-child(2)').text();             
                      if(text==orig){
                        $(tr).children('td').addClass('bg-success');
                      }
                    });
                  },
                  mouseOut: function() {
                    var orig = this.name;
                    var tb = $(this.series.chart.container).parent().data('table');
                    var tr = $(tb).children('tbody').children('tr');
                     _.each(tr, function(tr, key, list){
                        $(tr).children('td').removeClass('bg-success');
                    });
                  },
                  click: function(event) {
                    //console.log(this);
                  }
                }
              }
            }
          },
          
          legend: {
            enabled: false,
            //layout: 'vertical',
            //align: 'right',
            //width: 400,
            //verticalAlign: 'top',
            borderWidth: 0,
            useHTML: true,
            labelFormatter: function() {
              //total += this.y;
              return '<div style="width:400px"><span style="float: left; width: 250px;">' + this.name + '</span><span style="float: left; width: 100px; text-align: right;">' + this.percentage.toFixed(2) + '%</span></div>';
            },
            title: {
              text: null,
            },
              itemStyle: {
              fontWeight: 'normal',
              fontSize: '12px',
              lineHeight: '12px'
            }
          },
          
          exporting: {
            enabled: false
          }
        }
        return options;
      }

    var initDatePicker = function(){

      $('#dp-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        //calendarWeeks: true,
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-date-to').datetimepicker({
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        //calendarWeeks: true,
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });

      $('#dp-m-date-fr').datetimepicker({
        //defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        viewMode: 'months'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-m-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-m-date-to').datetimepicker({
       // defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        viewMode: 'months'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-m-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-y-date-fr').datetimepicker({
        format: 'YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        viewMode: 'years'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-y-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-y-date-to').datetimepicker({
        format: 'YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        viewMode: 'years'
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-y-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      function getWeekNumber(d) {
        // Copy date so don't modify original
        d = new Date(+d);
        d.setHours(0,0,0);
        // Set to nearest Thursday: current date + 4 - current day number
        // Make Sunday's day number 7
        d.setDate(d.getDate() + 4 - (d.getDay()||7));
        // Get first day of year
        var yearStart = new Date(d.getFullYear(),0,1);
        // Calculate full weeks to nearest Thursday
        var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7)
        // Return array of year and week number
        return [d.getFullYear(), weekNo];
      }

      function weeksInYear(year) {
        var d = new Date(year, 11, 31);
        var week = getWeekNumber(d)[1];
        return week == 1? getWeekNumber(d.setDate(24))[1] : week;
      }

      var changeWeek = function(t, year, week) {
        //console.log(t[0].id);
        var WiY = weeksInYear(t[0].value);
        if(t[0].id===year){
          if($('#'+week+' option').length===52 && WiY===53) {
            //console.log('53 dapat');
            $('#'+week+' option:last-of-type').after('<option value="53">53</option>');
          } else if($('#'+week+' option').length===53 && WiY===52) {
            //console.log('52 lang');
            $('#'+week+' option:last-of-type').detach();
          } else {
            //console.log('sakto lang');
          }
          
        }
        console.log($('.dp-w-fr')[0].value+' '+WiY);
      }


      $('.dp-w-fr').on('change', function(e){

        changeWeek($(this), 'fr-year', 'fr-week');

        var day = moment($('.dp-w-fr')[0].value+'-08-27').startOf('week').isoWeek($('.dp-w-fr')[1].value);
        console.log(day.format('YYYY-MM-DD'));

        $('#fr').val(day.format('YYYY-MM-DD'));
        //console.log(moment().startOf('week').week($('.dp-w-fr')[1].value));
        //console.log(moment($('.dp-w-fr')[0].value+'W0'+$('.dp-w-fr')[1].value+'1'));
      });


      $('.dp-w-to').on('change', function(e){

        changeWeek($(this), 'to-year', 'to-week');

        var day = moment($('.dp-w-to')[0].value+'-08-27').startOf('week').isoWeek($('.dp-w-to')[1].value);
        console.log(day.add(6, 'days').format('YYYY-MM-DD'));
        $('#to').val(day.format('YYYY-MM-DD'));
        
      });


      /***** quarter *****/
      $('.dp-q-fr').on('change', function(e){
        var day = moment($('.dp-q-fr')[0].value+'-'+$('.dp-q-fr')[1].value);
        console.log(day.format('YYYY-MM-DD'));
        $('#fr').val(day.format('YYYY-MM-DD'));
      });

      $('.dp-q-to').on('change', function(e){
        var day = moment($('.dp-q-to')[0].value+'-'+$('.dp-q-to')[1].value);
        console.log(day.format('YYYY-MM-DD'));
        $('#to').val(day.format('YYYY-MM-DD'));
      });
      /***** end:quarter *****/

    } /* end inidDatePicker */

  var fetchPurchased = function(a){
    var formData = a;
    //console.log(formData);
    return $.ajax({
          type: 'GET',
          contentType: 'application/x-www-form-urlencoded',
          url: '/api/t/purchase',
          data: formData,
          //async: false,
          success: function(d, textStatus, jqXHR){

          },
          error: function(jqXHR, textStatus, errorThrown){
            alert('Error on fetching data...');
          }
      }); 
  }

  var fetchPurchasedView = function(id, date) {
        var m = 1;
        if (pLastId===id && module==m) {
          $('#mdl-generic').modal('show');
        } else {
          $('#mdl-generic').modal('show');
          $('#mdl-generic .modal-content').html(ghtml);
          pLastId=id;
          module = 1;

          return $.ajax({
            method: 'GET',
            url: '/api/mdl/purchases/'+id,
            dataType: 'html',
            data: {
              fr: date,
              to: date
            },
            beforeSend: function(jqXHR, obj) {
              
              $('#mdl-generic .modal-content').html(ghtml);
              
            },
            success: function(data, textStatus, jqXHR) {
              
              $('#mdl-generic .modal-content').html(data);
              
              $('.tb-component-data').tablesorter({sortList: [[3,1]]});
              $('.tb-compcat-data').tablesorter({sortList: [[3,1]]});
              $('.tb-expense-data').tablesorter({sortList: [[3,1]]});
              $('.tb-expscat-data').tablesorter({sortList: [[3,1]]});
              $('.tb-payment-data').tablesorter({sortList: [[3,1]]});
              $('.tb-supplier-data').tablesorter({sortList: [[3,1]]});
              var componentChart = new Highcharts.Chart(getOptions('graph-pie-component-sale', 'component-purch-data'));
              var compcatChart = new Highcharts.Chart(getOptions('graph-pie-compcat-sale', 'compcat-purch-data'));
              var expenseChart = new Highcharts.Chart(getOptions('graph-pie-expense-sale', 'expense-purch-data'));
              var expscatChart = new Highcharts.Chart(getOptions('graph-pie-expscat-sale', 'expscat-purch-data'));
              var paymentChart = new Highcharts.Chart(getOptions('graph-pie-payment-sale', 'payment-purch-data'));
              var supplierChart = new Highcharts.Chart(getOptions('graph-pie-supplier-sale', 'supplier-purch-data'));
              
             
            },
            error: function(data, textStatus, jqXHR) {
              console.log('error');
              console.log(data);
              console.log(textStatus);
              console.log(jqXHR);
            }
          })
        }
      }

  var ghtml = '<div class="modal-header">'
                +'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
                +'<h4 class="modal-title" id="myModalLabel">Loading</h4></div>'
                +'<div class="modal-body"><p class="text-center"><img src="/images/spinner_google.gif"></p><p class="text-center">Loading content...</p></div>';

  var module = 0;              
  var lastId = 0;
  var pLastId = 0;

  var fetchSalesView = function(id, date) {
    var m = 2;
    console.log(id);
    if (lastId===id && module==m) {
      $('#mdl-generic').modal('show');
    } else {
      $('#mdl-generic').modal('show');
      
      lastId=id;
      module=2;


      return $.ajax({
        method: 'GET',
        url: '/api/mdl/sales/'+id,
        dataType: 'html',
        data: {
          fr: date,
          to: date
        },
        beforeSend: function(jqXHR, obj) {
          
          $('#mdl-generic .modal-content').html(ghtml);
          
        },
        success: function(data, textStatus, jqXHR) {
         
          
          $('#mdl-generic .modal-content').html(data);
          $('.tb-sales-data').tablesorter(); 

          $('.tb-product-data').tablesorter({sortList: [[2,1]]});
          $('.tb-prodcat-data').tablesorter({sortList: [[2,1]]});
          $('.tb-menucat-data').tablesorter({sortList: [[2,1]]});

          var productChart = new Highcharts.Chart(getOptions('graph-pie-product-sale', 'product-sale-data'));
          var prodcatChart = new Highcharts.Chart(getOptions('graph-pie-prodcat-sale', 'prodcat-sale-data'));
          var menucatChart = new Highcharts.Chart(getOptions('graph-pie-menucat-sale', 'menucat-sale-data'));
          

         
        },
        error: function(data, textStatus, jqXHR) {
          console.log('error');
          console.log(data);
          console.log(textStatus);
          console.log(jqXHR);
        }
      })
    }

  }


  $('document').ready(function(){

    initDatePicker();

    var getOptions = function(to, table) {
      var options = {
        data: {
          table: table,
          startColumn: 1,
          endColumn: 2,
        },
        chart: {
          renderTo: to,
          type: 'pie',
          height: 300,
          width: 300,
          events: {
            load: function (e) {
              //console.log(e.target.series[0].data);
            }
          }
        },
        title: {
            text: ''
        },
        style: {
          fontFamily: "Helvetica"
        },
        tooltip: {
          pointFormat: '{point.y:.2f}  <b>({point.percentage:.2f}%)</b>'
        },
        plotOptions: {
          pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: false
            },
            showInLegend: true,
            point: {
              events: {
                mouseOver: function(e) {    
                  var orig = this.name;
                  var tb = $(this.series.chart.container).parent().data('table');
                  var tr = $(tb).children('tbody').children('tr');
                   _.each(tr, function(tr, key, list){
                    var text = $(tr).children('td:nth-child(2)').text();             
                    if(text==orig){
                      $(tr).children('td').addClass('bg-success');
                    }
                  });
                },
                mouseOut: function() {
                  var orig = this.name;
                  var tb = $(this.series.chart.container).parent().data('table');
                  var tr = $(tb).children('tbody').children('tr');
                   _.each(tr, function(tr, key, list){
                      $(tr).children('td').removeClass('bg-success');
                  });
                },
                click: function(event) {
                  //console.log(this);
                }
              }
            }
          }
        },
        
        legend: {
          enabled: false,
          //layout: 'vertical',
          //align: 'right',
          //width: 400,
          //verticalAlign: 'top',
          borderWidth: 0,
          useHTML: true,
          labelFormatter: function() {
            //total += this.y;
            return '<div style="width:400px"><span style="float: left; width: 250px;">' + this.name + '</span><span style="float: left; width: 100px; text-align: right;">' + this.percentage.toFixed(2) + '%</span></div>';
          },
          title: {
            text: null,
          },
            itemStyle: {
            fontWeight: 'normal',
            fontSize: '12px',
            lineHeight: '12px'
          }
        },
        
        exporting: {
          enabled: false
        }
      }
      return options;
    }

    Highcharts.setOptions({
      lang: {
        thousandsSep: ','
    }});

    


    $('.btn-purchx').on('click', function(e){
      e.preventDefault();
      var data = {};
      data.date = $(this).data('date');
      data.branchid = "{{session('user.branchid')}}";

      fetchPurchased(data).success(function(d, textStatus, jqXHR){
        console.log(d);
        if(d.code===200){
          $('.modal-title small').text(moment(d.data.items.date).format('ddd MMM D, YYYY'));
          renderToTable(d.data.items.data);  
          renderTable(d.data.stats.categories, '.tb-category-data');  
          var categoryChart = new Highcharts.Chart(getOptions('graph-pie-category', 'category-data'));
          renderTable(d.data.stats.expenses, '.tb-expense-data');  
          var expenseChart = new Highcharts.Chart(getOptions('graph-pie-expense', 'expense-data'));
          renderTable(d.data.stats.suppliers, '.tb-supplier-data');  
          var supplierChart = new Highcharts.Chart(getOptions('graph-pie-supplier', 'supplier-data'));
          $('#link-download')[0].href="/api/t/purchase?date="+moment(d.data.items.date).format('YYYY-MM-DD')+"&download=1";
          //$('#link-print')[0].href="/api/t/purchase?date="+moment(d.date).format('YYYY-MM-DD');
          $('ul[role=tablist] a:first').tab('show');
          $('#mdl-purchased').modal('show');
        } else if(d.code===401) {
          document.location.href = '/analytics';
        } else {
          alert('Error on fetching data. Kindly refresh your browser');
        }
      });

    });

    $('#mdl-generic').delegate('.show.toggle', 'click', function() {
        var div = $(this).siblings('div.show');
        if(div.hasClass('less')) {
          div.removeClass('less');
          div.addClass('more');
          $(this).text('show less');
        } else if(div.hasClass('more')) {
          div.removeClass('more');
          div.addClass('less');
          $(this).text('show more');
        }
      });


    $('.btn-slsmtd-totgrs').on('click', function(e){
      e.preventDefault();
      fetchSalesView($(this).data('id'), $(this).data('date'));
    });

    $('.btn-purch').on('click', function(e){
      e.preventDefault();
      fetchPurchasedView($(this).data('id'), $(this).data('date'));
    });

     


    var renderToTable = function(data) {
      var tr = '';
      var ctr = 1;
      var totcost = 0;
      _.each(data, function(purchase, key, list){
          //console.log(purchase);
          tr += '<tr>';
          tr += '<td class="text-right">'+ ctr +'</td>';
          tr += '<td>'+ purchase.comp +'</td>';
          tr += '<td>'+ purchase.catname +'</td>';
          tr += '<td>'+ purchase.unit +'</td>';
          tr += '<td class="text-right">'+ purchase.qty +'</td>';
          tr += '<td class="text-right">'+ accounting.formatMoney(purchase.ucost, "", 2, ",", ".") +'</td>';
          tr += '<td class="text-right">'+ accounting.formatMoney(purchase.tcost, "", 2, ",", ".") +'</td>';
          tr += '<td class="text-right" data-toggle="tooltip" data-placement="top" title="'+ purchase.supname +'">'+ purchase.supno +'</td>';
          tr += '<td class="text-right">'+ purchase.terms +'</td>';
          tr += '<td class="text-right">'+ purchase.vat +'</td>';
          tr +='</tr>';
          ctr++;
          totcost += parseFloat(purchase.tcost);
      });
      $('#tot-purch-cost').html(accounting.formatMoney(totcost, "", 2, ",", "."));
      $('.tb-purchase-data .tb-data').html(tr);
      $('.table-sort').trigger('update')
                      .trigger('sorton', [[0,0]]);
      
    }




    var renderTable = function(data, table) {
      var tr = '';
      var ctr = 1;
      var totcost = 0;
      tr += '<tbody>';
      _.each(data, function(value, key, list){
          //console.log(key);
          tr += '<tr>';
          tr += '<td class="text-right">'+ ctr +'</td>';
          tr += '<td>'+ key +'</td>';
          tr += '<td style="display:none;">'+value +'</td>';
          tr += '<td class="text-right">'+ accounting.formatMoney(value, "", 2, ",", ".") +'</td>';
          tr +='</tr>';
          ctr++;
          totcost += parseFloat(value);
      });
      tr += '</tbody>';
      //tr += '<tfoot><tr><td></td><td class="text-right"><strong>Total</strong></td>';
      //tr += '<td class="text-right"><strong>'+accounting.formatMoney(totcost, "", 2, ",", ".")+'</strong></td></tr><tfoot>';

      
      $(table+' tfoot').remove();
      $(table+' tbody').remove();
      $(table+' thead').after(tr);
      $(table).tablesorter(); 
      $(table).trigger('update');


      
    }





  	$('#dp-date-fr').datetimepicker({
        defaultDate: "{{ $dr->fr->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        ignoreReadonly: true,
        calendarWeeks: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        console.log(date);
        $('#dp-date-to').data("DateTimePicker").minDate(e.date);
        $('#fr').val(date);
        if($('#fr').data('fr')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });


      $('#dp-date-to').datetimepicker({
        defaultDate: "{{ $dr->to->format('Y-m-d') }}",
        format: 'MM/DD/YYYY',
        showTodayButton: true,
        useCurrent: false,
        ignoreReadonly: true,
        calendarWeeks: true
      }).on('dp.change', function(e){
        var date = e.date.format('YYYY-MM-DD');
        $('#dp-date-fr').data("DateTimePicker").maxDate(e.date);
        $('#to').val(date);
        if($('#to').data('to')==date)
          $('.btn-go').prop('disabled', true);
        else
          $('.btn-go').prop('disabled', false);
      });

      Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: "Helvetica"
            }
        }
    });

    var arr = [];

    $('#graph').highcharts({
      data: {
          table: 'datatable'
      },
      chart: {
        type: 'line',
        height: 300,
        //spacingRight: 20,
        marginTop: 40,
        //marginRight: 20,
        //marginRight: 20,
        zoomType: 'x',
        panning: true,
        panKey: 'shift'
      },
      colors: ['#15C0C2','#D36A71', '#B09ADB', '#5CB1EF', '#F49041', '#f15c80', '#F9CDAD', '#91e8e1', '#8d4653'],
      title: {
          text: ''
      },
      xAxis: [
        {
          gridLineColor: "#CCCCCC",
          type: 'datetime',
          //tickInterval: 24 * 3600 * 1000, // one week
          tickWidth: 0,
          gridLineWidth: 0,
          lineColor: "#C0D0E0", // line on X axis
          labels: {
            align: 'center',
            x: 3,
            y: 15,
            formatter: function () {
              //var date = new Date(this.value);
              //console.log(date.getDay());
              //console.log(date);
              return Highcharts.dateFormat('%b %e', this.value);
            }
          },
          plotLines: arr
        },
        { // slave axis
          type: 'datetime',
          linkedTo: 0,
          opposite: true,
          tickInterval: 7 * 24 * 3600 * 1000,
          tickWidth: 0,
          labels: {
            formatter: function () {
              arr.push({ // mark the weekend
                color: "#CCCCCC",
                width: 1,
                value: this.value-86400000,
                zIndex: 3
              });
              //return Highcharts.dateFormat('%a', (this.value-86400000));
            }
          }
        }
      ],
      yAxis: [{ // left y axis
        min: 0,
          title: {
            text: null
          },
          labels: {
            align: 'left',
            x: 3,
            y: 16,
            format: '{value:.,0f}'
          },
            showFirstLabel: false
          },
          { // right y axis
          min: 0,
            title: {
              text: null
            },
            labels: {
              align: 'right',
              x: -10,
              y: 15,
              format: '{value:.,0f}'
            },
              showFirstLabel: false,
              opposite: true
            }], 
      legend: {
        align: 'left',
        verticalAlign: 'top',
        y: -10,
        floating: true,
        borderWidth: 0
      },
      tooltip: {
        shared: true,
        crosshairs: true
      },
      plotOptions: {
        series: {
          cursor: 'pointer',
          point: {
            events: {
              click: function (e) {
              console.log(Highcharts.dateFormat('%Y-%m-%d', this.x));
              /*
                hs.htmlExpand(null, {
                    pageOrigin: {
                        x: e.pageX,
                        y: e.pageY
                    },
                    headingText: this.series.name,
                    maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+
                        this.y +' visits',
                    width: 200
                });
              */
              }
            }
          },
          marker: {
            symbol: 'circle',
            radius: 3
          },
          lineWidth: 2,
          dataLabels: {
              enabled: false,
              align: 'right',
              crop: false,
              formatter: function () {
                console.log(this.series.index);
                return this.series.name;
              },
              x: 1,
              verticalAlign: 'middle'
          }
        }
      },
      exporting: {
        enabled: false
      },
        series: [
          {
            type: 'line',
            yAxis: 0
          }, {
            type: 'line',
            yAxis: 0
          }, {
            type: 'line',
             dashStyle: 'shortdot',
            yAxis: 1
          }, {
            type: 'line',
             dashStyle: 'shortdot',
            yAxis: 1,
            visible: false
          }, {
            type: 'line',
            yAxis: 0
          }, {
            type: 'line',
            //dashStyle: 'shortdot',
            yAxis: 0,
            visible: false
          }, {
            type: 'line',
            yAxis: 0,
            visible: false
          }
        ]
    });



    $('#h-tot-sales').text($('#f-tot-sales').text());
    $('#h-tot-purch').text($('#f-tot-purch').text());
    $('#h-tot-mancost').text($('#f-tot-mancost').text());
    $('#h-tot-tips').text($('#f-tot-tips').text());

    $('.date-type-selector .dropdown-menu li a').on('click', function(e){
      
        e.preventDefault();

        var type = $(this).data('date-type');
          $('#date-type-name').text($(this)[0].text);
          $('.dp-container').html(getDatePickerLayout(type));
          initDatePicker();
      });

      var getDatePickerLayout = function(type) {
        //console.log(type);
        var html = '';
        switch (type) {
          case 'weekly':
            html = '<select id="fr-year" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 3px 6px 12px">'
                @for($y=2015;$y<2021;$y++)
                  +'<option value="{{$y}}" {{ $dr->fr->copy()->startOfWeek()->year==$y?'selected':'' }}>{{$y}}</option>'
                @endfor
              +' </select>'
              +'<select id="fr-week" class="btn btn-default dp-w-fr" style="height:34px; padding: 6px 0px 6px 12px">'
                @for($x=1;$x<=lastWeekOfYear($dr->fr->copy()->startOfWeek()->year);$x++)
                +'<option value="{{$x}}" {{ $dr->fr->copy()->startOfWeek()->weekOfYear==$x?'selected':'' }}>{{$x}}</option>'
                @endfor
              +'</select>'
              +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
              +'<select id="to-year" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 3px 6px 12px">'
                @for($y=2015;$y<2021;$y++)
                  +'<option value="{{$y}}" {{ $dr->to->copy()->endOfWeek()->year==$y?'selected':'' }}>{{$y}}</option>'
                @endfor
              +'</select>'
              +'<select id="to-week" class="btn btn-default dp-w-to" style="height:34px; padding: 6px 0px 6px 12px">'
                @for($x=1;$x<=lastWeekOfYear($dr->to->copy()->endOfWeek()->year);$x++)
                  +'<option value="{{$x}}" {{ $dr->to->copy()->endOfWeek()->weekOfYear==$x?'selected':'' }}>{{$x}}</option>'
                @endfor
              +'</select>';
              $('#dp-form').prop('action', '/analytics/week');
            break;
          case 'monthly':
            html = '<label class="btn btn-default" for="dp-m-date-fr">'
              +'<span class="glyphicon glyphicon-calendar"></span>'
              +'</label>'
              +'<input readonly type="text" class="btn btn-default dp" id="dp-m-date-fr" value="{{ $dr->fr->format('m/Y') }}" style="max-width: 110px;">'
              +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
              +'<input readonly type="text" class="btn btn-default dp" id="dp-m-date-to" value="{{ $dr->to->format('m/Y') }}" style="max-width: 110px;">'
              +'<label class="btn btn-default" for="dp-m-date-to">'
              +'<span class="glyphicon glyphicon-calendar"></span>'
              +'</label>';
              $('#dp-form').prop('action', '/analytics/month');
            break;
          case 'quarterly':
            html = '<select id="fr-y" class="btn btn-default dp-q-fr" style="height:34px; padding: 6px 3px 6px 12px">'
              @for($y=2015;$y<2021;$y++)
                +'<option value="{{$y}}" {{ $dr->fr->year==$y?'selected':'' }}>{{$y}}</option>'
              @endfor
            +'</select>'
            +'<select id="fr-q" class="btn btn-default dp-q-fr" style="height:34px; padding: 6px 0px 6px 12px">'
              @for($x=0;$x<4;$x++)
              +'<option value="{{pad(($x*3)+1)}}-01" {{ $dr->fr->quarter==$x+1?'selected':'' }}>{{$x+1}}</option>'
              @endfor
            +'</select>'
            +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
            +'<select id="to-y" class="btn btn-default dp-q-to" style="height:34px; padding: 6px 3px 6px 12px">'
              @for($y=2015;$y<2021;$y++)
                +'<option value="{{$y}}" {{ $dr->to->year==$y?'selected':'' }}>{{$y}}</option>'
              @endfor
            +'</select>'
            +'<select id="to-q" class="btn btn-default dp-q-to" style="height:34px; padding: 6px 0px 6px 12px">'
              @for($x=0;$x<4;$x++)
                +'<option value="{{pad(($x*3)+1)}}-01" {{ $dr->to->quarter==$x+1?'selected':'' }}>{{$x+1}}</option>'
              @endfor
            +'</select>';
              $('#dp-form').prop('action', '/analytics/quarter');
            break;
          case 'yearly':
            html = '<label class="btn btn-default" for="dp-y-date-fr">'
              +'<span class="glyphicon glyphicon-calendar"></span></label>'
              +'<input readonly type="text" class="btn btn-default dp" id="dp-y-date-fr" value="{{ $dr->fr->format('Y') }}" style="max-width: 110px;">'
              +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
              +'<input readonly type="text" class="btn btn-default dp" id="dp-y-date-to" value="{{ $dr->to->format('Y') }}" style="max-width: 110px;">'
              +'<label class="btn btn-default" for="dp-y-date-to">'
              +'<span class="glyphicon glyphicon-calendar"></span>'
              +'</label>';
            $('#dp-form').prop('action', '/analytics/year');
            break;
          default:
            html = '<label class="btn btn-default" for="dp-date-fr">'
              +'<span class="glyphicon glyphicon-calendar"></span>'
              +'</label>'
              +'<input readonly type="text" class="btn btn-default dp" id="dp-date-fr" value="{{ $dr->fr->format('m/d/Y') }}" style="max-width: 110px;">'
              +'<div class="btn btn-default" style="pointer-events: none;">-</div>'
              +'<input readonly type="text" class="btn btn-default dp" id="dp-date-to" value="{{ $dr->to->format('m/d/Y') }}" style="max-width: 110px;">'
              +'<label class="btn btn-default" for="dp-date-to">'
              +'<span class="glyphicon glyphicon-calendar"></span>'
              +'</label>';
            $('#dp-form').prop('action', '/analytics');
        }

        return html;
      }

   
  });
</script>
<style type="text/css">
  .show.less {
      max-height: 310px;
      overflow: hidden;
  }

  .show.toggle {
    display: inline;
  }
  </style>
@endsection