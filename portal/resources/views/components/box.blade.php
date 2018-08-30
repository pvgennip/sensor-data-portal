<div class="box">
  <div class="box-header">
    <h3 class="box-title">{{ $title or '' }}</h3>
    <div class="pull-right">{{ $action or '' }}</div>
  </div>
  <!-- /.box-header -->
  <div class="box-body {{ $bodyClass or 'table-responsive' }}">
    {{ $body }}
  </div>
  <!-- /.box-body -->
</div>