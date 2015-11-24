@extends('index')

@section('title', '- DTR')

@section('body-class', 'branch-dtr')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">Daily Time Record</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/dashboard" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-unshare"></span>
            </a> 
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
            </button>
          </div> <!-- end btn-grp -->
          <div class="btn-group" role="group">
            <a href="/dtr/generate" class="btn btn-default" title="Back to Main Menu">
              <span class="gly gly-history"></span>
            </a>
          </div> <!-- end btn-grp -->
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    

    @if(count($dtrs)>0)
      <table id="tb-dtr-" class="table table-bordered table-responsive">
      <thead>
        <tr><th>Date</th><th>Daily Time Record</th><th>Mansked</th></tr>
      </thead>
      <tbody>
      @foreach($dtrs as $dtr)
        <tr>
          <td>{{ $dtr['date']->format('M d, Y D') }}</td>
          <td>
            @if(count($dtr['dtrs'])>0)
              <a href="/reports/dtr/{{ strtolower($dtr['date']->format('Y-m-d')) }}">{{ count($dtr['dtrs']) }}</a>
            @else
              -
            @endif
          </td>
          <td>
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
  

  </script>
@endsection
