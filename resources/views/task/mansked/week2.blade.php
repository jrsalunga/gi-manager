@extends('index')

@section('title', '- Create Daily Man Schedule')

@section('body-class', 'mansked-create')


@section('container-body')
<div class="container-fluid">

  <ol class="breadcrumb">
    <li><span class="gly gly-shop"></span> <a href="/">{{ $branch }}</a></li>
    <li><a href="/task/mansked">Manpower Schedule</a></li>
    <li class="active">Week {{$mansked->weekno}}</li>
  </ol>

  <div>
    <nav id="nav-action" class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-form">
          <div class="btn-group" role="group">
            <a href="/task/mansked" class="btn btn-default">
              <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a href="/task/mansked/week/{{$mansked->weekno}}" class="btn btn-default">
              <span class="gly gly-table"></span>
            </a>
            <button type="button" class="btn btn-default active">
              <span class="fa fa-calendar-o"></span>
            </button>   
          </div>
          <div class="btn-group" role="group">
            @if(strtotime($mansked->date) > strtotime('now'))
            <a href="/task/mansked/{{strtolower($mansked->id)}}/edit" class="btn btn-default">
              <span class="glyphicon glyphicon-edit"></span>
            </a>
            @else
            <button type="button" class="btn btn-default" disabled>
              <span class="glyphicon glyphicon-edit"></span>
            </button>
            @endif
          </div><!-- end btn-grp -->
          
          <div class="btn-group pull-right" role="group">
            @if($mansked->previousByField('weekno')==='false')
              <a href="/task/mansked/week/" class="btn btn-default disabled">
            @else
              <a href="/task/mansked/week/{{ strtolower($mansked->previousByField('weekno')->weekno) }}" class="btn btn-default">
            @endif
              <span class="glyphicon glyphicon-chevron-left"></span>
            </a>
            @if($mansked->nextByField('weekno')==='false')
              <a href="/task/mansked/week/" class="btn btn-default disabled">
            @else
              <a href="/task/mansked/week/{{ strtolower($mansked->nextByField('weekno')->weekno) }}" class="btn btn-default">
            @endif  
              <span class="glyphicon glyphicon-chevron-right"></span>
            </a>
          </div>
        </div>
      </div>
    </nav>

    @include('_partials.alerts')

    

    <table class="table table-bordered">
      <tbody>
        <tr>
          <td>Dept</td><td>Employee</td>
          @foreach($depts[0]['employees'][0]['manskeddays'] as $manday)

            <td>{{ $manday['date']->format('D, M d') }}</td>
          @endforeach
        
        </tr>
        <?php $ctr=1 ?>
        @foreach($depts as $dept)
          @for($i = 0; $i < count($dept['employees']); $i++)
            <tr>
              <td><?=strtolower($dept['name'])=='dining'?'DIN':'KIT';?></td>
              <td>{{ $ctr }}. {{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }} <span class="label label-default pull-right">{{ $dept['employees'][$i]->position->code }}</span></td>
              
                @foreach($dept['employees'][$i]['manskeddays'] as $manday)
                  @if(isset($manday['mandtl']['daytype']))
                    <td>
                      <div>
                        {{ date('g:i', strtotime($manday['mandtl']['timestart'])) }} - 
                        {{ date('g:i', strtotime($manday['mandtl']['timeend'])) }}
                      </div>
                      <div>{{ $manday['mandtl']['loading']=='0.00' ? '-':$manday['mandtl']['loading']+0 }}</div>
                    </td>
                  @else
                    <td>-</td>
                  @endif
                @endforeach
             
            </tr>
            <?php $ctr++ ?>
          @endfor
        @endforeach
      </tbody>
    </table>

    
    
      
  </div>


<!-- end main -->
</div>
@endsection




@section('js-external')
  @parent
  

<script>
  $('document').ready(function(){

   // $('#date').datepicker({'format':'yyyy-mm-dd'})

    $('select.form-control').on('change', function(e){
      //console.log(e);
      var x = ($(this)[0].value=='off') ? 0:1; 
     $(this).parent().children('.daytype').val(x);
    });



     $("#date").datepicker({ minDate: 1, dateFormat: 'yy-mm-dd',});
     $('.alert').not('.alert-important').delay(5000).slideUp(300);
  });
</script>
@endsection

