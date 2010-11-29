<script type="text/javascript" charset="utf-8">
  window.cars = {$cars_json}[0];
  
  if (window.cars) {
    jQuery('#CarId').val(window.cars.Car.id);
    jQuery('#CarRegistration').val(window.cars.Car.registration);
    jQuery('#CarChassis').val(window.cars.Car.chassis);
    jQuery('#CarMakes').val(window.cars.Car.make);
    jQuery('#CarVariant').val(window.cars.Car.variant);
    jQuery('#CarColour').val(window.cars.Car.colour);
  }
</script>
{if $cars}
<table border="0" cellpadding="0" cellspacing="1">
  <thead>
    <tr>
      <th>Dept</th><th>Authorised By</th><th>Work Order</th><th>Addon</th><th>Notes</th><th>Date Cleaned</th><th>Status</th><th>Dealership</th>     
    </tr>
  </thead>
  <tbody>
    {foreach from=$cars item=workorder}
      <tr style="cursor: pointer;" class="{if strtotime($workorder.Workorder.datetime_required) < strtotime('now')}overdue{/if} color{$workorder.Workorder.status_id}" onclick="location.href='/workorders/view/{$workorder.Workorder.id}';">
        <td>{$workorder.Department.name}</td>
        <td>{$workorder.AuthorisedByUser.username}</td>
        <td>{foreach from=$workorder.Ordertype item=ordertype}
          {$ordertype.name}<br />
            {foreachelse}
            No Work Order Listed
          {/foreach}
        </td>
        <td>
          {assign var=addonl value=""}
          {foreach from=$workorder.Addon item=addon}
            {assign var=addons value="YES"}
            {assign var=addonl value=$addonl|cat:$addon.name}
            {if !$dwoo.foreach.default.last}
              {assign var=addonl value=$addonl|cat:", "}
            {/if}
          {foreachelse}
            {assign var=addons value="NO"}
          {/foreach}
          <span title="{$addonl}">{$addons}</span>
        </td>
        <td><span title="{$workorder.Workorder.notes}">READ</span></td>
        <td>{date_format $workorder.Workorder.datetime_completed "%d/%m/%Y"}</td>
        <td>{$workorder.Status.name}</td>
        <td>{$workorder.Location.name}</td>
      </tr>
    {/foreach}
  </tbody>
</table>
{/if}