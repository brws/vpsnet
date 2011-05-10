<table border="0" id="front" cellspacing="0" cellpadding="5">
  <caption>VPS Online Report - {$month}/{$year} - {$invname}<br />
    <span style="color: #888">Printed {date_format $.now "%d-%m-%Y %H:%M:%S"}</span>
  </caption>
  <thead>
    <tr>
      <th>Authorised By</th><th>Work Order</th><th>Addon</th>
      <th width="150">Car</th><th>Date Required</th>
      <th>Date Created</th><th>Workorder Charges</th><th>Addon Charges</th><th>Total Charge</th>
    </tr>
  </thead>
  
    {$totalcost=0}
    {$totalcharge=0}
    {foreach from=$result item=orty key=dept}
    <thead>
      <tr><th colspan="9" style="background: #000; color: #fff">{$dept}</th></tr>
    </thead>
    {foreach from=$orty item=workorders key=name}
    <thead>
      <tr><th colspan="9">{$name}</th></tr>
    </thead>
    {foreach from=$workorders item=workorder}
      <tbody>
      <tr class="{cycle values=array("odd", "even")}">
        <td>{ucwords $workorder.AuthorisedByUser.firstname} {ucwords $workorder.AuthorisedByUser.surname}</td>
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
          {else}
            {assign var=addons value="NO"}
          {/}
          {if $addons == "YES"}
            <span>{$addonl}</span>
          {else}
            <span style="color: #888">N/A</span>
          {/}
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
        {/}<td>{if $role->atleast($role->VALET_ADMIN)}<strong>Cost:</strong> &pound;{number_format $cost 2}<br />{/}<strong>Charge:</strong> &pound;{number_format $charge 2}</td>

        {$acost=0}
        {$acharge=0}
        {foreach from=$workorder.Addon item=addon}
          {$acost+=$addon.cost}
          {$acharge+=$addon.charge}
        {/}<td>{if $role->atleast($role->VALET_ADMIN)}<strong>Cost:</strong> &pound;{number_format $acost 2}<br />{/}<strong>Charge:</strong> &pound;{number_format $acharge 2}</td>
        <td class="right">{if $role->atleast($role->VALET_ADMIN)}<strong>Cost:</strong> &pound;{number_format $cost+$acost 2}<br />{/}<strong>Charge:</strong> &pound;{number_format $charge+$acharge 2}</td>
      </tr>
      {$totalcost+=$cost}
      {$totalcost+=$acost}
      {$totalcharge+=$charge}
      {$totalcharge+=$acharge}
</tbody>
   {/foreach}
   {/foreach}
   {/foreach}
  </tbody>
  {if $show_charges == 1}
  
  <thead>
    <th colspan="9" align="left">Fixed Costs {$show_charges}</th>
  </thead>
  <tbody>
    {foreach from=$fixedcosts item=cost}
      {if $cost.FixedCost.hidden == 0}
        {$days=cal_days_in_month(0, $month, $year)}
        
        {$origcharge=$cost.FixedCost.charge}
      
        {if $cost.FixedCost.period == 'weekly'}
          {$weeks=$days/7}
          {$cost.FixedCost.charge=$cost.FixedCost.charge*$weeks}
          {$cost.FixedCost.cost=$cost.FixedCost.cost*$weeks}
        {/if}
        
        {if $cost.FixedCost.period == 'daily'}
          {$cost.FixedCost.charge=$cost.FixedCost.charge*$days}
          {$cost.FixedCost.cost=$cost.FixedCost.cost*$days}
        {/if}
        
        {$cost.FixedCost.charge=$cost.FixedCost.charge*$cost.FixedCost.timesperperiod}
        {$cost.FixedCost.cost=$cost.FixedCost.cost*$cost.FixedCost.timesperperiod}
        <tr>
          <td colspan="7">&nbsp;{$cost.FixedCost.name} ({$cost.FixedCost.period})</td>
          <td>{if $role->atleast($role->VALET_ADMIN)}&pound;{number_format $cost.FixedCost.cost 2}{else}-{/}</td><td class="right">&pound;{number_format $cost.FixedCost.charge 2}</td>
        </tr>
        {$totalcost+=$cost.FixedCost.cost}
        {$totalcharge+=$cost.FixedCost.charge}
      {/}
    {/}
  </tbody>
  {/}
  <thead>
    <tr>
      <th colspan="7">&nbsp;</th>
      <th>{if $role->atleast($role->VALET_ADMIN)}Sub Total Cost{else}&nbsp;{/}</th><th>Sub Total Charge</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="7">&nbsp;</td>
      <td>{if $role->atleast($role->VALET_ADMIN)}&pound;{number_format $totalcost 2}{else}&nbsp;{/}</td><td class="right">&pound;{number_format $totalcharge 2}</td>
    </tr>
  </tbody>
  <thead>
    <tr>
      <th colspan="7">&nbsp;</th>
      <th>&nbsp;</th><th>VAT</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="7">&nbsp;</td>
      {$tcpc=$totalcharge/100}
      {$vatc=$tcpc*$vat}
      <td>&nbsp;</td><td class="right">&pound;{number_format $vatc 2} @ {$vat}%</td>
      {$totalcharge=$totalcharge+$vatc}
    </tr>
  </tbody>
  <thead>
    <tr>
      <th colspan="7">&nbsp;</th>
      <th>{if $role->atleast($role->VALET_ADMIN)}Total Cost{else}&nbsp;{/}</th><th>Total Charge</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="7">&nbsp;</td>
      <td>{if $role->atleast($role->VALET_ADMIN)}&pound;{number_format $totalcost 2}{else}&nbsp;{/}</td><td class="right">&pound;{number_format $totalcharge 2}</td>
    </tr>
  </tbody>
</table>