



<table summary="" cellpadding=4 cellspacing=0 border=0 style="width:100%; background-color:#F0F0F0; border-style:solid; border-color:black; border-top-width:0px; border-bottom-width:1px; border-left-width:1px; border-right-width:1px;">
  <tr><th>Domain</th><th>Status</th>{if $acl.maildomainmailstore}<th>Postfachspeicher</th>{/if}{if $acl.maildomainmaxaccounts}<th>Max # Mailboxes</th>{/if}{if $acl.mailvirusscanner}<th>Virusscan</th>{/if}{if $acl.deletedomain}<th>&nbsp;</th>{/if}</tr>
  {section name=domains_sec loop=$domains}
    {strip}
      <tr><td><a href="main.php?item=domain&cmd=edit&domain={$domains[domains_sec].dc}">{$domains[domains_sec].dc}</a></td><td>{$domains[domains_sec].mailstatus}</td>{if $acl.maildomainmailstore}<td>{$domains[domains_sec].maildomainmailstore}</td>{/if}{if $acl.maildomainmaxaccounts}<td>{$domains[domains_sec].maildomainmaxaccounts}</td>{/if}{if $acl.mailvirusscanner}<td>{$domains[domains_sec].mailvirusscanner}</td>{/if}{if $acl.deletedomain}<td><a href="main.php?item=domains&cmd=delete&domain={$domains[domains_sec].dc}">Löschen</a></td>{/if}</tr>
    {/strip}
  {/section}
</table>

