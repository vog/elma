            <div id="Content">
                <h2>{t}Organize global admins{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form name="submitform" action="{$smarty.server.php_self}" method="post" onsubmit="markall()">
                    <fieldset>
                        <legend>{t}Lists{/t}</legend>
                        <table>
                            <tr>
                                <td colspan="2" id="sendcenter">
                                    <table>
                                        <td>
                                            {t}Admins{/t}
                                            <br>
                                            <select name="admins[]" size="5" multiple>
                                            {section name=adminloop loop=$admins}
                                                <option value="{$adminslong[adminloop]}">{$admins[adminloop]}</option>
                                            {/section}
                                            </select>
                                            <br>
                                            <input type="button" name="delfromlist" value="delfromlist" onClick="del()">
                                        </td>
                                        <td>
                                            {t}Non-Admins{/t}
                                            <br>
                                            <select name="nonadmins[]" size="5" multiple>
                                            {section name=nonadminloop loop=$nonadmins}
                                                <option value="{$nonadminslong[nonadminloop]}">{$nonadmins[nonadminloop]}</option>
                                            {/section}
                                            </select>
                                            <br>
                                            <input type="button" name="addtolist" value="addtolist" onclick="add()">
                                        </td>
                                    </table>
                                </td>
                            </tr>
			                <tr>
                                <td colspan="2" id="sendcenter"><input type="submit" name="submit" value="{t}Save{/t}" /></td>
			                </tr>
	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
