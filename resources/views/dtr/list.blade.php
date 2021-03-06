@extends('index')

@section('title', '- DTR List')

@section('body-class', 'list-dtr')

<?php
  $prev = $date->copy()->subMonth();
  $next = $date->copy()->addMonth();
?>

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/dtr/{{$date->format('Y')}}">DTR {{$date->format('Y')}}</a></li>
    <li class="active">{{$date->format('M')}}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dtr" class="btn btn-default" title="Back to Main Menu">
              <span class="glyphicon glyphicon-th-list"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="gly gly-table"></span>
            </button>
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <a href="/dtr/generate" class="btn btn-default" title="Generate">
              <span class="gly gly-history"></span>
            </a>
          </div> <!-- end btn-grp -->

          <div class="btn-group pull-right" role="group">
            <a href="/dtr/{{$prev->format('Y')}}/{{$prev->format('m')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            <a href="/dtr/{{$next->format('Y')}}/{{$next->format('m')}}" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    <h3>DTR Summary of {{ $dtrs[0]['date']->format('F') }}</h3>
    <p><em>Summary of DTR and Mansked count of employees each day.</em></p>

    @include('_partials.alerts')

    @if(count($dtrs)>0)
      <table id="tb-dtr" class="table table-bordered table-responsive">
      <thead>
        <tr>
          <th class="bg-default">Date</th>
          <th class="bg-default">Daily Time Record</th>
          <th class="bg-default">Mansked</th></tr>
      </thead>
      <tbody>
      @foreach($dtrs as $dtr)
        <tr>
          <td class="td-date">
            <span class="day">
            @if(count($dtr['dtrs'])>0)
              <a href="/dtr/{{$dtr['date']->year}}/{{pad($dtr['date']->month)}}/{{pad($dtr['date']->day)}}">{{ $dtr['date']->format('M d, Y D') }}</a>
            @else
              {{ $dtr['date']->format('M d, Y D') }} 
            @endif
          </span>

            <button class="btn btn-success pull-right btn-generate" type="button" data-date="{{ $dtr['date']->format('Y-m-d') }}" 
              title="process {{ $dtr['date']->format('M d, Y D') }}">
              <span class="gly gly-history"></span>
            </button>
          </td>
          <td class="td-dtr">
            @if(count($dtr['dtrs'])>0)
              <a href="/dtr/{{$dtr['date']->year}}/{{pad($dtr['date']->month)}}/{{pad($dtr['date']->day)}}">{{ count($dtr['dtrs']) }}</a>
            @else
              -
            @endif
          </td>
          <td class="td-mansked">
            @if(count($dtr['mandtls'])>0)
              <a href="/task/manday/{{ strtolower($dtr['mandtls'][0]->mandayid) }}" target="_blank">{{ count($dtr['mandtls']) }}</a>
            @else
              -
            @endif
          </td>
          
        </tr>
      @endforeach
      </tbody>
      </table>
    @else 
      no record(s) found!
    @endif

   
   

    
      
  
  </div>
</div><!-- end container-fluid -->
@endsection


@section('js-external')
  @parent
  <script>
    var processDtr = function(formData) {

      return $.ajax({
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded',
        url: '/dtr/generate',
        dataType: "json",
        //async: false,
        data: formData,
        beforeSend: function(jqXHR, obj) {
          console.log(obj.data);
        }
      });
      
    }

    var processBtnDone = function(btn){
      $('.alert').remove();
      var b = btn || $('.btn-generate.processing');
      b.children('span')
      .removeClass('glyphicon')
      .removeClass('glyphicon-refresh')
      .removeClass('rotate')
      .addClass('gly')
      .addClass('gly-history');
      b.removeClass('processing');
    }


    $(document).ready(function(){

      $('.btn-generate').on('click', function(e){
        e.preventDefault();

        console.log($('.btn-generate').hasClass('processing'));
        var parent = $(this).parent().parent();
        

        if(!$('.btn-generate').hasClass('processing')){
          console.log('start processing...');
          $(this).children('span')
          .removeClass('gly')
          .removeClass('gly-history')
          .addClass('glyphicon')
          .addClass('glyphicon-refresh')
          .addClass('rotate');
          
          $(this).addClass('processing');
          var formData = {};
          formData.fr = $(this).data('date');
          formData.to = $(this).data('date');
          processDtr(formData)
          .done(function(data, textStatus, jqXHR){
            console.log(data);
            if(data.status==='error' && data.code==='401') {
              window.location.href = '/';
            } else {
              var date = $('.btn-generate.processing').data('date').split('-');
              processBtnDone();
              var link1 = '<a href="/dtr/'+date[0]+'/'+date[1]+'/'+date[2]+'">'+moment(date[0]+' '+date[1]+' '+date[2], 'YYYY MM DD').format("MMM DD, YYYY ddd");+'</a>';
              parent.find('.td-date').stop( true, true ).effect("highlight", {}, 2000).children('.day').html(link1);
              var link2 = '<a href="/dtr/'+date[0]+'/'+date[1]+'/'+date[2]+'">'+data.count+'</a>';
              parent.find('.td-dtr').html(link2).stop( true, true ).effect("highlight", {}, 2000);
            }
          })
          .fail(function(jqXHR, textStatus, errorThrown){
            var html = '<div class="alert alert-danger" role="alert">';
            html += '<button type="button" class="close" data-dismiss="alert" aria-label="Close">';
            html += '<span aria-hidden="true">×</span>';
            html += '</button>';
            html += '<ul>';
            html += '<li>Error! contact system administrator...</li>';
            html += '</ul>';
            html += '</div>';

            $('.alert').remove();
            $('#nav-action').after(html);
            console.log(textStatus);
            console.log(errorThrown);
            console.log(jqXHR);
            
            processBtnDone();
          }); 
          /*
          setTimeout(function(){
            var b = $('.btn-generate.processing');
            b.children('span')
            .removeClass('glyphicon')
            .removeClass('glyphicon-refresh')
            .removeClass('rotate')
            .addClass('gly')
            .addClass('gly-history');
            b.removeClass('processing');
          }, 5000);
        */

        } else {
          console.log('busy processing...');
        }

      });
    });

  </script>
@endsection
