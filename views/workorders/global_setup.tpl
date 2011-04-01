<style type="text/css" media="screen">
  form label {
    display: none;
  }

  form input {
    border: 1px solid #000;
    margin: 0;
    width: 194px;
  }

  form .input {
    margin: 0 !important;
    padding: 0;
  }

  div.setups {
    margin-left: 20px;
    width: 648px;
    float: left;
  }

  div.setups table {
    width: 630px;
  }

  div.setups td, div.departs td {
    padding: 2px !important;
    margin: 2px !important;
  }

  div.departs {
    width: 100px;
    float: left;
    margin-left: 20px;
  }
</style>

<div class="setups">
  <h3>Setup Global Workorders</h3>
  <form target="/workorders/settax" method="post">
    <div class="input text">
      <label for="vat">VAT amount</label><input type="text" id="vat" value="{$vdata.vat}" name="data[vat]" />
    </div>
    <div class="submit"><input type="submit" value="Save VAT Amount"></div>
  </form>
  <!--
  {$form->create("Ordertype")}
  <table border="0" cellspacing="0" cellpadding="0">
    <tr><th>Work Order Description</th><th>Charge For Customer (£)</th><th>Internal Cost (£)</th><th>&nbsp;</th></tr>
    {for i 0 9}
    <tr>
      <td>{$form->input(cat("Ordertype." $i ".id"), array(value=$ordertypes[$i].Ordertype.id))}
          {$disabled=isset($ordertypes[$i])}
          {$form->input(cat("Ordertype." $i ".name"), array(value=$ordertypes[$i].Ordertype.name))}</td>
      <td>{$form->input(cat("Ordertype." $i ".charge"), array(value=$ordertypes[$i].Ordertype.charge, disabled=$disabled))}</td>
      <td>{$form->input(cat("Ordertype." $i ".cost"), array(value=$ordertypes[$i].Ordertype.cost))}{$form->input(cat("Ordertype." $i ".order"), array(style="display: none", value=$i))}</td>
      <td>{if isset($ordertypes[$i])}<a href="/ordertypes/delete/{$ordertypes[$i].Ordertype.id}?global=true" title="Delete {$ordertypes[$i].Ordertype.name}"><img src="/img/delete.png" /></a>{/if}</td>
    </tr>
    {/for}
  </table>
  {$form->end("Save Work Orders")}
<h3>Setup Global Addons</h3>
  {$form->create("Addon")}
  <table border="0" cellspacing="0" cellpadding="0">
    <tr><th>Addon Description</th><th>Charge For Customer (£)</th><th>Internal Cost (£)</th><th>&nbsp;</th></tr>
    {for i 0 9}
      <tr>
        <td>{$form->input(cat("Addon." $i ".id"), array(value=$addons[$i].Addon.id))}
            {$disabled=isset($addons[$i])}
            {$form->input(cat("Addon." $i ".name"), array(value=$addons[$i].Addon.name))}</td>
        <td>{$form->input(cat("Addon." $i ".charge"), array(value=$addons[$i].Addon.charge, disabled=$disabled))}</td>
        <td>{$form->input(cat("Addon." $i ".cost"), array(value=$addons[$i].Addon.cost))}{$form->input(cat("Addon." $i ".order"), array(style="display: none", value=$i))}</td>
        <td>{if isset($addons[$i])}<a href="/addons/delete/{$addons[$i].Addon.id}?global=true" title="Delete {$addons[$i].Addon.name}"><img src="/img/delete.png" /></a>{/if}</td>
      </tr>
    {/for}
  </table>
  {$form->end("Save Addons")}
</div>

<div class="departs">
  <h3>Departments</h3>
  {$form->create("Department")}
  <table border="0" cellspacing="0" cellpadding="0">
    <tr><th>Department Name</th><th>&nbsp;</th></tr>
    {for i 0 9}
    <tr>
      <td>{if $departments[$i]}{$form->input(cat("Department." $i ".id"), array(value=$departments[$i].Department.id))}{/if}
          {$form->input(cat("Department." $i ".name"), array(value=$departments[$i].Department.name))}</td>
      <td>{if isset($departments[$i])}<a href="/departments/delete/{$departments[$i].Department.id}?global=true" title="Delete {$departments[$i].Department.name}"><img src="/img/delete.png" /></a>{/if}</td>
    </tr>
    {/for}
  </table>
  {$form->end("Save Departments")}
</div>
-->

