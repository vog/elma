            <div id="Content">
                <h2>{t}Delete user{/t} {$user.uid.0}@{$domain}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                    <br />
                    <a href="index.php?module=users_list&domain={$domain}">{t}Back to domain overview{/t}</a>
                {else}
		        <form action="{$smarty.server.PHP_SELF}" method="post">
			        <fieldset>
			            <legend>{$user.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    {t 1=$user.uid.0 2=$domain}Are you sure you want to delete user %1@%2?{/t}
                                    <input type="hidden" name="uid" value="{$user.uid.0}" />
                                    <input type="hidden" name="domain" value="{$domain}" />
                                </td>
				            </tr>
				            <tr>
			                    <td>&nbsp;</td>
                                <td>
                                    <input type="submit" name="submit" value="{t}Yes{/t}"/>
                                    <input type="reset" name="reset" value="{t}No{/t}" onclick="javascript:history.back()"/>
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
