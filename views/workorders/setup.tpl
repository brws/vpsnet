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
  <h3>Setup Workorders</h3>
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
      <td>{if isset($ordertypes[$i])}<a href="/ordertypes/delete/{$ordertypes[$i].Ordertype.id}" title="Delete {$ordertypes[$i].Ordertype.name}"><img src="/img/delete.png" /></a>{/if}</td>
    </tr>
    {/for}
  </table>
  {$form->end("Save Work Orders")}
  <h3>Setup Addons</h3>
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
        <td>{if isset($addons[$i])}<a href="/addons/delete/{$addons[$i].Addon.id}" title="Delete {$addons[$i].Addon.name}"><img src="/img/delete.png" /></a>{/if}</td>
      </tr>
    {/for}
  </table>
  {$form->end("Save Addons")}
  
  <h3>Setup Fixed Costs</h3>
  {$form->create("FixedCost")}
  <table border="0" cellspacing="0" cellpadding="0">
    <tr><th>Fixed Cost Description</th><th>Charge For Customer (£)</th><th>Internal Cost (£)</th><th>Period</th><th>Repeated / Period</th><th>&nbsp;</th></tr>
    {for i 0 9}
      <tr>
        <td>{$form->input(cat("FixedCost." $i ".id"), array(value=$fixedcosts[$i].FixedCost.id))}
            {$disabled=isset($fixedcosts[$i])}
            {$form->input(cat("FixedCost." $i ".name"), array(value=$fixedcosts[$i].FixedCost.name))}</td>
        <td>{$form->input(cat("FixedCost." $i ".charge"), array(value=$fixedcosts[$i].FixedCost.charge, disabled=$disabled))}</td>
        <td>{$form->input(cat("FixedCost." $i ".cost"), array(value=$fixedcosts[$i].FixedCost.cost))}{$form->input(cat("FixedCost." $i ".order"), array(style="display: none", value=$i))}</td>
        <td>
          <div class="input select">
            <select name="data[FixedCost][{$i}][period]">
              <option {if $fixedcosts[$i].FixedCost.period == "daily"}selected="selected" {/if}value="daily">Daily</option>
              <option {if $fixedcosts[$i].FixedCost.period == "weekly"}selected="selected" {/if}value="weekly">Weekly</option>
              <option {if $fixedcosts[$i].FixedCost.period == "monthly"}selected="selected" {/if}value="monthly">Monthly</option>
            </select>
          </div>
        </td>
        <td>
          {$form->input(cat("FixedCost." $i ".timesperperiod"), array(value=$fixedcosts[$i].FixedCost.timesperperiod, style="width: 40px;"))} time(s)
        </td>
        <td>{if isset($fixedcosts[$i])}<a href="/fixed_costs/delete/{$fixedcosts[$i].FixedCost.id}" title="Delete {$fixedcosts[$i].FixedCost.name}"><img src="/img/delete.png" /></a>{/if}</td>
      </tr>
    {/for}
  </table>
  {$form->end("Save Fixed Costs")}
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
      <td>{if isset($departments[$i])}<a href="/departments/delete/{$departments[$i].Department.id}" title="Delete {$departments[$i].Department.name}"><img src="/img/delete.png" /></a>{/if}</td>
    </tr>
    {/for}
  </table>
  {$form->end("Save Departments")}
</div>

