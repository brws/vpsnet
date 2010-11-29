<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
  "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    {$this->Html->charset()}
    {$this->Html->meta('icon')}
    {$this->Html->css('cake.login')}{if $scripts_for_layout}
{$scripts_for_layout}{/}
    {$html->script('jquery-1.4.2.min')}
    <title>VPS Net - {$title_for_layout}</title>
  </head>
  <body>
    <div id="container">
      <div id="header">
        <img src="/img/biglogo.png" style="float: left;" />
      </div>
      <div id="content">
        {$content_for_layout}
      </div>
      <div id="footer">
        {$this->element('sql_dump')}
      </div>
    </div>
  </body>
</html>

