            <div id="Content">
                <h2>{t}Edit User{/t}</h2>
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
			            <legend>{$user.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$user.uid.0}" />
                                </td>
				            </tr>
			        {else}
			            <legend>{t}new User{/t}</legend>
			            <table>
			                <tr>
				                <td>
                                    {t}User{/t}
                                </td>
                                <td>
                                    <input type="text" name="uid" value="{$user.uid.0}" />@{$domain}
                                </td>
				            </tr>
                    {/if}
		                    <tr>
				                <td>
                                    {t}First Name{/t}
                                </td>
                                <td>
                                    <input type="text" name="cn" value="{$user.cn.0}" />
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}Last Name{/t}
                                </td>
                                <td>
                                    <input type="text" name="sn" value="{$user.sn.0}" />
                                </td>
                            </tr>
			                <tr>
				                <td>
                                    {t}Status{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $user.mailstatus.0 eq "FALSE"}{else}checked="checked"{/if} />
                                </td>
				            </tr>
				            <tr>
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
				            <tr>
                                <td>
                                    {t}Password{/t}
                                </td>
                                <td>
                                    <input type="text" name="clearpassword" value="{$user.clearpassword.0}" />
                                </td>
                            </tr>
				            <tr>
			                    <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="submit" value="{t}Save{/t}"/>
                                </td>
				            </tr>
 	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
