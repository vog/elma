                <div id="Content">
                    <h2>{t}Welcome to your Exim LDAP Mail Administrator Frontend{/t}</h2>
		    <p>{t}Click on the Domains link at the left navigation bar to get an overview of your email domains. When you click on the domain name you will see all users and aliases for that domain. Everything else should be pretty self explaining. Have fun ;-){/t}</p>
		    {if $userclass != "user"}
		    <h3>{t}Statistics{/t}</h3>
		    <table>
		    	<tr><td>{t}Domains{/t}</td><td>{$domainCount} ({$domainCountActive} {t}active{/t})</td></tr>
		    	<tr><td>{t}Users{/t}</td><td>{$userCountOverall} ({$userCountActive} {t}active{/t})</td></tr>
		    	<tr><td>{t}Aliases{/t}</td><td> {$aliasCountOverall} ({$aliasCountActive} {t}active{/t})</td></tr>
		    </table>
		    {/if}
                </div>

