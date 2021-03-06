            {validate id="nlo_mailaliasedname" message="Destinations must not be empty" append="validation_errors"}

            <div id="Content">
                <h2>{t}Edit alias{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.REQUEST_URI}" method="post">
			        <fieldset>
			            <legend>{$alias.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$alias.uid.0}" />
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}Alias for{/t}
                                </td>
                                <td>
                                    <textarea name="nlo_mailaliasedname" cols="40" rows="10">{$alias.mailaliasedname}</textarea>
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
