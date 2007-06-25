            <div id="Content">
                {if $mode == "modify"}
                <h2>{t }Edit domain{/t} {$domain.dc.0}</h2>
                {else}
                <h2>{t}New domain{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form action="{$smarty.server.php_self}" method="post" onsubmit="markAllAdmins()">
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
                                <td colspan="2"> 
                                    <hr />
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    ACL
                                </td>
                                <td>
                                    <table>
                                        <tr>
                                            <td>
                                                {t}Administrators{/t}
                                                <br />
                                                <select name="admins[]" size="8" multiple="multiple">
                                                    {if $notnulladmins.sysUser == 1}
                                                    <optgroup label="{t}Systemusers{/t}">
                                                    {foreach from=$admins item=admin}
                                                        {if $admin.mailUser == 0}
                                                        <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    {/if}
                                                    {if $notnulladmins.mailUser == 1}
                                                    <optgroup label="{t}Domainsusers{/t}">
                                                    {foreach from=$admins item=admin}
                                                        {if $admin.mailUser == 1}
                                                        <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    {/if}
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
                                                    {if $notnullnonadmins.sysUser == 1}
                                                    <optgroup label="{t}Systemusers{/t}">
                                                    {foreach from=$nonadmins item=nonadmin}
                                                        {if $nonadmin.mailUser == 0}
                                                        <option value="{$nonadmin.dn}">{$nonadmin.uid[0]} ({$nonadmin.cn[0]} {$nonadmin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    {/if}
                                                    {if $notnullnonadmins.mailUser == 1}
                                                    <optgroup label="{t}Domainusers{/t}">
                                                    {foreach from=$nonadmins item=nonadmin}
                                                        {if $nonadmin.mailUser == 1}
                                                        <option value="{$nonadmin.dn}">{$nonadmin.uid[0]} ({$nonadmin.cn[0]} {$nonadmin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    {/if}
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
			                <tr>
                                <td colspan="2" class="buttons"><input type="submit" name="submit" value="{t}Save{/t}" /></td>
			                </tr>
	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
