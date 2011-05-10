<h1 class="login">Please Login</h1>
{$session->flash('auth')}
{$form->create('User', array(action="login"))}
{if count($users) > 0}
    <div class="input select selectuser">
    <label>Username</label>
    <select style="width: 100%; padding: 10px;" id="UserUsername" name="data[User][username]">
        {foreach from=$users item=user}
          <option {if $user.User.username=="sd2"}selected="selected"{/} value="{$user.User.username}">{ucwords $user.User.firstname} {ucwords $user.User.surname}</option>
        {/}
    </select>
    </div>
    {if $dealer == 'admin'}
      {$form->input('locations', array(options=$locations style="width: 100%; padding: 10px;"))}
    {/}
{else}
    <div class="manualuser">
        {$form->input('username')}
    </div>
{/}
{$form->input('password')}
{$form->end('Login Here')}
<div class="login"></div>
<strong>Contact VPS</strong>: 02920 369 836

