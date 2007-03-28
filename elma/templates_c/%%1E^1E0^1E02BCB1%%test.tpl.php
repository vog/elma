<?php /* Smarty version 2.6.9, created on 2007-03-26 15:54:36
         compiled from /var/www/elma/templates/test.tpl */ ?>
<table summary="" cellpadding=4 cellspacing=0 border=0 style="width:100%; background-color:#F0F0F0; border-style:solid; border-color:black; border-top-width:0px; border-bottom-width:1px; border-left-width:1px; border-right-width:1px;">
  <tr><th>Domain</th><th>Status</th><?php if ($this->_tpl_vars['acl']['maildomainmailstore']): ?><th>Postfachspeicher</th><?php endif;  if ($this->_tpl_vars['acl']['maildomainmaxaccounts']): ?><th>Max # Mailboxes</th><?php endif;  if ($this->_tpl_vars['acl']['mailvirusscanner']): ?><th>Virusscan</th><?php endif;  if ($this->_tpl_vars['acl']['deletedomain']): ?><th>&nbsp;</th><?php endif; ?></tr>
  <?php unset($this->_sections['domains_sec']);
$this->_sections['domains_sec']['name'] = 'domains_sec';
$this->_sections['domains_sec']['loop'] = is_array($_loop=$this->_tpl_vars['domains']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['domains_sec']['show'] = true;
$this->_sections['domains_sec']['max'] = $this->_sections['domains_sec']['loop'];
$this->_sections['domains_sec']['step'] = 1;
$this->_sections['domains_sec']['start'] = $this->_sections['domains_sec']['step'] > 0 ? 0 : $this->_sections['domains_sec']['loop']-1;
if ($this->_sections['domains_sec']['show']) {
    $this->_sections['domains_sec']['total'] = $this->_sections['domains_sec']['loop'];
    if ($this->_sections['domains_sec']['total'] == 0)
        $this->_sections['domains_sec']['show'] = false;
} else
    $this->_sections['domains_sec']['total'] = 0;
if ($this->_sections['domains_sec']['show']):

            for ($this->_sections['domains_sec']['index'] = $this->_sections['domains_sec']['start'], $this->_sections['domains_sec']['iteration'] = 1;
                 $this->_sections['domains_sec']['iteration'] <= $this->_sections['domains_sec']['total'];
                 $this->_sections['domains_sec']['index'] += $this->_sections['domains_sec']['step'], $this->_sections['domains_sec']['iteration']++):
$this->_sections['domains_sec']['rownum'] = $this->_sections['domains_sec']['iteration'];
$this->_sections['domains_sec']['index_prev'] = $this->_sections['domains_sec']['index'] - $this->_sections['domains_sec']['step'];
$this->_sections['domains_sec']['index_next'] = $this->_sections['domains_sec']['index'] + $this->_sections['domains_sec']['step'];
$this->_sections['domains_sec']['first']      = ($this->_sections['domains_sec']['iteration'] == 1);
$this->_sections['domains_sec']['last']       = ($this->_sections['domains_sec']['iteration'] == $this->_sections['domains_sec']['total']);
?>
    <?php echo '<tr><td><a href="main.php?item=domain&cmd=edit&domain=';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['dc'];  echo '">';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['dc'];  echo '</a></td><td>';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['mailstatus'];  echo '</td>';  if ($this->_tpl_vars['acl']['maildomainmailstore']):  echo '<td>';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['maildomainmailstore'];  echo '</td>';  endif;  echo '';  if ($this->_tpl_vars['acl']['maildomainmaxaccounts']):  echo '<td>';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['maildomainmaxaccounts'];  echo '</td>';  endif;  echo '';  if ($this->_tpl_vars['acl']['mailvirusscanner']):  echo '<td>';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['mailvirusscanner'];  echo '</td>';  endif;  echo '';  if ($this->_tpl_vars['acl']['deletedomain']):  echo '<td><a href="main.php?item=domains&cmd=delete&domain=';  echo $this->_tpl_vars['domains'][$this->_sections['domains_sec']['index']]['dc'];  echo '">Löschen</a></td>';  endif;  echo '</tr>'; ?>

  <?php endfor; endif; ?>
</table>
