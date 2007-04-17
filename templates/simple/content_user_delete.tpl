            <div id="Content">
                <h2>{t}Delete User{/t}</h2>
		        {if $smarty.post.submit}
                    {if $submit_status == "0"}
                        <div>
                            {t}Data saved successfully.{/t}
                        </div>
                    {else}
                        <div>
                            {t}Sorry, your data could not be saved. The following LDAP error occured:{/t} {$submit_status}
                        </div>
                    {/if}
                {else}
		        <form action="{$smarty.server.php_self}" method="post">
			        <fieldset>
			            <legend>{$user.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    Are you sure you want to delete user {$user.uid.0}@{$domain}?
                                    <input type="hidden" name="uid" value="{$user.uid.0}" />
                                    <input type="hidden" name="domain" value="{$domain}" />
                                </td>
				            </tr>
				            <tr>
			                    <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="submit" />
                                </td>
				            </tr>
 	                    </table>
                    </fieldset>
			    </form>
                {/if}
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
