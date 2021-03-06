             {validate id="dc" message="Domain not valid or empty" append="validation_errors"}

            <div id="Content">
                <h2>{t }Edit domain{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
                <form action="{$smarty.server.REQUEST_URI}" method="post" onsubmit="markAllAdmins()">
    	            <div>
                        <input type="hidden" name="dc" value="{$domain.dc.0}" />
                    </div>
		            <fieldset>
			            <legend>{$domain.dc.0}</legend>
			            <table>
			                <tr>
			                    <td>
                                    {t}Is active?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $domain.mailstatus.0 eq "TRUE"}checked="checked"{/if} />
                                </td>
			                </tr>
                            {if @in_array("domain_edit.mailstorageserver",$acl) }
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
			        <td>
                                    {t}Use Domain as Alias?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="nlo_maildomainalias" {if $maildomainaliassettings.STATUS eq "#"}{else}checked="checked"{/if} />
                                </td>
			    </tr>
	               	    <tr>
			        <td>
                                    {t}Targetdomain{/t}
                                </td>
                                <td>
                                    <input type="text" name="nlo_maildomainaliastarget" value="{$maildomainaliassettings.TARGETDOMAIN}" />
                                </td>
			    </tr>
                            <tr>
                                <td colspan="2">
                                    <hr />
                                </td>
                            </tr>
                            {if @in_array("domain_edit.spamfilter",$acl) }
                            <tr>
                                <td>
                                    {t}Spamfitler{/t}
                                </td>
                                <td>
                                    <select name="mailspamfilter">
                                        <option {if $domain.mailspamfilter.0 eq "FALSE"}selected{/if} value="FALSE">{t}disabled{/t}</option>
                                        <option {if $domain.mailspamfilter.0 eq "DISCARD"}selected{/if} value="DISCARD">{t}discard email{/t}</option>
<!--                                        <option {if $domain.mailspamfilter.0 eq "REDIRECT"}selected{/if} value="REDIRECT">{t}redirect to{/t} spam@{$domain.dc.0}</option>
                                        <option {if $domain.mailspamfilter.0 eq "FOLDER"}selected{/if} value="FOLDER">{t}save in user's spamfolder{/t}</option>-->
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
                                                <select name="admins[]" size="8" multiple="multiple" class="multiple">
                                                    <optgroup label="{t}Systemusers{/t}">
                                                    {foreach from=$admins item=admin}
                                                        {if ! @in_array("mailUser",$admin.objectclass) }
                                                        <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                    <optgroup label="{t}Domainusers{/t}">
                                                    {foreach from=$admins item=admin}
                                                        {if @in_array("mailUser",$admin.objectclass) }
                                                        <option value="{$admin.dn}">{$admin.uid[0]} ({$admin.cn[0]} {$admin.sn[0]})</option>
                                                        {/if}
                                                    {/foreach}
                                                    </optgroup>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="button" name="delfromlist" value="&gt;" onclick="delAdmin()" /><br /><input type="button" name="addtolist" value="&lt;" onclick="addAdmin()" />
                                            </td>
                                            <td>
                                                <select name="nonadmins[]" size="8" multiple="multiple" class="multiple">
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
