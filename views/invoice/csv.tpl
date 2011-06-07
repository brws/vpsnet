"Authorised By","Department","Work Order","Addon","Car","Colour","Make","Variant","Date Required","Date Created","Workorder Charges","Addon Charges","Total Charge",
{$totalcost=0}{$totalcharge=0}{foreach from=$result item=orty key=dept}{foreach from=$orty item=workorders key=name}{foreach from=$workorders item=workorder}
"{ucwords $workorder.AuthorisedByUser.firstname} {ucwords $workorder.AuthorisedByUser.surname}","{$dept}","{foreach from=$workorder.Ordertype item=ordertype}{$ordertype.name} {foreachelse}No Work Order Listed{/foreach}","{assign var=addonl value=""}{foreach from=$workorder.Addon item=addon}{assign var=addons value="YES"}{assign var=addonl value=$addonl|cat:$addon.name}{if !$dwoo.foreach.default.last}{assign var=addonl value=$addonl|cat:" - "}{/if}{else}{assign var=addons value="NO"}{/}{if $addons == "YES"}{$addonl}{else}N/A{/}","{if $workorder.Car.registration}{$workorder.Car.registration}{else}{$workorder.Car.chassis}{/if}","{$workorder.Car.colour}","{$workorder.Car.make}","{$workorder.Car.variant}","{date_format $workorder.Workorder.datetime_required "%d/%m/%Y"} {date_format $workorder.Workorder.datetime_required "%I:%M %P"}","{date_format $workorder.Workorder.created "%d/%m/%Y"} {date_format $workorder.Workorder.created "%I:%M %P"}","{$cost=0}{$charge=0}{foreach from=$workorder.Ordertype item=ordertype}{$cost+=$ordertype.cost}{$charge+=$ordertype.charge}{/}£{$charge}","{$acost=0}{$acharge=0}{foreach from=$workorder.Addon item=addon}{$acost+=$addon.cost}{$acharge+=$addon.charge}{/}£{$acharge}","£{$charge+$acharge}",
{/foreach}{/foreach}{/foreach}
,,,,,,,,,,,,
"Workorder Subtotal",£{$total.workorder_ch},,,,,,,,,,,
"Fixed Costs",,,,,,,,,,,,
{foreach from=$fixedcosts item=cost}{if $cost.FixedCost.hidden == 0}{$cost.FixedCost.name} ({$cost.FixedCost.period}),£{$cost.FixedCost.charge},,,,,,,,,,,
{/}{/}
"Fixed Costs Subtotal",£{$total.fixedcost_ch},,,,,,,,,,,
"VAT",£{$total.vat_on_all}, @ {$vat}%,,,,,,,,,,,
"Total Charge",£{$total.all_with_vat},,,,,,,,,,,