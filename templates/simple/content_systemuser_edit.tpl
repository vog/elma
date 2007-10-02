            <div id="Content">
                {if $mode == "modify"}
                <h2>{t}Edit systemuser{/t} {$user.uid.0}</h2>
                {else}
                <h2>{t}New systemuser{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.php_self}" method="post" onsubmit="markAllDomains()">
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
                                <td>
                                    {t}Password{/t}
                                    {if $mode == "modify"}
                                    <br />
                                    {/if}
                                </td>
                                <td>
                                    <input type="hidden" name="userpassword" value="{$user.userpassword.0}" />
                                    <input type="text" name="clearpassword" value="{$user.clearpassword.0}" />
                                    {t}(leave empty to keep password){/t}
                                </td>
                            </tr>
				            <tr>
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}ACL{/t}
                                </td>
                                <td class="center">
                                    <table>
                                        <tr>
                                            <td>
                                                {t}Administrator of{/t}
                                                <br />
                                                <select name="nlo_adminofdomains[]" size="8" multiple="multiple">
                                                {foreach from=$adminofdomains item=domain}
                                                    <option value="{$domain}">{$domain}</option>
                                                {/foreach}
                                                </select>
                                            </td>
                                            <td>
                                                <div>
                                                    <br />
                                                    <br />
                                                    <input type="button" name="delfromlist" value="&gt;" onclick="delDomain()" />
                                                    <br />
                                                    <br />
                                                    <input type="button" name="addtolist" value="&lt;" onclick="addDomain()" />
                                                </div>
                                            </td>
                                            <td>
                                                {t}Not administrator of{/t}
                                                <br />
                                                <select name="nlo_availabledomains[]" size="8" multiple="multiple">
                                                {foreach from=$availabledomains item=domain}
                                                    <option value="{$domain}">{$domain}</option>
                                                {/foreach}
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr/>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {t}Next Step{/t}
                                 </td>
                                 <td>
                                    <input type="radio" name="nlo_next_step" value="add_another" checked="true" /> {t}Add another user{/t}<br />
                                    <input type="radio" name="nlo_next_step" value="edit_current" /> {t}neuen Benutzer bearbeiten{/t}<br />
                                    <input type="radio" name="nlo_next_step" value="show_overview" /> {t}Go to user overview{/t}<br />
                                 </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr/>
                                </td>
                            </tr>
				            <tr>
                                <td colspan="2" class="buttons">
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
