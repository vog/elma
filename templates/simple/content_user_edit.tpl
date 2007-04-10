                <div id="Content">
                    <h2>{t}Edit User{/t}</h2>
		        {if $smarty.post.submit}{if $submit_status == "0"}<div>{t}Data saved successfully.{/t}</div>{else}<div>{t}Sorry, your data could not be saved. The following LDAP error occured:{/t} {$submit_status}</div>{/if}{/if}
		        <form action="{$smarty.server.php_self}" method="post" name="user_edit">
			<input type="hidden" name="mode" value="{$mode}">

			{if $mode == "modify"}
			<input type="hidden" name="dc" value="{$user.uid.0}">
			<fieldset>
			    <legend>{$user.uid.0}</legend>
			    <table id="Content">
			        <tr>
				    <td>{t}Status{/t}</td><td><input type="checkbox" name="mailstatus" {if $user.mailstatus.0 eq "TRUE"}checked{/if}></td>
				</tr>
				</tr>
			            <td>&nbsp;</td><td><input type="submit" name="submit"></td>
				</tr>
	                    </table>
                        </fieldset>
			{else}
                	<fieldset>
			    <legend>{t}new User{/t}</legend>
			    <table id="Content">
			        <tr>
				    <td>{t}User{/t}</td><td><input type="text" name="uid"></td>
				</tr>
			        <tr>
				    <td>{t}Status{/t}</td><td><input type="checkbox" name="mailstatus" checked></td>
				</tr>
				</tr>
			            <td>&nbsp;</td><td><input type="submit" name="submit"></td>
				</tr>
	                    </table>
                        </fieldset>
                        {/if}
                 

			</form>
                </div>

