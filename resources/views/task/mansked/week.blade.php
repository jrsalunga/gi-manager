@extends('index')

@section('title', '- Weekly Man Sched')

@section('body-class', 'mansked-week')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li class="active">Week {{ $mansked->weekno }}</li>
  </ol>

  <div>
    
   

    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/task/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="gly gly-table"></span>
            </button>
            <!--
            <a href="/masterfiles/employee/" class="btn btn-default">
              <span class="glyphicon glyphicon-file"></span>
            </a>   
          -->
          </div>
          <!--
          <div class="btn-group" role="group">
            <a href="/task/mansked/add" class="btn btn-default">
              <span class="glyphicon glyphicon-plus"></span>
            </a>
          </div>
        -->
      </div><!-- end btn-grp -->
      </div>
    </nav>


    <table class="table table-bordered">
      <tbody>
        <tr>
          <td rowspan="2">Week {{ $mansked->weekno }}</td>

          <td>Date</td>
          <td>Man Cost</td>
          <td>Notes</td>
        </tr>
        <tr>
          <td>{{ date('F j, Y', strtotime($mansked->date)) }}</td>
          <td></td>
          <td></td>
        </tr>
      </tbody>
    <table>

    <div class="graph" style="height: 250px">

    </div>

    <table class="table tb-mansked-week table-responsive">
      <tbody>
    <?php

    /*
    for($j=0; $j<=8; $j++){
      echo '<tr>';
      for($i=0; $i<=6; $i++){
          if($i==1 || $i==2 || $i==6)
            continue;
          else if($j==8)
            continue;
            //echo '<td>'. $mansked[$j-1]['created'] .'</td>';
          else 
            echo '<td>'. $mansked[$j][$i] .'</td>';
      }
      
      echo '</tr>';
    }
    */


    
    for($i=0; $i<=7; $i++){

      echo '<tr>';
      for($j=0; $j<=7; $j++){
          if($i==1 || $i==2 || $i==6)
            continue;
          else if($i==7 && $j!=0)
            if($manday[$j]['created']=='true')
              echo '<td class="text-center"><a class="btn btn-default" href="/task/manday/'.strtolower($manday[$j]['id']).'"><i class="fa fa-calendar-o"></i></a></td>';
            else
              continue;
              //echo '<td><a href="#">'. $manday[$j]['created'] .'</a></td>';
          else if($i==0 && $j!=0)
                echo '<td class="text-center">'. date('M j',strtotime($manday[$j][$i])) .'</td>';
          else if(($i==3 || $i==5) && $j!=0)
                echo '<td style="text-align: right">'. number_format($manday[$j][$i], 0) .'</td>';
          else if($i==4 && $j!=0)
                echo '<td style="text-align: right">'. number_format($manday[$j][$i], 2) .'</td>';
          else 
            echo '<td>'. $manday[$j][$i] .'</td>';
      }
      echo '</tr>';
    }
    
    ?>
      </tbody>
    </table>


    
      
  
</div>

<!-- end main -->
</div>
@endsection


@section('js-external')
  
 @parent
  
  @include('_partials.js-vendor-highcharts')

<script>
$(function () {

  $.get('/csv/2015/week/42', function (csv) {
    //console.log(csv);
    $('.graph').highcharts({
        chart: {
          type: 'line',
          style: {
            fontFamily: "Helvetica"
          }
        },
        data: {
                csv: csv
            },
        title: {
            text: null
        },
        yAxis: {
            title: {
                text: null
            }
        },
        legend: {
            align: 'left',
            verticalAlign: 'top',
            y: -5,
            x: 30,
            floating: true,
            borderWidth: 0
        },
        plotOptions: {
          series: {
            cursor: 'pointer',
            point: {
              events: {
                click: function (e) {
                  console.log(Highcharts.dateFormat('%Y-%m-%d', this.x));
                }
              }
            },
            marker: {
              lineWidth: 1,
              symbol: 'circle'
            }
          }
        }
      })
    });
});
</script>





  
@endsection


