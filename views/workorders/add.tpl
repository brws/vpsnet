{$form->create('Workorder', array(onsubmit="return test(this)"))}

<style type="text/css" media="screen">
  #WorkorderNotes {
    height: 300px;
  }
</style>

<script type="text/javascript" charset="utf-8">
  jQuery(function() {
    var $ordertypes = new Array();

    jQuery('#Ordertype1 option').each(function() {
      $ordertypes.push([jQuery(this).val(), jQuery(this).text()]);
    });

    jQuery('#Ordertype0').change(function() {
      var $this = jQuery(this);
      jQuery('#Ordertype1').empty();
      var dropdowns = new Array();

      for (var i = 0, len = $ordertypes.length; i < len; i++) {

        if (($this.val() !== $ordertypes[i][0]) || $ordertypes[i][0].length == 0) {
          dropdowns.push('<option value="'+$ordertypes[i][0]+'">' +$ordertypes[i][1]+ '</value>');
        }
      }

      jQuery('#Ordertype1').html(dropdowns.join(''));
    });
  });

  function test(aform) {
    if (jQuery('#Workorder_hour').val() < 8 && jQuery('#Workorder_meridian').val() == 'am') {
      if (confirm('Are you sure you want to book this car in for ' + parseInt(jQuery('#Workorder_hour').val()) + ' in the morning?')) {
        return true;
      } else {
        return false;
      }
    } else {
      return true;
    }
  }
</script>

<div class="input step">
  <span class="title">The Job</span>
  {$form->input('Ordertype.0', array(type="select", options=$ordertypes, label="Work Order", empty="-- Select Workorder 1 --"))}
  <div style="display: none;">
    {$form->input('Ordertype.1', array(type="select", options=$ordertypes, label="Work Order", empty="-- Select Workorder 2 --"))}
  </div>
  {$form->input('Addon', array(label="Addons", multiple="checkbox"))}
  {include "../clever/calendar.tpl" label="Date Required" default_date="now" label2="Time Required" field="datetime_required" model="Workorder"}
  {if $role->atleast($role->VALET_ADMIN)}
    {$form->input('Workorder.assigned_to_user_id', array(type="select", options=$vusers, label="Valeter working on Job"))}
  {/if}
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

