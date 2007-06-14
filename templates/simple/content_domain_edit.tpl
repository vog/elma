            <div id="Content">
                {if $mode == "modify"}
                <h2>{t }Edit domain{/t} {$domain.dc.0}</h2>
                {else}
                <h2>{t}New domain{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form action="{$smarty.server.php_self}" method="post" onsubmit="markall()">
                    <div>
                        <input type="hidden" name="mode" value="{$mode}" />
                    </div>
			        {if $mode == "modify"}
			            <div>
                            <input type="hidden" name="dc" value="{$domain.dc.0}" />
                        </div>
			            <fieldset>
			                <legend>{$domain.dc.0}</legend>
			                <table>
			        {else}
            	        <fieldset>
			                <legend>{t}new domain{/t}</legend>
			                <table>
			                    <tr>
			                        <td>
                                        {t}Domain{/t}
                                    </td>
                                    <td>
                                        <input type="text" name="dc" />
                                    </td>
			                    </tr>
                    {/if}
			                <tr>
			                    <td>
                                    {t}Is active?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $domain.mailstatus.0 eq "FALSE"}{else}checked="checked"{/if} />
                                </td>
			                </tr>
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
