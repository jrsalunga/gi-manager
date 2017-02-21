<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="gridSystemModalLabel">Sales <small>{{$data['ds']->date->format('D M d, Y')}}</small></h4>
</div>
<div class="modal-body">
  <div class="row">
    @if(is_null($data['sales']))

    @else
    <div class="col-md-12">
      <!-- Nav tabs -->
      <ul class="nav nav-pills" role="tablist">
        <li role="presentation" class="active">
          <a href="#stat" aria-controls="stat" role="tab" data-toggle="tab">
            <span class="gly gly-charts"></span>
            <span class="hidden-xs hidden-sm">
              Stats
            </span>
          </a>
        </li>
        <li role="presentation">
          <a href="#products" aria-controls="products" role="tab" data-toggle="tab">
            <span class="gly gly-cutlery"></span>
              <span class="hidden-xs hidden-sm">
                Sales
              </span>
          </a>
        </li>
        <li role="presentation" style="float: right;">
          <div>
            Gross Sales: 
            <h3 id="tot-sales-cost" class="text-right" style="margin:0 0 10px 0;">{{number_format($data['ds']->slsmtd_totgrs,2)}}</h3>
            <div class="diff text-right" style="font-size:12px; margin-top:-10px;">
            <?php
              $v = '';
              if (!is_null($data['sales'])) {

                $diff =$data['ds']->slsmtd_totgrs-$data['ds']->sales;
                $c = $diff>0 ? 'success':'danger';
                $d = $diff>0 ? 'up':'down';

                $v = '<span class="text-'.$c.'"><span class="glyphicon glyphicon-arrow-'.$d.'"></span><b> '.number_format($diff,2).'</b></span>';
              } 
              ?>
            
              {!!$v!!}
            </div>
          </div>
        </li>
        <li role="presentation" style="float: right;margin-right:20px;">
          <div>
            Net Sales: 
          <h3 id="tot-salesmtd-cost" class="text-right" style="margin:0 0 20px 0;">{{number_format($data['ds']->sales,2)}}</h3>
          </div>
        </li>
      </ul>
    </div>

    <div class="col-md-12">
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="products">
          <div class="table-responsive">
            <table class="tb-sales-data table table-condensed table-hover table-striped table-sort tablesorter tablesorter-default" role="grid">
              <thead>
                <tr>
                  <th>Order Time</th>
                  <th>Slip No</th>
                  <th>Product</th>
                  <th class="text-right">Qty</th>
                  <th class="text-right">Price</th>
                  <th class="text-right">Gross</th>
                  <th>Category</th>
                  <th>Menu Category</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $last_slip = 1;
                ?>
                @foreach($data['sales'] as $sale)
                  <?php
                    if ($last_slip!=$sale->cslipno) {
                      $last_slip = $sale->cslipno;
                      //$color = rand_color();
                      $color = sprintf("#%06x",rand(0,16777215));
                    }
                  ?>
                  <tr>
                    <td title="{{ $sale->ordtime->format('D M j, Y h:i A') }}">
                      <small class="text-muted">{{ $sale->ordtime->format('h:i A') }}</small>
                    </td>
                    <td><small class="text-muted" style="color: {{$color}};">{{ $sale->cslipno }}</small></td>
                    <td>{{ $sale->product }} <small><span class="label label-primary">{{ $sale->group }}</span></small></td>
                    <td class="text-right"><small class="text-muted">{{ number_format($sale->qty, 2)+0 }}</small></td>
                    <td class="text-right"><small class="text-muted">{{ number_format($sale->uprice,2) }}</small></td>
                    <td class="text-right">{{ number_format($sale->grsamt,2) }}</td>
                    <td><small class="text-muted">{{ $sale->prodcat }}</small></td>
                    <td><small class="text-muted">{{ $sale->menucat }}</small></td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        </div>
        <div role="tabpanel" class="tab-pane active" id="stat">

          @if(is_null($data['sales']))

          @else

          <!-- Product Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Product Sales Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-product-sale" data-table="#product-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-product-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th class="text-right">Quantity</th>
                              <th class="text-right">Amount</th>
                              <th>Category</th>
                              <th>Menu Category</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $prodtot=0; ?> 
                            @foreach($data['products'] as $product) 
                              <tr>
                                <td>{{ $product->product }}</td>
                                <td class="text-right"><small class="text-muted">{{ number_format($product->qty, 0) }}</small></td>
                                <td class="text-right">{{ number_format($product->netamt, 2) }}</td>
                                <td><small class="text-muted">{{ $product->prodcat }}</small></td>
                                <td><small class="text-muted">{{ $product->menucat }}</small></td>
                              </tr>
                            <?php $prodtot+=$product->netamt; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($prodtot,2)}}</b></td><td></td><td></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;display: inline;">show more</span>
                      
                      <table id="product-sale-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['products'] as $product)
                              <tr>
                                <td>{{ $product->product }}</td>
                                <td>{{ $product->netamt }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Prodcat Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Product Category Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-prodcat-sale" data-table="#prodcat-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div>
                        <table class="tb-prodcat-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Product Category</th>
                              <th class="text-right"></th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['prodcats'] as $prodcat)
                              <tr>
                                <td>{{ $prodcat->prodcat }}</td>
                                <td class="text-right"></td>
                                <td class="text-right">{{ number_format($prodcat->netamt, 2) }}</td>
                              </tr>
                            <?php $t+=$prodcat->netamt; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      
                      <table id="prodcat-sale-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Product</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['prodcats'] as $prodcat)
                              <tr>
                                <td>{{ $prodcat->prodcat }}</td>
                                <td>{{ $prodcat->netamt }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Menucat Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Menu Category Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-menucat-sale" data-table="#menucat-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-menucat-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Menu Category</th>
                              <th class="text-right"></th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['menucats'] as $menucat)
                              <tr>
                                <td>{{ $menucat->menucat }}</td>
                                <td class="text-right"></td>
                                <td class="text-right">{{ number_format($menucat->netamt, 2) }}</td>
                              </tr>
                            <?php $t+=$menucat->netamt; ?>
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;display: inline;">show more</span>

                      <table id="menucat-sale-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>menucat</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['menucats'] as $menucat)
                              <tr>
                                <td>{{ $menucat->menucat }}</td>
                                <td>{{ $menucat->netamt }}</td>
                              </tr>
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Groupies Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Groupies Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-groupies" data-table="#groupies-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div>
                        <table class="tb-groupies-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Groupies</th>
                              <th>Qty</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['groupies'] as $key => $groupie)
                              <tr>
                                <td>{{ $key }}</td>
                                <td>{{ number_format($groupie['qty'], 2)+0 }}</td>
                                <td class="text-right">{{ number_format($groupie['grsamt'], 2) }}</td>
                              </tr>
                            <?php $t+=$groupie['grsamt']; ?>
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      
                      <table id="groupies-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Group</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php $t=0; ?>
                            @foreach($data['groupies'] as $groupie)
                              <tr>
                                <td>{{ $groupie['group'] }}</td>
                                <td>{{ $groupie['grsamt'] }}</td>
                              </tr>
                            <?php $t+=$groupie['grsamt']; ?>
                            @endforeach
                              <tr>
                                <td>Sales of not Groupies</td>
                                <td>{{ $data['ds']->sales-$t }}</td>
                              </tr>
                          </tbody>
                        </table>
                      
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          @endif
        </div><!-- end: #stats -->
      </div>
    </div>
    @endif
  </div>
</div> <!-- end: .modal-body -->
<div class="modal-footer">
  <button type="button" id="mdl-btn-discard" class="btn btn-link pull-right" data-dismiss="modal" tabindex="-1">Cancel</button>
</div>


