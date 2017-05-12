<!DOCTYPE html>
<html>
<head>
	<title>Printer Friendly Version</title>
</head>

<style type="text/css">
table td.nbtl {
    border-top: 1px solid #fff;
    border-left: 1px solid #fff;
    border-bottom: 1px solid #fff;
}

.text-right {
	text-align: right;
}

.prn {
	cursor: pointer;
	padding: 6px 12px;
	border: 1px solid #ccc;
	margin: 10px;
	display: inline-block;
	border-radius: 4px;
	text-decoration: none;
	color: #000;
}

.prn:hover {
	color: #333;
  background-color: #d4d4d4;
  border-color: #8c8c8c;
}

@media print {
 table {
 
 	font-size: 13px;
 }

 table td {
	padding: 2px;
 }

 .prn {
 	display: none;
 }
}
</style>
<body onload="window.print()">

<a class="prn" href="javascript:window.print();">Print</a>

<h4 style="margin: 5px 0 0 0;">{{ session('user.branch') }} - {{ session('user.branchcode') }}</h4>
<h4 style="margin: 5px 0 10px 0;">Manpower Sked {{ $mansked->year }}</h4>
<table border="1" style="border-collapse: collapse;" cellpadding="5" cellspacing="0" >
	<tbody>
		<tr>
          <td colspan="2" class="nbtl">
            <h3 style="margin: 0;">{{ $mansked->year }} - Week {{ $mansked->weekno }}</h3>
          </td>
          @for($i=0;$i<7;$i++)
          <td>
          	<div style="text-align: center;">{{ $mansked->manskeddays[$i]->date->format('D') }}</div>
          	<div style="text-align: center;">{{ $mansked->manskeddays[$i]->date->format('d-M') }}</div>
          </td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Forecasted Customer</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->custCount() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Forecasted Ave Spending</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->headSpend() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Total Crew On-duty</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->empCount() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">Total Work Hours</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->workHrs() }}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="2" class="text-right nbtl">
            Manpower Cost %</td>
          @for($i=0;$i<7;$i++)
            <td class="text-right">
              <abbr title="Man Cost: &#8369 {{ $mansked->mancost }}">  
                {{ $mansked->manskeddays[$i]->computeMancost($mansked->mancost, true) }}
              </abbr>
            </td>
          @endfor
        </tr>
        <!--
        <tr>
          <td colspan="2" class="text-right nbtl">
            <abbr title="{{ session('user.branch') }} - &#8369 {{ session('user.branchmancost') }}/8">Work Hour Cost</abbr> %</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{{ $mansked->manskeddays[$i]->computeHourcost($mansked->mancost, true) }}</td>
          @endfor
        </tr>
      -->
        <tr>
          <td colspan="2" class="text-right nbtl">Loading</td>
          @for($i=0;$i<7;$i++)
          <td class="text-right">{!! $mansked->manskeddays[$i]->loadings() !!}</td>
          @endfor
        </tr>
        <tr>
          <td colspan="9" style="border-left: 1px solid #fff; border-right: 1px solid #fff; border-top: 1px solid #000;">&nbsp;</td>
        </tr>
		@foreach($depts as $dept)
			<tr>
         <td colspan="2" style="text-align: center;"><b>{{ $dept['name'] }}</b></td>
         <td colspan="7">&nbsp;</td>
      </tr>
			<?php 
				$ctr=1; 
				$arr = [];
				$z = [];
			?>
			@for($i = 0; $i < count($dept['employees']); $i++)
			
        <tr>
        	<!--
          <td>{{ strtoupper($dept['code']) }}</td>
          -->
          <td>{{ $dept['employees'][$i]->lastname }}, {{ $dept['employees'][$i]->firstname }}</td> 
          <td style="font-size: 12px;">{{ empty($dept['employees'][$i]->position->code) ? '':$dept['employees'][$i]->position->code }}</td>
          
            @foreach($dept['employees'][$i]['manskeddays'] as $k => $manday)
              <?php 
              	$arr[$k][$i]=0; 
              	$z[$k] = 0;
              ?>
              @if(!empty($manday['mandtl']['daytype']) && $manday['mandtl']['daytype']=='1')
              <?php 
              	$arr[$k][$i]++;
              	$z[$k]++;
              ?>
                <td>
                  <div style="text-align: center;">
                    {{ empty($manday['mandtl']['timestart']) ? '':date('g', strtotime($manday['mandtl']['timestart'])) }} 
                    <!--    
                    {{ empty($manday['mandtl']['timestart']) ? '':date('g:i', strtotime($manday['mandtl']['timestart'])) }} 
                    - 
                    {{ empty($manday['mandtl']['timeend']) ? '':date('g:i', strtotime($manday['mandtl']['timeend'])) }}
                  </div>
                  <div>
                    @if($manday['mandtl']['loading'] > 0)
                      <span class="label label-primary pull-right" style="letter-spacing: 2px;">+{{ $manday['mandtl']['loading']+0 }}</span>
                    @elseif($manday['mandtl']['loading'] < 0)
                      <span class="label label-danger pull-right" style="letter-spacing: 2px;">{{ $manday['mandtl']['loading']+0 }}</span>
                    @else
                        
                    @endif
                    -->
                  </div>
                </td>
              @else
                @if(!empty($manday['mandtl']['daytype']) && $manday['mandtl']['daytype']=='0')
                  <td>&nbsp;</td>
                @else
                  <td>{{ $manday['mandtl']['daytype'] }}</td>
                @endif
              @endif
            @endforeach
         
        </tr>
        <?php $ctr++ ?>
      @endfor
      <tr>
      	<td colspan="2" style="text-align: right;">Total Staff - {{ $dept['name'] }}</td>

      	@for($z=0; $z<count($arr); $z++)
      		<?php $ctr = 0; ?>
      		@for($y=0; $y<count($arr[$z]); $y++)
      			<?php $ctr += $arr[$z][$y]; ?>
					@endfor
            <td style="text-align: center;"><b><?=$ctr>0?$ctr:''?></b></td>
				@endfor
      </tr>
      <tr>
      	<td colspan="9">&nbsp;</td>
      </tr>

		@endforeach
	</tbody>
</table>


<script type="text/javascript">
	

</script>

</body>
</html>