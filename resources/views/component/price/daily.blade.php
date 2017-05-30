@extends('master')

@section('title', '- Price Range ('.strtoupper(brcode()).')')

@section('body-class', 'daily-purchases')

@section('navbar-2')
<ul class="nav navbar-nav navbar-right"> 
  <li class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <span class="glyphicon glyphicon-menu-hamburger"></span>
    </a>
    <ul class="dropdown-menu">
      <li><a href="/settings"><span class="glyphicon glyphicon-cog"></span> Settings</a></li>
      <li><a href="/logout"><span class="glyphicon glyphicon-log-out"></span> Log Out</a></li>     
    </ul>
  </li>
</ul>
<p class="navbar-text navbar-right">{{ $name }}</p>
@endsection

@section('container-body')
<div class="backdrop"></div>
<div class="loader"><img src="/images/spinner_google.gif"></div>
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/dashboard">{{ $branch }}</a></li>
    <li><a href="/{{brcode()}}/component">Component</a></li>
    @if($branch)
    <li><a href="/{{brcode()}}/component/price/comparative">Price Range</a></li>
    <li class="active">{{ $dr->fr->format('M j, Y') }} - {{ $dr->to->format('M j, Y') }}</li>
    @else
    <li class="active">Purchases</li>
    @endif
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group pull-left" role="group">
          <?php
              $href = request()->has('back') && request()->has('back_fr') && request()->has('back_to')
                ? '/analytics/'.request()->input('back').'?fr='.request()->input('back_fr').'&to='.request()->input('back_to')
                :'/'.brcode().'/dashboard';
            ?>
            <a href="{{$href}}" class="btn btn-default" title="Back">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs">Back</span>
            </a> 
          </div> <!-- end btn-grp -->
          
          <div class="btn-group visible-xs-inline-block pull-right" role="group">
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#mdl-form">
              <span class="glyphicon glyphicon-option-vertical"></span>
            </button>
          </div>
          <!--
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
              <span class="hidden-xs hidden-sm">Back</span>
            </a> 
          </div> <!-- end btn-grp -->
          <?php $url=brcode().'/component/price/comparative'; ?>
          <div class="btn-group btn-group pull-right clearfix hidden-xs" role="group" style="margin-left: 5px;">
            {!! Form::open(['url' => $url, 'method' => 'get', 'id'=>'filter-form']) !!}
            
            <button type="submit" data-toggle="loader" class="btn btn-success btn-go" title="Go">
              <span class="gly gly-search"></span>
              <span class="hidden-xs hidden-sm">Go</span>
            </button> 
            <input type="hidden" name="table" id="table" value="{{ $filter->table }}">
            <input type="hidden" name="item" id="item" value="{{ $filter->item }}">
            <input type="hidden" name="itemid" id="itemid" value="{{ $filter->id }}">
            <input type="hidden" name="fr" id="fr" value="{{ $dr->fr->format('Y-m-d') }}" data-fr="{{ $dr->fr->format('Y-m-d') }}">
            <input type="hidden" name="to" id="to" value="{{ $dr->to->format('Y-m-d') }}" data-to="{{ $dr->to->format('Y-m-d') }}">
            {!! Form::close() !!}
          </div> <!-- end btn-grp -->

      
          <div class="btn-group pull-right clearfix dp-container hidden-xs" role="group">
            
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

          <div class="btn-group hidden-xs" role="group" style="margin-left: 5px;">
          	<input type="text" id="searchfield" class="form-control searchfield" value="{{ $filter->item }}" placeholder="Search Filter">
          </div>
          
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    @if($components)
    <div class="table-responsive">
      <table class="table table-hover table-striped table-sort">
        <thead>
          <tr>
            <th>Branch</th>
            <th class="text-right">Total Qty</th>
            <th class="text-right">Total Cost</th>
            <th class="text-right">Average Cost</th>
            <th class="text-right">Min-Max Cost</th>
            <th class="text-right">Min-Max Qty</th>
            <th class="text-right">Tran Cnt</th>
          </tr>
        </thead>
        <tbody>
        @foreach($components as $component)
          <tr class="<?=$component->code==strtoupper(brcode())?'bg-success':''?>">
            <td>
              @if($component->code==strtoupper(brcode()))
                <a href="/glv/component/purchases?table=component&item={{$filter->item}}&itemid={{$filter->id}}&fr={{$dr->fr->format('Y-m-d')}}&to={{$dr->to->format('Y-m-d')}}">
                  {{ $component->code }}
                </a>
              @else
                {{ $component->code }}
              @endif
            </td>
            <td class="text-right">{{ number_format($component->tot_qty, 0) }}</td>
            <td class="text-right">{{ number_format($component->tcost, 2) }}</td>
            <td class="text-right">{{ number_format($component->ave, 2) }}</td>
            <td class="text-right">{{ number_format($component->ucost_min, 2) }} - {{ number_format($component->ucost_max, 2) }}</td>
            <td class="text-right">{{ number_format($component->qty_min, 2) }} - {{ number_format($component->qty_max, 2) }}</td>
            <td class="text-right help" title="Negative transactions are not included">{{ number_format($component->trancnt, 0) }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @else
      No Data
    @endif



@endsection


@section('js-external')
  <script src="/js/vendors-common.min.js"></script>
  <script src="/js/hc-all.js"> </script>
  <script src="/js/dr-picker.js"> </script>
	
	<script>
  
  


    
  moment.locale('en', { week : {
    dow : 1 // Monday is the first day of the week.
  }});

  Highcharts.setOptions({
    lang: {
      thousandsSep: ','
  }});

  $(document).ready(function(){

  initDatePicker();
 
 

  
  $('.mdl-btn-go').on('click', function(){
    //loader();
    $('#filter-form').submit();
  });
  

  
    

    $('.show.toggle').on('click', function(){
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

    

    $.widget("custom.autocomplete", $.ui.autocomplete, {
      _create: function() {
        this._super();
        this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
      },
      _renderMenu: function(ul, items) {
        var that = this,
          currentCategory = "";
        $.each(items, function(index, item) {
          var li;
          if (item.category != currentCategory) {
            ul.append('<li class="ui-autocomplete-category"><span class="label label-success">' + item.category + '</span></li>' );
            currentCategory = item.category;
          }
          li = that._renderItemData(ul, item);
          if (item.category) {
            li.attr( "aria-label", item.category + " : " + item.label);
          }
        });
      }
    });


   	$(".searchfield").autocomplete({
     	source: function(request, response) {
      	$.ajax({
        	type: 'GET',
        	url: "/api/search/component",
          dataType: "json",
          data: {
            maxRows: 25,
            q: request.term
          },
          success: function(data) {
            response($.map(data, function(item) {
              return {
                //label: item.item + ', ' + item.table,
                label: item.item,
                value: item.item,
                category: item.table,
								id: item.id
              }
            }));
          }
        });
      },
      minLength: 2,
      select: function(event, ui) {
  			//console.log(ui);
        //log( ui.item ? "Selected: " + ui.item.label : "Nothing selected, input was " + this.value);
  			$("#table").val(ui.item.category); /* set the selected id */
  			$("#item").val(ui.item.value); /* set the selected id */
  			$("#itemid").val(ui.item.id); /* set the selected id */
      },
      open: function() {
        $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
  			$("#table").val(''); /* set the selected id */
  			$("#item").val(''); /* set the selected id */
  			$("#itemid").val(''); /* set the selected id */
      },
      close: function() {
          $( this ).removeClass("ui-corner-top").addClass("ui-corner-all");
      },
    	messages: {
      	noResults: '',
      	results: function() {}
    	}
    }).on('blur', function(e){
      if ($(this).val().length==0) {
        $( this ).removeClass("ui-corner-all").addClass("ui-corner-top");
        $("#table").val(''); /* set the selected id */
        $("#item").val(''); /* set the selected id */
        $("#itemid").val(''); /* set the selected id */
      }

      //setTimeout(submitForm, 1000);
    });


    var submitForm  = function(){
      console.log('submit Form');
      $('#filter-form').submit();
    }
 
  });

  </script>

@endsection