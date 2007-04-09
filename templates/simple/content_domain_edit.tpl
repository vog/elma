                <div id="Content">
                    <h2>{t}Edit Domain{/t}</h2>
		        {if $smarty.post.submit}{if $submit_status == "0"}<div>{t}Data saved successfully.{/t}</div>{else}<div>{t}Sorry, your data could not be saved. The following LDAP error occured:{/t} {$submit_status}</div>{/if}{/if}
		        <form action="{$smarty.server.php_self}" method="post" name="domain_edit">
			<input type="hidden" name="mode" value="{$mode}">
			<input type="hidden" name="dc" value="{$domain.dc.0}">
			<fieldset>
			    <legend>{$domain.dc.0}</legend>
			    <table id="Content">
			        <tr>
				    <td>{t}Status{/t}</td><td><input type="checkbox" name="mailstatus" {if $domain.mailstatus.0 eq "TRUE"}checked{/if}></td>
				</tr>
				</tr>
			            <td>&nbsp;</td><td><input type="submit" name="submit"></td>
				</tr>
	                    </table>
			</form>
                </div>

