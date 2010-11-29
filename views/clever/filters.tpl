<div id="filters">
  {$form->create('Filters', array(id="filterform"))}
  {$form->input('Department', array(label=false, empty="[Department]", options=$departments))}
  {$form->input('User', array(type="select", label=false, empty="[User]", options=$users, style="width: 100px;"))}
  {$form->input('Ordertype', array(label=false, empty="[Work Order]", options=$ordertypes, style="width: 100px;"))}

  <div>
    {$paginator->prev("«")}
    {$paginator->numbers(array(modulus=2 separator=" "))}
    {$paginator->next("»")}
  </div>
  {if !$norange}
    {include "../clever/calendar.tpl" label=false time=false field="datetime_required_from" model="Filters"}
    {include "../clever/calendar.tpl" label=false time=false field="datetime_required_to" model="Filters"}
  {/if}
  {if $search !== false}
    {$form->input("search", array(label=false, style="width: 100px;"))}
  {/if}

  {if $status}
    {$form->input('Status', array(label=false, empty="[Status]" options=$statuses, style="width: 100px;"))}
  {/}

  {$form->end()}
</div>

<script type="text/javascript" charset="utf-8">
  jQuery(function() {
    jQuery('#filters form *').change(function() {
      clearInterval(reloadInterval);
    });

    jQuery('#calendardatetime_required_to, #calendardatetime_required_from').datepicker('option', {
      beforeShow: customRange,
      onClose: function(dateText, input) {
        if (jQuery('#calendardatetime_required_from').val().length > 0 && jQuery('#calendardatetime_required_to').val().length > 0) {
          new Ajax.Updater('content','/{$params.controller}/search{if $searchurlextra}/{$searchurlextra}{/}', {
            asynchronous:true,
            evalScripts:true,
            parameters:Form.serialize('filterform'),
            requestHeaders:['X-Update', 'content']
          });
        }
      }
    });
  });

  function customRange(input) {
    if (input.id == 'calendardatetime_required_to') {
      return {
        minDate: jQuery('#calendardatetime_required_from').datepicker("getDate")
      };
    } else if (input.id == 'calendardatetime_required_from') {
      return {
        maxDate: jQuery('#calendardatetime_required_to').datepicker("getDate")
      };
    }
  }
</script>
{if $searchurlextra}
{$s="search/"|cat:$searchurlextra}
{$ajax->observeForm('filterform', array(url=$s, update="content"))}
{else}
{$ajax->observeForm('filterform', array(url="search", update="content"))}
{/}

{assign var=searchi value=$session->read($params.controller)}

{if ($searchi)}
  Viewing from {$searchi.from} to {$searchi.to}
{/if}

