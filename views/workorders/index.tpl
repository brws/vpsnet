{$paginator->options(array(update="#content", evalScripts=true))}
{include "../clever/filters.tpl" status=true}

<table border="0" cellpadding="0" cellspacing="1" id="front">
  <thead>
    <tr>
      <th>{$paginator->sort("Dept", "Department.name")}</th><th>{$paginator->sort("Authorised By", "AuthorisedByUser.username")}</th><th>Work Order</th><th>Addon</th><th>Notes</th>
      <th>Car</th><th>{$paginator->sort("Key / Tag", "Workorder.key")}</th><th>{$paginator->sort("Date Required", "Workorder.datetime_required")}</th>
      <th>{$paginator->sort("Date Created", "Workorder.created")}</th><th>{$paginator->sort("Status", "Status.name")}</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$workorders item=workorder}
      <tr class="{cycle values=array("odd", "even")} {if strtotime($workorder.Workorder.datetime_required) < strtotime('now')}overdue{/if} color{$workorder.Workorder.status_id}" onclick="location.href='/workorders/view/{$workorder.Workorder.id}';">
        <td>{$workorder.Department.name}</td>
        <td>{ucwords $workorder.AuthorisedByUser.firstname} {ucwords $workorder.AuthorisedByUser.surname}</td>
        <td>{foreach from=$workorder.Ordertype item=ordertype}
          {$ordertype.name}<br />
            {foreachelse}
            No Work Order Listed
          {/foreach}
        </td>
        <td>{assign var=addonl value=""}{foreach from=$workorder.Addon item=addon}{assign var=addons value="YES"}{assign var=addonl value=$addonl|cat:$addon.name}{if !$dwoo.foreach.default.last}{assign var=addonl value=$addonl|cat:", "}{/if}{foreachelse}{assign var=addons value="NO"}{/foreach}<span title="{$addonl}">{$addons}</span>
        </td>
        <td><span title="{$workorder.Workorder.notes}">{if $workorder.Workorder.notes}READ{else}NONE{/}</span></td>
        <td><strong>{if $workorder.Car.registration}{$workorder.Car.registration}{else}{$workorder.Car.chassis}{/if}</strong>, {$workorder.Car.colour}<br />
        {$workorder.Car.make} {$workorder.Car.variant}</td>
        <td>{$workorder.Workorder.key}</td>
        <td>{date_format $workorder.Workorder.datetime_required "%d/%m/%Y"}<br /><strong>{date_format $workorder.Workorder.datetime_required "%I:%M %P"}</strong></td>
        <td>{date_format $workorder.Workorder.created "%d/%m/%Y"}<br /><strong>{date_format $workorder.Workorder.created "%I:%M %P"}</strong></td>
        <td class="status">{$workorder.Status.name}</td>
      </tr>
    {/foreach}
  </tbody>
</table>

<table id="key" style="width: 600px; margin: 0 auto;" cellpadding="0" cellspacing="0">
  <thead>
    <tr><th colspan="6">Key / Legend</th>
  </thead>
  <tbody>
    <tr><td class="color0">Not Yet Started</td><td class="color3">In Progress</td><td class="color1">Completed</td><td class="color2">Cancelled</td><td class="overdue">Overdue</td><td class="color4">Urgent</td></tr>
  </tbody>
</table>

{$this->Js->writeBuffer()}

