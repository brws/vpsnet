{foreach from=$messages item=message}
  <div class="messages"><span class="from">{$message.User.firstname} {$message.User.surname}</span> <span class="date">{$message.Message.created}</span>
    <div class="text">{$message.Message.message}</div>
  </div>
{else}
  No messages
{/}