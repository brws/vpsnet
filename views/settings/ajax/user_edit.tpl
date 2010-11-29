<h3>User Details</h3>
{$form->create('User', array(url="/users/edit"))}
  {$form->input('User.id')}
  {$form->input('User.username', array(disabled="disabled"))}
  {$form->input('User.password', array(value=""))}
  {$form->input('User.firstname')}
  {$form->input('User.surname')}
  {$form->input('User.role_id', array(multiple=false))}
  {$form->input('User.department_id')}
  {$form->input('User.active', array(options=array('Not Active', 'Active')))}
{$form->end('Save')}
