<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title" id="gridSystemModalLabel">Purchased <small>{{$data['ds']->date->format('D M d, Y')}}</small></h4>
</div>
<div class="modal-body">
  <div class="row">
    @if(is_null($data['purchases']))

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
          <a href="#purchases" aria-controls="purchases" role="tab" data-toggle="tab">
            <span class="gly gly-shopping-cart"></span>
              <span class="hidden-xs hidden-sm">
                Components
              </span>
          </a>
        </li>
        <li role="presentation" style="float: right;margin-right:20px;">
          <div>
            Total Purchased Cost: 
          <h3 id="tot-salesmtd-cost" class="text-right" style="margin:0 0 20px 0;">{{number_format($data['ds']->purchcost,2)}}</h3>
          </div>
        </li>
      </ul>
    </div>

    <div class="col-md-12">
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="purchases">
          <div class="table-responsive">
            <table class="tb-sales-data table table-condensed table-hover table-striped table-sort tablesorter tablesorter-default" role="grid">
              <thead>
                <tr>
                  <th>Component</th>
                  <th>UoM</th>
                  <th class="text-right">Qty</th>
                  <th class="text-right">Unit Cost</th>
                  <th class="text-right">Total Cost</th>
                  <th>Category</th>
                  <th>Supplier</th>
                  <th>Terms</th>
                  <th class="text-right">Vat</th>
                </tr>
              </thead>
              <tbody>
                @foreach($data['purchases'] as $purchase)
                  <tr>
                    <td>{{ $purchase->component }}</td>
                    <td><small class="text-muted">{{ $purchase->uom }}</small></td>
                    <td class="text-right"><small class="text-muted">{{ number_format($purchase->qty, 2)+0 }}</small></td>
                    <td class="text-right"><small class="text-muted">{{ number_format($purchase->ucost,2) }}</small></td>
                    <td class="text-right">{{ number_format($purchase->tcost,2) }}</td>
                    <td><small class="text-muted">{{ $purchase->compcatcode }}</small></td>
                    <td><small class="text-muted">{{ $purchase->suppliercode }}</small></td>
                    <td><small class="text-muted">{{ $purchase->terms }}</small></td>
                    <td class="text-right"><small class="text-muted">{{ number_format($purchase->vat,2) }}</small></td>
                  </tr>
                @endforeach
              </tbody>
            </table>

          </div>
        </div>
        <div role="tabpanel" class="tab-pane active" id="stat">

          @if(is_null($data['purchases']))

          @else

          <!-- Component Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Component Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-component-sale" data-table="#component-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-component-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Component</th>
                              <th class="text-right">Quantity</th>
                              <th class="text-right">Tran Count</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['components'] as $component)
                              <tr>
                                <td>{{ $component->component }}</td>
                                <td class="text-right">{{  number_format($component->qty,2)+0 }}</td>
                                <td class="text-right">{{ $component->tran_cnt }}</td>
                                <td class="text-right">{{ number_format($component->tcost,2) }}</td>
                              </tr>
                            <?php $t+=$component->tcost; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;display: inline;">show more</span>
                      
                      <table id="component-purch-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>component</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['components'] as $component)
                              @if($component->tcost>0)
                              <tr>
                                <td>{{ $component->component }}</td>
                                <td>{{ $component->tcost }}</td>
                              </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

           <!-- Compcat Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Component Category Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-compcat-sale" data-table="#compcat-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-compcat-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Component Category</th>
                              <th class="text-right"></th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['compcats'] as $compcat)
                              <tr>
                                <td>{{ $compcat->compcat }}</td>
                                <td class="text-right"></td>
                                <td class="text-right">{{ number_format($compcat->tcost, 2) }}</td>
                              </tr>
                            <?php $t+=$compcat->tcost; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;display: inline;">show more</span>
                      
                      <table id="compcat-purch-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Compcat</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['compcats'] as $compcat)
                              @if($compcat->tcost>0)
                              <tr>
                                <td>{{ $compcat->compcat }}</td>
                                <td>{{ $compcat->tcost }}</td>
                              </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Expense Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Expense Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-expense-sale" data-table="#expense-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-expense-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Code</th>
                              <th>Expense</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['expenses'] as $expense)
                              <tr>
                                <td>{{ $expense->expensecode }}</td>
                                <td>{{ $expense->expense }}</td>
                                <td class="text-right">{{ number_format($expense->tcost, 2) }}</td>
                              </tr>
                            <?php $t+=$expense->tcost; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;display: inline;">show more</span>
                      
                      <table id="expense-purch-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Compcat</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['expenses'] as $expense)
                              @if($expense->tcost>0)
                              <tr>
                                <td>{{ $expense->expense }}</td>
                                <td>{{ $expense->tcost }}</td>
                              </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Expense Category Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Expense Category Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-expscat-sale" data-table="#expscat-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div>
                        <table class="tb-expscat-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Code</th>
                              <th>Expense Category</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['expscats'] as $expscat)
                              <tr>
                                <td>{{ $expscat->expscatcode }}</td>
                                <td>{{ $expscat->expscat }}</td>
                                <td class="text-right">{{ number_format($expscat->tcost, 2) }}</td>
                              </tr>
                            <?php $t+=$expscat->tcost; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      
                      <table id="expscat-purch-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Expense Category</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['expscats'] as $expscat)
                              @if($expscat->tcost>0)
                              <tr>
                                <td>{{ $expscat->expscat }}</td>
                                <td>{{ $expscat->tcost }}</td>
                              </tr>
                              @endif
                            @endforeach
                          </tbody>
                        </table>
                    </div><!-- end: .table-responsive -->
                  </div><!-- end: .row -->
                </div><!-- end: .col-md-7 -->
              </div><!-- end: .row -->
            </div>
          </div><!-- end: .panel.panel-default -->

          <!-- Expense Payments Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Payments/Terms</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-payment-sale" data-table="#payment-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div>
                        <table class="tb-payment-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Code</th>
                              <th>Mode of Payment</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['payments'] as $payment)
                              <tr>
                                <td>{{ $payment->terms }}</td>
                                <td>
                                  @if($payment->terms=='C')
                                    Cash
                                  @elseif($payment->terms=="K")
                                    Check
                                  @else
                                    -
                                  @endif
                                </td>
                                <td class="text-right">{{ number_format($payment->tcost, 2) }}</td>
                              </tr>
                            <?php $t+=$payment->tcost; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      
                      <table id="payment-purch-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Payment</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['payments'] as $payment)
                              <tr>
                                <td>
                                  @if($payment->terms=='C')
                                    Cash
                                  @elseif($payment->terms=="K")
                                    Check
                                  @else
                                    -
                                  @endif
                                </td>
                                <td>{{ $payment->tcost }}</td>
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

          <!-- Supplier Panel -->
          <div class="panel panel-default">
            <div class="panel-heading">Supplier Summary</div>
            <div class="panel-body">
              <div class="row">
                <div class="col-xs-12 col-md-5 col-md-push-7">
                  <div class="graph-container pull-right">
                    <div id="graph-pie-supplier-sale" data-table="#supplier-data"></div>
                  </div>
                </div><!-- end: .col-md-5 -->
                <div class="col-xs-12 col-md-7 col-md-pull-5">
                  <div class="row">
                    <div class="table-responsive">
                      <div class="show less">
                        <table class="tb-supplier-data table table-condensed table-hover table-striped">
                          <thead>
                            <tr>
                              <th>Code</th>
                              <th>Supplier</th>
                              <th class="text-right">Amount</th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $t=0; ?>
                            @foreach($data['suppliers'] as $supplier)
                              <tr>
                                <td>{{ $supplier->code }}</td>
                                <td>{{ $supplier->descriptor }}</td>
                                <td class="text-right">{{ number_format($supplier->tcost, 2) }}</td>
                              </tr>
                            <?php $t+=$supplier->tcost; ?> 
                            @endforeach
                          </tbody>
                          <tfoot><tr><td></td><td></td><td class="text-right"><b>{{number_format($t,2)}}</b></td></tr></tfoot>
                        </table>
                      </div>
                      <span class="label label-info show toggle" style="margin-left:3px;display: inline;">show more</span>
                      
                      <table id="supplier-purch-data" style="display:none;">
                          <thead>
                            <tr>
                              <th>Compcat</th>
                              <th>Total Cost</th>
                            </tr>
                          </thead>
                          <tbody>
                            @foreach($data['suppliers'] as $supplier)
                              @if($supplier->tcost>0)
                              <tr>
                                <td>{{ $supplier->descriptor }}</td>
                                <td>{{ $supplier->tcost }}</td>
                              </tr>
                              @endif
                            @endforeach
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


