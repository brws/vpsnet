<script type="text/javascript" charset="utf-8">
  jQuery(function() {
    var $ordertypes = new Array();

    jQuery('#Ordertype1 option').each(function() {
      $ordertypes.push([jQuery(this).val(), jQuery(this).text()]);
    });

    jQuery('#Ordertype0').change(function() {
      var $this = jQuery(this);
      jQuery('#Ordertype1').empty();
      var dropdowns = new Array();

      for (var i = 0, len = $ordertypes.length; i < len; i++) {

        if (($this.val() !== $ordertypes[i][0]) || $ordertypes[i][0].length == 0) {
          dropdowns.push('<option value="'+$ordertypes[i][0]+'">' +$ordertypes[i][1]+ '</value>');
        }
      }

      jQuery('#Ordertype1').html(dropdowns.join(''));
    });

    jQuery('form').bind('submit', function() {
      jQuery('input, select, option').attr('disabled', '');
    });

    {if $role->is($role->VALET_STAFF) or $role->is($role->DEALER_STAFF)}
      jQuery('input[type=checkbox]').attr('disabled', 'disabled');
    {/}
  });
</script>
{$disabled=""}
{$disabledtf=false}

{$form->create('Workorder')}
{$form->input('Workorder.id')}
<div class="input step">
  <span class="title">The Job</span>
  {$form->input('Ordertype.0', array(type="select", options=$ordertypes, label="Work Order", empty="-- Select Workorder 1 --"))}
  {$form->input('Ordertype.1', array(type="select", options=$ordertypes, label="Work Order", empty="-- Select Workorder 2 --"))}
  {$form->input('Addon', array(label="Addons", multiple="checkbox"))}

  {if $role->is($role->VALET_STAFF)}
    <div class="input">
    <br />
    <label>Date Required</label>
    <span style="margin: 10px;">{date_format $data['Workorder']['datetime_required']}</span>
    </div>
    <div class="input">
    <label>Time Required</label>
    <span style="margin: 10px;">{date_format $data['Workorder']['datetime_required'] "%r"}</span>
    </div>
    <div style="visibility: hidden">
    {include "../clever/calendar.tpl" disabled=$disabledtf label="Date Required" label2="Time Required" field="datetime_required" model="Workorder"}
    </div>
  {else}
  	{include "../clever/calendar.tpl" disabled=$disabledtf label="Date Required" label2="Time Required" field="datetime_required" model="Workorder"}
  {/if}
</div>

<div class="input step">
  <span class="title">The Car</span>
  {$form->input('Car.id', array(disabled=$disabled))}
  {$form->input('Car.registration', array(disabled=$disabled))}
  {$form->input('Car.chassis', array(disabled=$disabled))}
  {$ajax->observeField('CarRegistration', array(url="update_car_reg", update="results"))}
  {$ajax->observeField('CarChassis', array(url="update_car_cha", update="results"))}
  {$form->input('Car.makes', array(type="select", name="data[Car][make]", options=$makes, value=$data.Car.make, empty="-- Select Make --"), null)}
  {$form->input('Car.variant', array(type="text"))}
  {$form->input('Car.colour', array(disabled=$disabled))}
  {$form->input('key', array(label="Key / Tag"))}
  {$form->input('place', array(label="Location"))}
</div>

<div class="input step">
  <span class="title">Extra Instructions And Notes</span>
  {$form->input('Workorder.department_id', array(disabled=$disabled))}
  {$form->input('notes')}
  {$form->input('Workorder.status_id', array(type=hidden))}
  <div class="input text ">
    <label>Current Status</label><div style="padding: 4px" class="{if strtotime($data.Workorder.datetime_required) < strtotime('now')}overdue{/if} color{$data.Workorder.status_id}" style="float: left;">
      <strong>
        {if $data.Status.id == 1}
          {$data.Status.name} on {date_format $data.Workorder.datetime_completed "%d-%B-%Y at %l:%M %P"}
        {else}
        {$data.Status.name}
        {/if}
      </strong>
    </div>
  </div>
  {if ($data.Workorder.completed_by > -1)}
    <div class="input text ">
      <label>Completed by</label>
      <div style="padding: 4px" style="float: left;">
        <strong>
          {$data.CompletedByUser.firstname} {$data.CompletedByUser.surname} ({$data.CompletedByUser.username})
        </strong>
      </div>
    </div>
  {/if}
  {if ($data.Workorder.updated_by > -1)}
    <div class="input text ">
      <label>Last updated by</label>
      <div style="padding: 4px" style="float: left;">
        <strong>
          {$data.UpdatedByUser.firstname} {$data.UpdatedByUser.surname} ({$data.UpdatedByUser.username})
        </strong>
      </div>
    </div>
  {/if}
  {if ($data.Workorder.assigned_to_user_id > -1)}
    <div class="input text ">
      <label>Assigned to</label>
      <div style="padding: 4px" style="float: left;">
        <strong>
          {$data.AssignedToUser.firstname} {$data.AssignedToUser.surname} ({$data.AssignedToUser.username})
        </strong>
      </div>
    </div>
  {/if}
</div>

<script type="text/javascript">
  jQuery(function() {
    jQuery('#pickup, #complete, #urgent, #cancel').click(function() {
      jQuery('#newaction').val(jQuery(this).attr('name'));
      jQuery('#WorkorderViewForm').submit();
    });
  });
</script>

<input type="hidden" id="newaction" name="data[action]" value="4"></input>

{if $data.Workorder.status_id > 2 || $data.Workorder.status_id == 0}
  <div class="fl">
    {if $role->is($role->VALET_STAFF) or $role->atleast($role->DEALER_ADMIN)}


            <button id="pickup" name="0">Pick Up Job</button>
        <button id="complete" name="1">Job Completed</button>

    {/}
    {if $role->is($role->DEALER_STAFF) or $role->atleast($role->DEALER_ADMIN)}
            <button id="urgent" name="2">Make Urgent</button>
        <button id="cancel" name="3">Cancel This Work Order</button>

    {/}
  </div>

  {$form->end('Save')}
{/if}

