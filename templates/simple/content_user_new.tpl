            <div id="Content">
                <h2>{t}New user{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.php_self}" method="post">
			        <fieldset>
			            <legend>{t}new user{/t}</legend>
			            <table>
			                <tr>
				                <td>
                                    {t}Username{/t}
                                </td>
                                <td>
                                    <input type="text" name="uid" value="{$user.uid.0}" />@{$domain}
                                </td>
				            </tr>
		                    <tr>
				                <td>
                                    {t}First name{/t}
                                </td>
                                <td>
                                    <input type="text" name="cn" value="{$user.cn.0}" />
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}Last name{/t}
                                </td>
                                <td>
                                    <input type="text" name="sn" value="{$user.sn.0}" />
                                </td>
                            </tr>
			                <tr>
				                <td>
                                    {t}Is active?{/t}
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
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
				            <tr>
                                <td colspan="2" class="buttons">
                                    <input type="submit" name="submit" value="{t}Save{/t}" id="button"/>
                                </td>
				            </tr>
 	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
