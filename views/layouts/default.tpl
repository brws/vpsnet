<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    {$html->charset()}
    {$html->meta('icon')}
    {$html->css('cake.generic')}{if $scripts_for_layout}
{$scripts_for_layout}{/}
    {$html->script('jquery-1.4.2.min')}
    {$html->script('jquery-ui-1.8.1.custom.min')}
    {$html->css('custom-theme/jquery-ui-1.8.1.custom')}
    <script type="text/javascript" charset="utf-8">
      $.noConflict();
    </script>
    {$html->script('prototype')}
    {$html->script('scriptaculous')}
    <title>VPS Net - {$title_for_layout}</title>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <img src="/img/logo.png" style="float: left;" />
        <div id="user">
          <span>Logged in as :<br /> {default $user->getUser() "Nobody"} ({default $user->getLocation() "Nowhere"}) {$user->logout()}</span>
        </div>
        <div id="nav">
          <span>
            <a href="/" title="Active Jobs"><img src="/img/active_jobs.png" /></a>
            {if $role->atleast($role->VALET_ADMIN)}
            <a href="/workorders/add_service" title="Add Service Job"><img src="/img/service.png" /></a>
            {/}
            <a href="/workorders/add" title="Add Job"><img src="/img/add_job.png" /></a>
            <a href="/overview" title="Reports"><img src="/img/reports.png" /></a>
            <a href="/settings" title="Settings"><img src="/img/settings.png" /></a>
          </span>
        </div>
        <div id="summary">
          <span>
            {default $location->getJobsCountNow() 0} Job(s) in progress<br />
            {default $location->getJobsCountToday() 0} Job(s) left today<br />
            {default $location->getJobsCountTomorrow() 0} Job(s) tomorrow<br />
          </span>
        </div>
      </div>
        <script type="text/javascript">
        var reloadInterval;
        jQuery(function startInterval() {
          if (location.pathname=='/') {
            reloadInterval = setInterval(function() {
              try {
                location.reload(true);
              } catch (e) {
                console.log('error');
                console.log(e);
              }
            }, 60000);
          }
        });
        </script>
      <div id="content">
        {$session->flash()}
        {$content_for_layout}
      </div>
      <div id="footer">
        {$this->element('sql_dump')}
      </div>
    </div>
  </body>
</html>

