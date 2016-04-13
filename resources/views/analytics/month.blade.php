@extends('index')

@section('title', '- By Month Analytics')

@section('body-class', 'analytics-month')

@section('container-body')
<div class="container-fluid">
	<ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/dashboard">{{ $branch }}</a></li>
    <li>Analytics</li>
    <li class="active">By Month</li>
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
            {!! Form::open(['url' => '/analytics', 'method' => 'get']) !!}
            <button type="submit" class="btn btn-success btn-go" title="Go"   }}>
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->fr->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

          <div class="btn-group" role="group">
            <a href="/analytics?fr={{$dr->now->copy()->startOfMonth()->format('Y-m-d')}}&to={{$dr->now->format('Y-m-d')}}" class="btn btn-default" title="Back to Main Menu">
              <span class="fa fa-calendar-o"></span>
              <span class="hidden-xs hidden-sm">Daily</span>
            </a>
            <button class="btn btn-default active">
              <span class="fa fa-calendar"></span>
              <span class="hidden-xs hidden-sm">Monthly</span>
            </button> 
          </div> <!-- end btn-grp -->

          <div class="btn-group pull-right clearfix" role="group">
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
                  <th>Month</th>
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
              @foreach($dailysales as $d)
              <tr>
                <td data-sort="{{$d->date->format('Y-m-d')}}">{{ $d->date->format('M Y') }}</td>
                @if(!is_null($d->dailysale))
                <td class="text-right" data-sort="{{ number_format($d->dailysale['sales'], 2,'.','') }}">
                  {{ number_format($d->dailysale['sales'], 2) }}
                </td>
                <td class="text-right" data-sort="{{ number_format($d->dailysale['purchcost'], 2,'.','') }}">
                    {{ number_format($d->dailysale['purchcost'], 2) }}
                  @if($d->dailysale['purchcost']==0) 
                    
                  @else
                  <!--
                  <a href="#" data-date="{{ $d->date->format('Y-m-d') }}" class="text-primary btn-purch">
                    {{ number_format($d->dailysale['purchcost'], 2) }}
                  </a>
                  -->
                  @endif
                </td>
                <td class="text-right" data-sort="{{ number_format($d->dailysale['custcount'], 0) }}">
                  {{ number_format($d->dailysale['custcount'], 0) }}
                </td>
                <!--- head speand -->
                @if($d->dailysale['custcount']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <td class="text-right" data-sort="{{ number_format($d->dailysale['sales']/$d->dailysale['custcount'], 2,'.','') }}">
                    {{ number_format($d->dailysale['sales']/$d->dailysale['custcount'], 2) }}
                  </td>
                @endif
                <!--- end: head speand -->
                <td class="text-right" data-sort="{{ $d->dailysale['empcount'] }}">
                  {{ number_format($d->dailysale['empcount'], 0) }}
                </td>
                <!--- sales per emp -->
                @if($d->dailysale['empcount']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <td class="text-right" data-sort="{{ number_format($d->dailysale['sales']/$d->dailysale['empcount'], 2,'.','') }}">
                    {{ number_format($d->dailysale['sales']/$d->dailysale['empcount'], 2) }}
                  </td>
                @endif
                <!--- end: sales per emp -->
                <?php
                  $mancost = $d->dailysale['empcount']*session('user.branchmancost');
                ?>
                <td class="text-right" data-sort="{{ number_format($mancost,2,'.','') }}">
                  {{ number_format($mancost,2) }}
                </td>
                <!--- mancostpct -->
                @if($d->dailysale['sales']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <?php
                    $mancostpct = (($d->dailysale['empcount']*session('user.branchmancost'))/$d->dailysale['sales'])*100;
                  ?>
                  <td class="text-right" data-sort="{{ number_format($mancostpct, 2,'.','') }}"
                    title="(({{$d->dailysale['empcount']}}*{{session('user.branchmancost')}}/{{$d->dailysale['sales']}})*100 ={{$mancostpct}}"
                  >
                    {{ number_format($mancostpct, 2) }}
                  </td>
                @endif
                <!--- end: mancostpct -->
                <td class="text-right" data-sort="{{ number_format($d->dailysale['tips'],2,'.','') }}">
                  {{ number_format($d->dailysale['tips'],2) }}
                </td>
                <!--- sales per emp -->
                @if($d->dailysale['sales']==0)
                  <td class="text-right" data-sort="0.00">
                    -
                  </td>
                @else
                  <td class="text-right" data-sort="{{ number_format(($d->dailysale['tips']/$d->dailysale['sales'])*100, 2,'.','') }}">
                    {{ number_format(($d->dailysale['tips']/$d->dailysale['sales'])*100, 2) }}
                  </td>
                @endif
                <!--- end: sales per emp -->

                @else <!-- is_null d->dailysale) -->
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                <td class="text-right" data-sort="0.00">-</td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>

        <table id="datatable" class="tb-data" style="display:none;">
          <thead>
            <tr>
                <th>Date</th>
                <th>Sales</th>
                <th>Purchased</th>
                <th>Tips</th>
                <th>Man Cost</th>
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
              <td>{{ $d->dailysale['tips'] }}</td>
              <td>{{ ($d->dailysale['empcount']*session('user.branchmancost')) }}</td>
              <td>{{ $d->dailysale['empcount']=='0' ? 0:number_format(($d->dailysale['sales']/$d->dailysale['empcount']), 2, '.', '') }}</td>
              @else 
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





@endsection




@section('js-external')
  @parent
  @include('_partials.js-vendor-highcharts')
  
<script>
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


  $('document').ready(function(){

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

    


    $('.btn-purch-').on('click', function(e){
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
              return Highcharts.dateFormat('%b %Y', this.value);
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
              /*
              arr.push({ // mark the weekend
                color: "#CCCCCC",
                width: 1,
                value: this.value-86400000,
                zIndex: 3
              });
*/
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
      }
    });



    $('#h-tot-sales').text($('#f-tot-sales').text());
    $('#h-tot-purch').text($('#f-tot-purch').text());
    $('#h-tot-mancost').text($('#f-tot-mancost').text());
    $('#h-tot-tips').text($('#f-tot-tips').text());

   
  });
</script>
@endsection