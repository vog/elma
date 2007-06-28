            <div id="Content">
                {if $mode == "modify"}
                <h2>{t}Edit alias{/t} {$alias.uid.0}@{$domain}</h2>
                {else}
                <h2>{t}New alias{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.php_self}" method="post">
			        <div>
                        <input type="hidden" name="mode" value="{$mode}"/>
                    </div>
			        <fieldset>
			        {if $mode == "modify"}
			            <legend>{$alias.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$alias.uid.0}" />
                                </td>
				            </tr>
			        {else}
			            <legend>{t}new alias{/t}</legend>
			            <table>
			                <tr>
				                <td>
                                    {t}Alias{/t}
                                </td>
                                <td>
                                    <input type="text" name="uid" value="{$alias.uid.0}" />@{$domain}
                                </td>
				            </tr>
                    {/if}
                            <tr>
                                <td>
                                    {t}Alias for{/t}
                                </td>
                                <td>
                                    {strip}
                                        <textarea name="nlo_mailaliasedname" cols="40" rows="10">
                                            {section name=mailaliasedname_sec loop=$alias.mailaliasedname}
                                                {$alias.mailaliasedname[mailaliasedname_sec]}
                                            {/section}
                                        </textarea>
                                    {/strip}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    {t}Is active?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $alias.mailstatus.0 eq "FALSE"}{else}checked="checked"{/if} />
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
