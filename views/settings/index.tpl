<style type="text/css" media="screen">

</style>
<div class="business details">
  <h3>Business Details</h3>
  {if $role->atleast($role->DEALER_ADMIN)}
    {$form->create('Location')}
    {$form->input('Location.id')}
    {$form->input('Location.name')}
    {$form->input('Location.addressline1')}
    {$form->input('Location.addressline2')}
    {$form->input('Location.addressline3')}
    {$form->input('Location.postcode')}
    {$form->input('Location.tel')}
    {$form->input('Location.fax')}
    {$form->input('Location.contact_name')}
    {$form->input('Location.contact_tel')}
    {$form->input('Location.group', array(type="text"))}
    {$form->end('Save Business Details')}
    {if $role->atleast($role->VALET_ADMIN)}
      <p style="text-align: right"><button onclick="location.href='/workorders/setup';">Configure Business Options</button></p><br />
    {/}
    {if $role->atleast($role->SUPER)}
      <p style="text-align: right"><button onclick="location.href='/workorders/global_setup';">Configure Global Business Options</button></p>
    {/}
  {else}
    <div class="input text">
      <strong>{$data.Location.name}</strong><br />
      {$data.Location.addressline1}<br />
      {$data.Location.addressline2}<br />
      {$data.Location.addressline3}<br />
      {$data.Location.postcode}<br />
      {$data.Location.tel}<br />
      {$data.Location.fax}<br />
      <br /><strong>Contact</strong><br />
      {$data.Location.contact_name}<br />
      {$data.Location.contact_tel}
    </div>
  {/}
</div>

<div id="userform" class="user details">
  <h3>User Details</h3>
  {$form->create('User', array(url="/users/edit"))}
    {$form->input('User.id')}
    {$form->input('User.username', array(disabled="disabled"))}
    {$form->input('User.password', array(value=""))}
    {$form->input('User.firstname')}
    {$form->input('User.surname')}
    {if $role->atleast($role->VALET_ADMIN)}
      {$form->input('User.role_id', array(multiple=false, options=$roles))}
    {/}
    {if $role->atleast($role->DEALER_ADMIN)}
      {$form->input('User.department_id', options=$departments)}
      {$form->input('User.active', array(options=array('Not Active', 'Active')))}
    {/}
  {$form->end('Save')}
</div>
{if $role->atleast($role->DEALER_ADMIN)}
<div class="user details">
  <h3>Current Users</h3>
  {$form->button('Add New User', array(id="AddUser"))}

  <div class="input checkbox" style="float: left;">
    <input type="hidden" name="data[deactivated]" value="0" />
    {$form->checkbox('deactivated', array(id="deac", value=$data['deactivated']))}
    {$ajax->observeField('deac', array(url="index", update="content"))}
    <label>Show Deactivated Users</label>
  </div>

  <table style="width: 100%; margin-top: 20px;" border="0" cellspacing="1" cellpadding="5">
    <tr><th>Username</th><th>Name</th><th>Access</th><th>Active</th></tr>
    {foreach from=$users item=user}
      <tr onclick="loadForm({$user.User.id});" style="cursor: pointer">
        <td>{$user.User.username}</td>
        <td>{ucwords $user.User.firstname} {ucwords $user.User.surname}</td>
        <td>{$user.Role.name}</td>
        <td>{$user.User.active}</td>
      </tr>
    {/foreach}
  </table>
</div>

<script type="text/javascript" charset="utf-8">
  jQuery(function() {
    jQuery('#AddUser').click(function() {
      new Ajax.Updater('userform', '/settings/add', {
        asynchronous:true, evalScripts:true, parameters:null, requestHeaders:['X-Update', 'userform']
      });
    });
  });

  function loadForm(uid) {
    new Ajax.Updater('userform', '/settings/edit/' + uid, {
      asynchronous:true, evalScripts:true, parameters:null, requestHeaders:['X-Update', 'userform']
    });
  }
</script>

{/}

{$this->Js->writeBuffer()}

