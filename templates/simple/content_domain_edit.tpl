            <div id="Content">
                {if $mode == "modify"}
                <h2>{t }Edit domain{/t} {$domain.dc.0}</h2>
                {else}
                <h2>{t}New domain{/t}</h2>
                {/if}
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.php_self}" method="post">
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
			                <legend>{t}new Domain{/t}</legend>
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
			                    <td>&nbsp;</td>
                                <td><input type="submit" name="submit" value="{t}Save{/t}" /></td>
			                </tr>
	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
