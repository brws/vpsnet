<style type="text/css">
  #filters input {
    float: left
  }
  
  #calendars {
    display: none;
  }
</style>

<script type="text/javascript">
  var current_user = {default $current_user 0};
  var date_range = {default $date_range 0};

  function setUser(t) {
    if (jQuery(t).val() == '') {
      var href = '/overview/valeter';
      if (date_range) href += '?w=' + date_range;
    } else {
      var href = '/overview/valeter';
      
      if (date_range) {
        href += '?w=' + date_range + '&';
      } else {
        href += '?';
      }
      
      href += 'u=' + jQuery(t).val();
    }
    
    location.href=href;
    
    return function() {
      return false;
    };
  }
  
  function setDate(t) {
    if (jQuery(t).val() == 'custom') {
      jQuery('#calendars').show();
    } else if (jQuery(t).val() == '') {
      jQuery('#calendars').hide();
    } else {
      var href = '/overview/valeter';
      
      if (current_user) {
        href += '?u=' + current_user + '&';
      } else {
        href += '?';
      }
      
      href += 'w=' + jQuery(t).val();
      
      location.href=href;
      jQuery('#calendars').hide();
    }
    
    return function() {
      return false;
    };
  };
</script>

<div id="filters">
  {$form->create('Filters', array(id="filterform"))}
    {$form->input('User', array(default=$current_user, onchange="setUser(this)", type="select", label=false, empty="[User]", options=$vusers, style="width: 100px;"))}
    
    <div class="input select">
      <select id="time_range" name="data[Filters][time_range]" onchange="setDate(this)">
        <option {if $time_range == ""}selected="selected"{/} value="">[Choose Date Range]</option>
        <option {if $time_range == "custom"}selected="selected"{/} value="custom">Enter Date Range</option>
        <option {if $time_range == "last_month"}selected="selected"{/} value="last_month">Last Month</option>
        <option {if $time_range == "last_week"}selected="selected"{/}value="last_week">Last Week</option>
        <option {if $time_range == "this_month"}selected="selected"{/}value="this_month">This Month</option>
        <option {if $time_range == "this_week"}selected="selected"{/}value="this_week">This Week</option>
        <option {if $time_range == "today"}selected="selected"{/}value="today">Today</option>
        <option {if $time_range == "yesterday"}selected="selected"{/}value="yesterday">Yesterday</option>
      </select>
    </div>
    
    <div id="calendars">
      {include "../clever/calendar.tpl" label=false time=false default_date=$date_from field="datetime_required_from" model="Filters"}
      {include "../clever/calendar.tpl" label=false time=false default_date=$date_to field="datetime_required_to" model="Filters"}
      <button>Set Range</button>
    </div>
  </form>
</div>

<div style="float: left">
  <table>
    <thead>
      <tr>
        <th>Valet Type</th><th>Amount</th>
      </tr>
    </thead>
    <tbody>
    {foreach from=$data item=report}
      <tr>
        <td>{$report.ordertypes.name}</td>
        <td>{$report.0.valets}</td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  
  <pre>{print_r $data}</pre>
</div>
