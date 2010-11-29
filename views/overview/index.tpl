{$paginator->options(array(update="#content", evalScripts=true))}

<style type="text/css" media="screen">
  table#front tr td {
    cursor: pointer;
  }
</style>

<table border="0" id="front">
  <thead>
    <tr>
      <th>Date</th><th>Work Orders</th>{if $role->atleast($role->VALET_ADMIN)}<th>Cost</th>{/}<th>Charge</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$workorders item=workorder}
      <tr class="{cycle values=array("odd", "even")}" onclick="location.href='/overview/complete/{$workorder.0.month}/{$workorder.0.year}';">
        <td>1-{$workorder.0.monthname}-{$workorder.0.year} to {$workorder.0.lastday}-{$workorder.0.monthname}-{$workorder.0.year}</td>
        <td>{$workorder.0.count}</td>
        {$cost=$workorder.0.acost+$workorder.0.ocost}
        {$charge=$workorder.0.acharge+$workorder.0.ocharge}
        {if $role->atleast($role->VALET_ADMIN)}<td>£{'%n'|money_format:$cost}</td>{/}
        <td>£{'%n'|money_format:$charge}</td>
      </tr>
    {/foreach}
  </tbody>
</table>

{$this->Js->writeBuffer()}

