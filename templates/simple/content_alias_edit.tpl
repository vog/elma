            <div id="Content">
                <h2>{t}Edit Alias{/t}</h2>
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
                {/if}
		        <form action="{$smarty.server.php_self}" method="post">
			        <div>
                        <input type="hidden" name="mode" value="{$mode}"/>
                    </div>
			        <fieldset>
			        {if $mode == "modify"}
			            <legend>{$alias.uid.0}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$alias.uid.0}" />
                                </td>
				            </tr>
			        {else}
			            <legend>{t}new Alias{/t}</legend>
			            <table>
			                <tr>
				                <td>
                                    {t}Alias{/t}
                                </td>
                                <td>
                                    <input type="text" name="uid" value="{$alias.uid.0}" />@{$domain}
                                </td>
				            </tr>
                    {/if}
                            <tr>
                                <td>
                                    {t}Alias for{/t}
                                </td>
                                <td>
                                    {strip}
                                        <textarea name="nlo_mailaliasedname" cols="40" rows="10">
                                            {section name=mailaliasedname_sec loop=$alias.mailaliasedname}
                                                {$alias.mailaliasedname[mailaliasedname_sec]}
                                            {/section}
                                        </textarea>
                                    {/strip}
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
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
