{$form->create('Workorder')}

<style type="text/css" media="screen">
  #WorkorderNotes {
    height: 300px;
  }
</style>

<div class="input step">
  <span class="title">The Job</span>
{if $role->atleast($role->VALET_ADMIN)}
    {$form->input('Workorder.assigned_to_user_id', array(type="select", options=$vusers, label="Valeter working on Job"))}
{/if}
  {$form->input('Ordertype.0', array(type="radio", options=$ordertypes, label="Work Order", empty="-- Select Workorder 1 --"))}
  {$form->input('Addon', array(type="hidden", label="Addons", multiple="checkbox"))}
  {include "../clever/calendar.tpl" label="Date Required" default_date="now" label2="Time Required" field="datetime_required" model="Workorder"}
</div>

<div class="input step">
  <span class="title">The Car</span>
  {$form->input('Car.id')}
  {$form->input('Car.registration')}
  {$form->input('Car.chassis')}
  {$ajax->observeField('CarRegistration', array(url="update_car_reg", update="results"))}
  {$ajax->observeField('CarChassis', array(url="update_car_cha", update="results"))}
  {$form->input('Car.makes', array(type="select", name="data[Car][make]", options=$makes, value=$data.Car.make, empty="-- Select Make --"), null)}
  {$form->input('Car.variant', array(type="text"))}
  {$form->input('Car.colour')}
  {$form->input('key', array(label="Key / Tag"))}
  {$form->input('place', array(label="Location"))}
</div>

<div class="input step">
  <span class="title">Extra Instructions And Notes</span>
  {$form->input('Workorder.department_id')}
  {$form->input('notes')}
</div>

{$form->submit('Add Work Order', array(id="submit"))}
<div id="results">

</div>

