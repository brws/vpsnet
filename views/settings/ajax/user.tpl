<h3>User Details</h3>
{$form->create('User')}
  {$form->input('User.password', array(value=""))}
  {$form->input('User.password_confirm', array(type="password", value=""))}
  {$form->input('User.firstname')}
  {$form->input('User.surname')}
  {$form->input('User.role_id', array(multiple=false))}
  {$form->input('User.department_id')}
  {$form->input('User.active', array(value=1, options=array('Not Active', 'Active')))}
{$form->end('Save')}
