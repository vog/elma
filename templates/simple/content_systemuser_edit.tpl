            <div id="Content">
                {if $mode == "modify"}
                <h2>{t}Edit systemuser{/t} {$user.uid.0}</h2>
                {else}
                <h2>{t}New systemuser{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.php_self}" method="post" onsubmit="markall()">
			        <div>
                        <input type="hidden" name="mode" value="{$mode}"/>
                    </div>
			        <fieldset>
			        {if $mode == "modify"}
			            <legend>{$user.uid.0}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$user.uid.0}" />
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
                                    <input type="text" name="uid" value="{$user.uid.0}" />
                                </td>
				            </tr>
                    {/if}
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
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
				            <tr>
                                <td>
                                    {t}Password{/t}
                                    {if $mode == "modify"}
                                    <br />
                                    {t}(leave empty to keep password){/t}
                                    {/if}
                                </td>
                                <td>
                                    <input type="hidden" name="userpassword" value="{$user.userpassword.0}" />
                                    <input type="text" name="clearpassword" value="{$user.clearpassword.0}" />
                                </td>
                            </tr>
                            {if $isadmin == true}
                            <tr>
                                <td colspan="2" class="sendcenter">
                                    <table>
                                        <tr>
                                            <td>
                                                {t}Admins{/t}
                                                <br />
                                                <select name="domainsin[]" size="8" multiple="multiple">
                                                {foreach from=$domainsin item=domain}
                                                    <option value="{$domain}">{$domain}</option>
                                                {/foreach}
                                                </select>
                                            </td>
                                            <td>
                                                <div>
                                                    <br />
                                                    <br />
                                                    <input type="button" name="delfromlist" value="&gt;" onclick="del()" />
                                                    <br />
                                                    <br />
                                                    <input type="button" name="addtolist" value="&lt;" onclick="add()" />
                                                </div>
                                            </td>
                                            <td>
                                                {t}Non-Admins{/t}
                                                <br />
                                                <select name="domains[]" size="8" multiple="multiple">
                                                {foreach from=$domains item=domain}
                                                    <option value="{$domain}">{$domain}</option>
                                                {/foreach}
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            {/if}
				            <tr>
                                <td colspan="2" class="sendcenter">
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
