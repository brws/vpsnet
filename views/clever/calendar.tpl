<div class="input date">
  {if $label!==false}
  {if isset($data[$model][$field][date])}
  {assign var=date value=$data[$model][$field][date]}
  {else}
  {assign var=date value=$data[$model][$field]}
  {/if}
  {if !isset($date) && isset($default_date)}
    {assign var=date value=strtotime($default_date)}
  {/if}
  <label for="{$field}">{default $label "Date"}</label>{/if}<input {if $disabled}disabled="disabled"{/if} class="date" id="calendar{$field}" type="text" name="data[{$model}][{$field}][date]" style="width: 100px" value="{date_format $date "%d/%m/%Y"}" />
</div>

{if $time !== false}

<div class="input date">
  <label for="{$field}">{default $label2 "Time"}</label>
  {assign var=hrs value=array("01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12)}
  {assign var=mins value=array("00", "01", "02", "03", "04", "05", "06", "07", "08", "09", 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59)}
  <div class="time">
    <select id="{$model}_hour" {if $disabled}disabled="disabled"{/if} name="data[{$model}][{$field}][hours]" class="hours" size="1">
      {foreach from=$hrs item=hr}
        {if !isset($date)}
        <option{if $hr == '09'} selected="selected"{/} value="{$hr}">{$hr}</option>
        {else}
          <option{if date_format($date, "%I") == $hr} selected="selected"{/} value="{$hr}">{$hr}</option>
        {/if}
      {/}
    </select>
    <span>:</span>
    <select id="{$model}_minute" {if $disabled}disabled="disabled"{/if} name="data[{$model}][{$field}][minutes]" class="minutes" size="1">
      {foreach from=$mins item=min}
        <option {if date_format($date, "%M") == $min} selected="selected"{/} value="{$min}">{$min}</option>
      {/}
    </select>
    <select id="{$model}_meridian" {if $disabled}disabled="disabled"{/if} name="data[{$model}][{$field}][am]" class="minutes" size="1">
      <option {if date_format($date, "%P") == "am"} selected="selected"{/} value="am">AM</option>
      <option {if date_format($date, "%P") == "pm"} selected="selected"{/}value="pm">PM</option>
    </select>
  </div>
</div>

{/if}

<script type="text/javascript" charset="utf-8">
  jQuery(function($) {
     $('#calendar{$field}').datepicker({ dateFormat: 'dd/mm/yy', showAnim: '' });
  });
</script>

