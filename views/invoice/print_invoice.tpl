<table border="0" id="front" cellspacing="0" cellpadding="5">
  <caption>VPS Online Report - {$month}/{$year} - {$invname}<br />
    <span style="color: #888">Generated on {date_format $.now "%d-%m-%Y at %H:%M:%S"}</span>
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
        {/}<td class="money">&pound;{number_format $charge 2}</td>

        <td class="money">&pound;{number_format $acharge 2}</td>
        <td class="right">&pound;{number_format $charge+$acharge 2}</td>
      </tr>
  </tbody>
   {/foreach}
   {/foreach}
   {/foreach}
  </tbody>
  <thead>
    <tr>
      <th colspan="7">&nbsp;</th>
      <th colspan="2">Workorder Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="7">&nbsp;</td>
      <td colspan="2" class="money right">&pound;{number_format $total.workorder_ch 2}</td>
    </tr>
  </tbody>
  {if $show_charges == 1}
    <thead>
      <th colspan="9" align="left">Fixed Costs</th>
    </thead>
    <tbody>
      {foreach from=$fixedcosts item=cost}
        {if $cost.FixedCost.hidden == 0}
          <tr>
            <td colspan="7">&nbsp;{$cost.FixedCost.name} ({$cost.FixedCost.period})</td>
            <td colspan="2" class="money right">&pound;{number_format $cost.FixedCost.charge 2}</td>
          </tr>
        {/}
      {/}
    </tbody>
    <thead>
      <tr>
        <th colspan="7">&nbsp;</th>
        <th colspan="2">Fixed Charges Total</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="7">&nbsp;</td>
        <td colspan="2" class="money right">&pound;{number_format $total.fixedcost_ch 2}</td>
      </tr>
    </tbody>
  {/}
  <thead>
    <tr>
      <th colspan="7">&nbsp;</th>
      <th colspan="2">VAT</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="7">&nbsp;</td>
      <td colspan="2" class="money right">
        {if $show_charges == 1}
          &pound;{number_format $total.vat_on_all 2} @ {$vat}%
        {else}
          &pound;{number_format $total.vat_on_jwo 2} @ {$vat}%
        {/}
      </td>
    </tr>
  </tbody>
  <thead>
    <tr>
      <th colspan="7">&nbsp;</th>
      <th colspan="2">Total Charge</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td colspan="7">&nbsp;</td>
      <td colspan="2" class="money right" style="font-size: 20pt">
        {if $show_charges == 1}
          &pound;{number_format $total.all_with_vat 2}
        {else}
          &pound;{number_format $total.jwo_with_vat 2}
        {/}
      </td>
    </tr>
  </tbody>
</table>