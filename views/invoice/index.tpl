<link media="screen" rel="stylesheet" href="/css/colorbox.css">
<script src="/js/jquery.colorbox.js"></script>
<script>  
  function update_preview() {
    var preview = '';
    
    if (jQuery('#InvoiceName').val().length == 0) {
      alert('Please enter a name for this invoice');
      return null;
    }
    
    jQuery('.ordertype input.ot').each(function() {
      preview += 'ordertypes['+jQuery(this).attr('rel')+']='+jQuery(this).attr('checked')+'&';
    });
    
    jQuery('.department input.dp').each(function() {
      preview += 'departments['+jQuery(this).attr('rel')+']='+jQuery(this).attr('checked')+'&';
    });
    
    var err = false;
    jQuery('.custom_charges .charge_name').each(function() {      
      preview += 'ccnames['+jQuery(this).attr('rel')+']='+jQuery(this).val()+'&';
    });
    
    jQuery('.custom_charges .charge_value').each(function() {
      if (jQuery('.cn'+jQuery(this).attr('rel')).val().length == 0 && jQuery(this).val().length > 0) {
        err = true;
      }
      preview += 'ccvalues['+jQuery(this).attr('rel')+']='+jQuery(this).val()+'&';
    });
    
    if (err == true) {
      alert('Please ensure all entered charges have names');
      return false;
    }
    
    preview += 'name='+jQuery('#InvoiceName').val();
    
    return preview;
  }

  jQuery(function() {
    jQuery('#preview').click(function() {
      var preview = update_preview();
      
      if (preview != null) {
        jQuery.colorbox({ width: 930, height: 600, href:'/invoice/preview_invoice/{$month}/{$year}?'+preview, iframe: true });
      }
      
      return false;
    });
  });
</script>

<style type="text/css">
  form input {
    border: 1px solid #000;
    width: 125px;
    margin: 0;
  }
  
  table td {
    padding: 0 !important;
  }
</style>

<div class="input step">
  <span class="title">Create custom invoice</span>
  {$form->create(null, array(url="/invoice/index/$month/$year"))}
  {$form->input("name")}
  <div class="department select">
    <h3 style="font-size: 12pt; margin-top: 10px;">Select Departments</h3>
    {foreach $departments department}
      <div class="checkbox">
        <input type="hidden" name="data[Department][{$department.Department.id}]" value="0" />
        <input rel="{$department.Department.id}" class="dp" id="department{$department.Department.id}" {if $this->data[Department][$department.Department.id] == 1}checked="checked"{/} name="data[Department][{$department.Department.id}]" type="checkbox" value="1" />
        <label for="department{$department.Department.id}" style="width: 17em;">{$department.Department.name}</label>
      </div>
    {/}
  </div>
  
  <div style="clear: left;">&nbsp;</div>
  
  <div class="ordertype select">
    <h3 style="font-size: 12pt; margin-top: 10px;">Work Order Types</h3>
    {foreach $ordertypes ordertype}
      <div class="checkbox">
        <input type="hidden" name="data[Ordertype][{$ordertype.Ordertype.id}]" value="0" />
        <input rel="{$ordertype.Ordertype.id}" class="ot" id="ordertype{$ordertype.Ordertype.id}" {if $this->data[Ordertype][$ordertype.Ordertype.id] == 1}checked="checked"{/} name="data[Ordertype][{$ordertype.Ordertype.id}]" type="checkbox" value="1" />
        <label for="ordertype{$ordertype.Ordertype.id}" style="width: 17em;">{$ordertype.Ordertype.name}</label>
      </div>
    {/}
  </div>
  
  <div style="clear: left;">&nbsp;</div>
  
  <div class="custom_charges select">
    <h3 style="font-size: 12pt; margin-top: 10px;">Custom Charges</h3>
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Charge (&pound;)</th>
        </tr>
      </thead>
      <tbody>
      {foreach $charges cid charge}
        <tr>
          <td>
            <input rel="{$cid}" class="charge_name cn{$cid}" type="text" name="data[Charges][{$cid}][name]" value="{$charge.name}" />
          </td>
          <td>
            <input rel="{$cid}" class="charge_value cv{$cid}" type="text" name="data[Charges][{$cid}][value]" value="{$charge.value}" />
          </td>
        </tr>
      {else}
        {for cid 0 5}
        <tr>
          <td>
            <input rel="{$cid}" class="charge_name cn{$cid}" type="text" name="data[Charges][{$cid}][name]" value="" />
          </td>
          <td>
            <input rel="{$cid}" class="charge_value cv{$cid}" type="text" name="data[Charges][{$cid}][value]" value="" />
          </td>
        </tr>
        {/for}
      {/}
      </tbody>
    </table>
  </div>
  <div class="input submit">
    {$form->button('Preview', array(id="preview", name="data[Preview]", value="true"))}
    {$form->button('Create', array(name="data[Create]", value="true"))}
  </div>
</div>

<div class="input step2" style="margin-left: 10px;">
  <span class="title">Custom invoices for {$month}/{$year}</span>
  <div class="invoices select">
    <h3 style="font-size: 12pt; margin-top: 10px;">Saved Invoices</h3>
    <table>
      <thead>
        <tr><th>Invoice Name</th><th>Actions</th></tr>
      </thead>
      <tbody>
    {foreach $invoices invoice}
      <tr>
        <td>{$invoice.Invoice.name}</td>
        <td><button onclick="location.href='/invoice/print_invoice/{$invoice.Invoice.id}';">Print</button></td>
      </tr>
    {else}
      <tr>
        <td colspan="2">No custom invoices</td>
      </tr>
    {/}
    </tbody>
    </table>
  </div>
</div>
<div style="clear: both;">&nbsp;</div>