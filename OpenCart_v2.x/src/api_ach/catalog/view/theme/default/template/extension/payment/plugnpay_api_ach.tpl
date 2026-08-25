<form id="payment" class="form-horizontal">
  <fieldset>
    <legend><?php echo $text_ach_echeck; ?></legend>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-ach-owner"><?php echo $entry_owner; ?></label>
      <div class="col-sm-10">
        <input type="text" name="ach_owner" value="" placeholder="<?php echo $entry_owner; ?>" id="input-ach-owner" class="form-control" />
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-ach-accttype"><?php echo $entry_accttype; ?></label>
      <div class="col-sm-3">
        <select name="ach_accttype" id="input-ach-accttype" class="form-control">
          <option value=""> </option>
          <option value="checking"><?php echo $entry_accttype_checking; ?></option>
          <option value="savings"><?php echo $entry_accttype_savings; ?></option>
        </select>
       </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-ach-acctclass"><?php echo $entry_acctclass; ?></label>
      <div class="col-sm-3">
        <select name="ach_acctclass" id="input-ach-acctclass" class="form-control">
          <option value=""> </option>
          <option value="personal"><?php echo $entry_acctclass_personal; ?></option>
          <option value="business"><?php echo $entry_acctclass_business; ?></option>
        </select>
       </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-ach-routingnum"><?php echo $entry_routingnum; ?></label>
      <div class="col-sm-10">
        <input type="text" name="ach_routingnum" value="" placeholder="<?php echo $entry_routingnum; ?>" id="input-ach-routingnum" class="form-control" maxlength="9"/>
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-ach-accountnum"><?php echo $entry_accountnum; ?></label>
      <div class="col-sm-10">
        <input type="text" name="ach_accountnum" value="" placeholder="<?php echo $entry_accountnum; ?>" id="input-ach-accountnum" class="form-control" maxlength="19"/>
      </div>
    </div>
    <div class="form-group required">
      <label class="col-sm-2 control-label" for="input-ach-checknum"><?php echo $entry_checknum; ?></label>
      <div class="col-sm-10">
        <input type="text" name="ach_checknum" value="" placeholder="<?php echo $entry_checknum; ?>" id="input-ach-checknum" class="form-control" maxlength="19"/>
      </div>
    </div>
  </fieldset>
</form>
<div class="buttons">
  <div class="pull-right">
    <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
  </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/payment/plugnpay_api_ach/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',
		cache: false,
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}

			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
//--></script>
