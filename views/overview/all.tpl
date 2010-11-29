{$paginator->options(array(update="#content", evalScripts=true))}
<button onclick="location.href='/overview/complete/{$month}/{$year}'" style="float: left; margin-bottom: 10px">Back To Month View</button>
{include "../clever/filters.tpl" norange=true status=false}
<table border="0" id="front">
  <thead>
    <tr>
      <th>{$paginator->sort("Authorised By", "AuthorisedByUser.username")}</th><th>{$paginator->sort("Work Order", "Ordertype.name")}</th><th>Addon</th>
      <th>Car</th><th>{$paginator->sort("Date Required", "Workorder.datetime_required")}</th>
      <th>{$paginator->sort("Date Created", "Workorder.created")}</th><th>Workorder Charges</th><th>Addon Charges</th><th>Total Charge</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$workorders item=workorder}
      <tr class="{cycle values=array("odd", "even")} {if strtotime($workorder.Workorder.datetime_required) < strtotime('now')}overdue{/if} color{$workorder.Workorder.status_id}" onclick="location.href='/workorders/view/{$workorder.Workorder.id}';">
        <td>{ucwords $workorder.AuthorisedByUser.firstname} {ucwords $workorder.AuthorisedByUser.surname}</td>
        <td>{foreach from=$workorder.Ordertype item=ordertype}
          {$ordertype.name}<br />
            {foreachelse}
            No Work Order Listed
          {/foreach}
        </td>
        <td>{assign var=addonl value=""}{foreach from=$workorder.Addon item=addon}{assign var=addons value="YES"}{assign var=addonl value=$addonl|cat:$addon.name}{if !$dwoo.foreach.default.last}{assign var=addonl value=$addonl|cat:", "}{/if}{foreachelse}{assign var=addons value="NO"}{/foreach}<span title="{$addonl}">{$addons}</span>
        </td>
        <td><strong>{if $workorder.Car.registration}{$workorder.Car.registration}{else}{$workorder.Car.chassis}{/if}</strong>, {$workorder.Car.colour}<br />
        {$workorder.Car.make} {$workorder.Car.variant}</td>
        <td>{date_format $workorder.Workorder.datetime_required "%d/%m/%Y"}<br /><strong>{date_format $workorder.Workorder.datetime_required "%I:%M %P"}</strong></td>
        <td>{date_format $workorder.Workorder.created "%d/%m/%Y"}<br /><strong>{date_format $workorder.Workorder.created "%I:%M %P"}</strong></td>
        {$cost=0}
        {$charge=0}
        {foreach from=$workorder.Ordertype item=ordertype}
          {$cost+=$ordertype.cost}
          {$charge+=$ordertype.charge}
        {/}<td>{if $role->atleast($role->VALET_ADMIN)}<strong>Cost:</strong> £{money_format '%n' $cost}<br />{/}<strong>Charge:</strong> £{money_format '%n' $charge}</td>

        {$acost=0}
        {$acharge=0}
        {foreach from=$workorder.Addon item=addon}
          {$acost+=$addon.cost}
          {$acharge+=$addon.charge}
        {/}<td>{if $role->atleast($role->VALET_ADMIN)}<strong>Cost:</strong> £{money_format '%n' $acost}<br />{/}<strong>Charge:</strong> £{money_format '%n' $acharge}</td>
        <td>{if $role->atleast($role->VALET_ADMIN)}<strong>Cost:</strong> £{money_format '%n' $cost+$acost}<br />{/}<strong>Charge:</strong> £{money_format '%n' $charge+$acharge}</td>
      </tr>
    {/foreach}
  </tbody>
</table>

{$this->Js->writeBuffer()}

