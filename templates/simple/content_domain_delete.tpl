            <div id="Content">
                <h2>{t}Delete domain{/t} {$domain}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {else}
		        <form action="{$smarty.server.php_self}" method="post">
			        <fieldset>
			            <legend>{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    {t 1=$domain}Are you sure you want to delete domain %1?{/t}
                                    <input type="hidden" name="domain" value="{$domain}" />
                                </td>
				            </tr>
				            <tr>
			                    <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="submit" value="{t}Yes{/t}"/>
                                    <input type="reset" name="reset" value="{t}No{/t}" onClick="javascript:history.back()"/>
                                </td>
				            </tr>
 	                    </table>
                    </fieldset>
			    </form>
                {/if}
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
