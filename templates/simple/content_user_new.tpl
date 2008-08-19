            {validate id="uid" message="Username not valid or empty" append="validation_errors"}
            {validate id="cn" message="First name not valid or empty" append="validation_errors"}
            {validate id="sn" message="Last name not valid or empty" append="validation_errors"}
            {validate id="password" message="passwords do not match" append="validation_errors"}

            <div id="Content">
                <h2>{t}New user{/t}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}
		        <form action="{$smarty.server.REQUEST_URI}" method="post">
			        <fieldset>
			            <legend>{t}new user{/t}</legend>
			            <table>
			                <tr>
				                <td>
                                    {t}Username{/t}
                                </td>
                                <td>
                                    <input type="text" name="uid" value="{$user.uid}" />@{$domain}
                                </td>
				            </tr>
		                    <tr>
				                <td>
                                    {t}First name{/t}
                                </td>
                                <td>
                                    <input type="text" name="cn" value="{$user.cn}" />
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}Last name{/t}
                                </td>
                                <td>
                                    <input type="text" name="sn" value="{$user.sn}" />
                                </td>
                            </tr>
			                <tr>
				                <td>
                                    {t}Is active?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $user.mailstatus eq "FALSE"}{else}checked="checked"{/if} />
                                </td>
				            </tr>
				            <tr>
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
				            <tr>
                                <td>
                                    {t}Password{/t}
                                </td>
                                <td>
                                    <input type="text" name="clearpassword" value="{if isset($user.clearpassword)}{$user.clearpassword}{else}{$autogen_password}{/if}" />
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
                                    <input type="radio" name="nlo_next_step" value="add_another" checked="true" /> {t}Add another user{/t}<br />
                                    <input type="radio" name="nlo_next_step" value="show_overview" /> {t}Go to domain overview{/t}<br />
                                 </td>
                            </tr>
    			            <tr>
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
				            <tr>
                                <td colspan="2" class="buttons">
                                    <input type="submit" name="submit" value="{t}Save{/t}" id="button"/>
                                </td>
				            </tr>
 	                    </table>
                    </fieldset>
			    </form>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
