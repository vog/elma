            <div id="Content">
                <h2>{t}New domain{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form action="{$smarty.server.php_self}" method="post" onsubmit="markAllAdmins()">
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
			                <tr>
			                    <td>
                                    {t}Is active?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $domain.mailstatus.0 eq "TRUE"}checked="checked"{/if} />
                                </td>
			                </tr>
                            {if @in_array("domain_new.mailstorageserver",$acl) }
                            <tr>
                                <td>
                                    {t}Mailstorageserver{/t}
                                </td>
                                <td>
                                    <select name="mailstorageserver">
                                    {foreach from=$mailstorageservers item=server}
                                        <option {if $domain.mailstorageserver.0 eq $server}selected{/if}>{$server}</option>
                                    {/foreach}       
                                    </select>
                                </td>
                            </tr>
                            {/if}
                            <tr>
                                <td colspan="2"> 
                                    <hr />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"> 
                                    ACL
                                    <table class="inside">
                                        <tr>
                                            <td>
                                                {t}Administrators{/t}
                                            </td>
                                            <td>
                                            </td>
                                            <td>
                                                {t}available users{/t}
                                            </td>
                                        <tr/>
                                        <tr>
                                            <td>
                                                <select name="admins[]" size="8" multiple="multiple">
                                                    <optgroup label="{t}Systemusers{/t}">
                                                    {foreach from=$admins item=admin}
                                                        {if ! @in_array("mailUser",$admin.objectclass) }
                                                        <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    <optgroup label="{t}Domainsusers{/t}">
                                                    {foreach from=$admins item=admin}
                                                        {if @in_array("mailUser",$admin.objectclass) }
                                                        <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="button" name="delfromlist" value="&gt;" onclick="delAdmin()" /><br />
                                                <input type="button" name="addtolist" value="&lt;" onclick="addAdmin()" />
                                            </td>
                                            <td>
                                                <select name="nonadmins[]" size="8" multiple="multiple">
                                                    <optgroup label="{t}Systemusers{/t}">
                                                    {foreach from=$nonadmins item=nonadmin}
                                                        {if ! @in_array("mailUser",$nonadmin.objectclass) }
                                                        <option value="{$nonadmin.dn}">{$nonadmin.uid[0]} ({$nonadmin.cn[0]} {$nonadmin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    <optgroup label="{t}Domainusers{/t}">
                                                    {foreach from=$nonadmins item=nonadmin}
                                                        {if @in_array("mailUser",$nonadmin.objectclass) }
                                                        <option value="{$nonadmin.dn}">{$nonadmin.uid[0]} ({$nonadmin.cn[0]} {$nonadmin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
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
                                <td valign="top">
                                    {t}Next Step{/t}
                                 </td>
                                 <td>
                                    <input type="radio" name="nlo_next_step" value="add_another" checked="true" /> {t}Add another domain{/t}<br />
                                    <input type="radio" name="nlo_next_step" value="show_domain_list" /> {t}Go to domain list{/t}<br />
                                    <input type="radio" name="nlo_next_step" value="show_overview" /> {t}Edit newly created domain{/t}<br />
                                 </td>
                            </tr>

                            <tr>
                                <td colspan="2"> 
                                    <hr />
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
