{$paginator->options(array(update="#content", evalScripts=true))}
<button onclick="location.href='/overview'" style="float: left; margin-bottom: 10px">Back To Reports</button><br />
<table border="0" id="front">
  <thead>
    <tr>
      <th>Date</th><th>Work Orders</th>{if $role->atleast($role->VALET_ADMIN)}<th>Cost</th>{/}<th>Charge</th><th>&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$workorders item=workorder}
      <tr class="{cycle values=array("odd", "even")}">
        <td>{$workorder.0.day}/{$workorder.0.monthname}/{$workorder.0.year}</td>
        <td>{$workorder.0.count}</td>
        {$cost=$workorder.0.acost+$workorder.0.ocost}
        {$charge=$workorder.0.acharge+$workorder.0.ocharge}
        {if $role->atleast($role->VALET_ADMIN)}<td>£{'%n'|money_format:$cost}</td>{/}
        <td>£{'%n'|money_format:$charge}</td>
        <td><button onclick="location.href='/overview/all/{$workorder.0.year}-{$workorder.0.month}-{$workorder.0.day}';">Show Complete List of Work Orders</button></td>
      </tr>
    {/foreach}
  </tbody>
</table>

{$this->Js->writeBuffer()}

