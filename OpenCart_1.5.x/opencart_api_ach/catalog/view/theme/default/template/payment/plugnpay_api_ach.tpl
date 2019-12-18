<h2><?php echo $text_ach_echeck; ?></h2>
<div class="content" id="payment">
  <table class="form">
    <tr>
      <td><?php echo $entry_ach_owner; ?></td>
      <td><input type="text" name="ach_owner" value="" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_ach_routingnum; ?></td>
      <td><input type="text" name="ach_routingnum" value="" size="9" maxlength="9" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_ach_accountnum; ?></td>
      <td><input type="text" name="ach_accountnum" value="" size="20" maxlength="20" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_ach_checknum; ?></td>
      <td><input type="text" name="ach_checknum" value="" size="10" maxlength="20" /></td>
    </tr>
    <tr>
      <td><?php echo $entry_ach_accttype; ?></td>
      <td><select name="ach_accttype">
          <?php foreach ($ach_accttype as $accttype) { ?>
          <option value="<?php echo $accttype['value']; ?>"><?php echo $accttype['text']; ?></option>
          <?php } ?>
        </select></td>
    </tr>
    <tr>
      <td><?php echo $entry_ach_acctclass; ?></td>
      <td><select name="ach_acctclass">
          <?php foreach ($ach_acctclass as $acctclass) { ?>
          <option value="<?php echo $acctclass['value']; ?>"><?php echo $acctclass['text']; ?></option>
          <?php } ?>
        </select></td>
    </tr>
  </table>
</div>
<div class="buttons">
  <div class="right"><input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="button" /></div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').bind('click', function() {
	$.ajax({
		url: 'index.php?route=payment/plugnpay_api_ach/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',		
		beforeSend: function() {
			$('#button-confirm').attr('disabled', true);
			$('#payment').before('<div class="attention"><img src="catalog/view/theme/default/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-confirm').attr('disabled', false);
			$('.attention').remove();
		},				
		success: function(json) {
			if (json['error']) {
				alert(json['error']);
			}
			
			if (json['success']) {
				location = json['success'];
			}
		}
	});
});
//--></script>
