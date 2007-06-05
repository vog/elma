            <div id="Content">
                {if $mode == "modify"}
                <h2>{t}Edit systemuser{/t} {$systemuser.uid.0}</h2>
                {else}
                <h2>{t}New systemuser{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.php_self}" method="post">
			        <div>
                        <input type="hidden" name="mode" value="{$mode}"/>
                    </div>
			        <fieldset>
			        {if $mode == "modify"}
			            <legend>{$systemuser.uid.0}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$systemuser.uid.0}" />
                                </td>
				            </tr>
			        {else}
			            <legend>{t}new user{/t}</legend>
			            <table>
			                <tr>
				                <td>
                                    {t}Username{/t}
                                </td>
                                <td>
                                    <input type="text" name="uid" value="{$systemuser.uid.0}" />
                                </td>
				            </tr>
                    {/if}
		                    <tr>
				                <td>
                                    {t}First name{/t}
                                </td>
                                <td>
                                    <input type="text" name="cn" value="{$systemuser.cn.0}" />
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}Last name{/t}
                                </td>
                                <td>
                                    <input type="text" name="sn" value="{$systemuser.sn.0}" />
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
                                    <input type="text" name="clearpassword" value="{$systemuser.clearpassword.0}" />
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
