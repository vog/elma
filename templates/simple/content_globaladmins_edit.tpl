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
                                                {section name=adminloop loop=$admins}
                                                    <option value="{$adminslong[adminloop]}">{$admins[adminloop]} ({$adminscn[adminloop]} {$adminssn[adminloop]})</option>
                                                {/section}
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
                                                {section name=nonadminloop loop=$nonadmins}
                                                    <option value="{$nonadminslong[nonadminloop]}">{$nonadmins[nonadminloop]} ({$nonadminscn[nonadminloop]} {$nonadminssn[nonadminloop]})</option>
                                                {/section}
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
