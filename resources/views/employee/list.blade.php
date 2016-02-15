@extends('index')

@section('title', '- Employee')

@section('body-class', 'branch-employees')

@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li class="active">Employee</li>
  </ol>

  <div class="">
    

    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-default active">
              <span class="glyphicon glyphicon-th-list"></span>
            </button>
             
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

    

    <table class="table table-bordered" id="employee-table">
      <thead>
        <tr>
          <th>Lastname</th>
          <th>Firstname</th>
          <th>Middlename</th>
          <!--
          <th>Position</th>
          <th>Branch</th>
        -->
        </tr>
      </thead>
    </table>
      
  
</div>

<!-- end main -->
</div>
@endsection






@section('js-external')
  @parent
  <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>

  <script>
  $(function() {
      $('#employee-table').DataTable({
          processing: true,
          serverSide: true,
          ajax: 'api/dt/employee',
          columns: [
              { data: 'lastname', name: 'lastname' },
              { data: 'firstname', name: 'firstname' },
              { data: 'middlename', name: 'middlename' }
              /*,
              { data: 'position.descriptor', name: 'position', orderable: false, searchable: false },
              { data: 'branch.descriptor', name: 'branch', orderable: false, searchable: false }
              */
          ]
      });
  });
  </script>
@stop


