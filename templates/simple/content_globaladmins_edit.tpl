            <div id="Content">
                <h2>{t}Organize global admins{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form action="{$smarty.server.php_self}" method="post" onsubmit="markall()">
                    <fieldset>
                        <legend>{t}Lists{/t}</legend>
                        <table>
                            <tr>
                                <td colspan="2" class="sendcenter">
                                    <table>
                                        <tr>
                                            <td>
                                                {t}Admins{/t}
                                                <br />
                                                <select name="admins[]" size="8" multiple="multiple">
                                                {foreach from=$admins item=admin}
                                                    <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
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
                                                <select name="nonadmins[]" size="8" multiple="multiple">
                                                {foreach from=$nonadmins item=nonadmin}
                                                    <option value="{$nonadmin.dn}">{$nonadmin.uid[0]} ({$nonadmin.cn[0]} {$nonadmin.sn[0]})</option>
                                                {/foreach}
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
			                <tr>
                                <td colspan="2" class="sendcenter"><input type="submit" name="submit" value="{t}Save{/t}" /></td>
			                </tr>
	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
