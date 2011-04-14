{$paginator->options(array(update="#content", evalScripts=true))}

<style type="text/css" media="screen">
  table#front tr td {
    cursor: pointer;
  }
</style>

<table border="0" id="front">
  <thead>
    <tr>
      <th>Date</th><th>Work Orders</th>{if $role->atleast($role->VALET_ADMIN)}<th>Cost</th>{/}<th>Charge</th><th colspan="3">Actions</th>
    </tr>
  </thead>
  <tbody>
    {foreach from=$workorders item=workorder}
      {capture "onclick"}onclick="location.href='/overview/complete/{$workorder.0.month}/{$workorder.0.year}';"{/capture}
      <tr class="{cycle values=array("odd", "even")}">
        <td {$.capture.onclick}>1-{$workorder.0.monthname}-{$workorder.0.year} to {$workorder.0.lastday}-{$workorder.0.monthname}-{$workorder.0.year}</td>
        <td {$.capture.onclick}>{$workorder.0.count}</td>
        {$cost=$workorder.0.acost+$workorder.0.ocost}
        {$charge=$workorder.0.acharge+$workorder.0.ocharge}
        {if $role->atleast($role->VALET_ADMIN)}<td>£{'%n'|money_format:$cost}</td>{/}
        <td {$.capture.onclick}>£{'%n'|money_format:$charge}</td>
        <td>
          <button onclick="location.href='/overview/print_report/{$workorder.0.month}/{$workorder.0.year}';">Print</button>
        </td>
        <td>
          <button onclick="location.href='/overview/custom_report/{$workorder.0.month}/{$workorder.0.year}';">Custom</button>
        </td>
        <td>
          {if $role->atleast($role->VALET_ADMIN)}
            <button onclick="location.href='/overview/custom_report/{$workorder.0.month}/{$workorder.0.year}';">Charges</button>
          {/}
        </td>
      </tr>
    {/foreach}
  </tbody>
</table>

{$this->Js->writeBuffer()}