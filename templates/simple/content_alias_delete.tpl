            <div id="Content">
                <h2>{t}Delete Alias{/t}</h2>
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
			            <legend>{$alias.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    {t 1=$alias.uid.0 2=$domain}Are you sure you want to delete alias %1@%2?{/t}
                                    <input type="hidden" name="uid" value="{$alias.uid.0}" />
                                    <input type="hidden" name="domain" value="{$domain}" />
                                </td>
				            </tr>
				            <tr>
			                    <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="submit" value="{t}Yes{/t}"/>
                                    <input type="reset" name="reset" value="{t}No{/t}"/>
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