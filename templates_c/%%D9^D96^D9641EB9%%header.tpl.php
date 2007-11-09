<?php /* Smarty version 2.6.14, created on 2007-09-18 16:04:11
         compiled from designs/Andromeda/header.tpl */ ?>
		<div id="nav">
				<div>Aktueller Planet</div>
				<?php echo $this->_tpl_vars['selectField']; ?>

				
				<?php unset($this->_sections['id']);
$this->_sections['id']['name'] = 'id';
$this->_sections['id']['loop'] = is_array($_loop=$this->_tpl_vars['gameNav']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['id']['show'] = true;
$this->_sections['id']['max'] = $this->_sections['id']['loop'];
$this->_sections['id']['step'] = 1;
$this->_sections['id']['start'] = $this->_sections['id']['step'] > 0 ? 0 : $this->_sections['id']['loop']-1;
if ($this->_sections['id']['show']) {
    $this->_sections['id']['total'] = $this->_sections['id']['loop'];
    if ($this->_sections['id']['total'] == 0)
        $this->_sections['id']['show'] = false;
} else
    $this->_sections['id']['total'] = 0;
if ($this->_sections['id']['show']):

            for ($this->_sections['id']['index'] = $this->_sections['id']['start'], $this->_sections['id']['iteration'] = 1;
                 $this->_sections['id']['iteration'] <= $this->_sections['id']['total'];
                 $this->_sections['id']['index'] += $this->_sections['id']['step'], $this->_sections['id']['iteration']++):
$this->_sections['id']['rownum'] = $this->_sections['id']['iteration'];
$this->_sections['id']['index_prev'] = $this->_sections['id']['index'] - $this->_sections['id']['step'];
$this->_sections['id']['index_next'] = $this->_sections['id']['index'] + $this->_sections['id']['step'];
$this->_sections['id']['first']      = ($this->_sections['id']['iteration'] == 1);
$this->_sections['id']['last']       = ($this->_sections['id']['iteration'] == $this->_sections['id']['total']);
?>
				<?php echo '<div>';  echo $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['cat'];  echo '</div>';  unset($this->_sections['iid']);
$this->_sections['iid']['name'] = 'iid';
$this->_sections['iid']['loop'] = is_array($_loop=$this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['iid']['show'] = true;
$this->_sections['iid']['max'] = $this->_sections['iid']['loop'];
$this->_sections['iid']['step'] = 1;
$this->_sections['iid']['start'] = $this->_sections['iid']['step'] > 0 ? 0 : $this->_sections['iid']['loop']-1;
if ($this->_sections['iid']['show']) {
    $this->_sections['iid']['total'] = $this->_sections['iid']['loop'];
    if ($this->_sections['iid']['total'] == 0)
        $this->_sections['iid']['show'] = false;
} else
    $this->_sections['iid']['total'] = 0;
if ($this->_sections['iid']['show']):

            for ($this->_sections['iid']['index'] = $this->_sections['iid']['start'], $this->_sections['iid']['iteration'] = 1;
                 $this->_sections['iid']['iteration'] <= $this->_sections['iid']['total'];
                 $this->_sections['iid']['index'] += $this->_sections['iid']['step'], $this->_sections['iid']['iteration']++):
$this->_sections['iid']['rownum'] = $this->_sections['iid']['iteration'];
$this->_sections['iid']['index_prev'] = $this->_sections['iid']['index'] - $this->_sections['iid']['step'];
$this->_sections['iid']['index_next'] = $this->_sections['iid']['index'] + $this->_sections['iid']['step'];
$this->_sections['iid']['first']      = ($this->_sections['iid']['iteration'] == 1);
$this->_sections['iid']['last']       = ($this->_sections['iid']['iteration'] == $this->_sections['iid']['total']);
 echo '';  echo '';  if ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Buddylist' && $this->_tpl_vars['buddys'] > 0):  echo '<a href="';  echo $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['url'];  echo '" style="color:#0f0">';  elseif ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Flotten' && $this->_tpl_vars['fleetAttack'] > 0):  echo '<a href="';  echo $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['url'];  echo '" style="color:#f00">';  elseif ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Nachrichten' && $this->_tpl_vars['messages'] > 0):  echo '<a href="';  echo $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['url'];  echo '" style="color:#0f0">';  elseif ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Notizen' && $this->_tpl_vars['noteBox'] == true):  echo '<a href="javascript:;" onclick="window.open(\'show.php?page=notepad\',\'notes\',\'status=no,width=800,height=600,scrollbars=yes\');">';  elseif ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Hilfe' && $this->_tpl_vars['helpBox'] == true):  echo '<a href="javascript:;" onclick="window.open(\'show.php?page=help\',\'help\',\'status=no,width=800,height=600,scrollbars=yes\');">';  else:  echo '<a href="';  echo $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['url'];  echo '">';  endif;  echo '';  echo $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'];  echo '';  if ($this->_tpl_vars['notes'] > 0 && $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Notizen'):  echo '&nbsp;(';  echo $this->_tpl_vars['notes'];  echo ' vorhanden)</a>';  elseif ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Flotten' && $this->_tpl_vars['fleetAttack'] > 0):  echo '&nbsp;(';  echo $this->_tpl_vars['fleetAttack'];  echo ' fremde)</a>';  elseif ($this->_tpl_vars['buddys'] > 0 && $this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Buddylist'):  echo '(';  echo $this->_tpl_vars['buddys'];  echo ' online)';  elseif ($this->_tpl_vars['gameNav'][$this->_sections['id']['index']]['items'][$this->_sections['iid']['index']]['name'] == 'Nachrichten' && $this->_tpl_vars['messages'] > 0):  echo '';  if ($this->_tpl_vars['blinkMessages'] == true):  echo '&nbsp;<blink>(';  echo $this->_tpl_vars['messages'];  echo ' neu)</blink>';  else:  echo '&nbsp;(';  echo $this->_tpl_vars['messages'];  echo ' neu)';  endif;  echo '';  endif;  echo '</a>';  echo '';  endfor; endif;  echo ''; ?>

				<?php endfor; endif; ?>	
		</div>		
		
		<div id="content">
	