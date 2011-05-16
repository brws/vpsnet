<!doctype>
<html>
  <head>
    {$html->charset()}
    {$html->meta('icon')}
    <title>{$title_for_layout}</title>
    <style type="text/css" media="all">
      * {
        font-family: 'Droid Sans', 'Calibri', 'Trebuchet MS', 'Arial', sans-serif;
        font-size: 8pt;
      }

      table {
        width: 21cm;
        border-collapse: collapse;
      }

      caption {
        font-size: 10pt;
        font-weight: bold;
      }

      table th {
        background: #ccc;
        border: 1px solid #000;
      }

      table td {
        border-left: 1px solid #ccc;
        border-bottom: 1px solid #ccc;
      }

      table td.right {
        border-right: 1px solid #ccc;
        text-align: right;
      }
      
      table td.money {
        text-align: right;
      }
    </style>
  </head>
  <body>
    {if $role->atleast($role->VALET_ADMIN)}
      {$content_for_layout}
    {else}
      You are not authorised to access this system.
    {/}
  </body>
</html>