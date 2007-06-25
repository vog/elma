            <div id="Content">
                <h2>{t}Systemadminstrators{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form action="{$smarty.server.php_self}" method="post" onsubmit="markall()">
                    <table>
                        <tr>
                            <td>
                                <table class="center">
                                    <tr>
                                        <td>
                                            {t}Administrators{/t}
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
                                                <input type="button" name="delfromlist" value="&gt;" onclick="delAdmin()" />
                                                <br />
                                                <br />
                                                <input type="button" name="addtolist" value="&lt;" onclick="addAdmin()" />
                                            </div>
                                        </td>
                                        <td>
                                            {t}available users{/t}
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
                            <td class="buttons">
                                <input type="submit" name="submit" value="{t}Save{/t}" />
                            </td>
			            </tr>
	                </table>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
