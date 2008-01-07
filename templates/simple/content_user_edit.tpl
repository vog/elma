            {validate id="cn" message="First name not valid or empty" append="validation_errors"}
            {validate id="sn" message="Last name not valid or empty" append="validation_errors"}
            {validate id="password" message="passwords do not match" append="validation_errors"}

            <div id="Content">
                <h2>{t}Edit user{/t} {$user.uid.0}@{$domain}</h2>
		        {if $smarty.post.submit}
                    {include file="print_submit_status.tpl"}
                {/if}

		        <form action="{$smarty.server.REQUEST_URI}" method="post">

			        <fieldset>
			            <legend>{$user.uid.0}@{$domain}</legend>
			            <table>
			                <tr>
                                <td>
                                    <input type="hidden" name="uid" value="{$user.uid.0}" />
                                </td>
				            </tr>
		                    <tr>
				                <td>
                                    {t}First name{/t}
                                </td>
                                <td>
                                    <input type="text" name="cn" value="{$user.cn.0}" />
                                </td>
				            </tr>
                            <tr>
                                <td>
                                    {t}Last name{/t}
                                </td>
                                <td>
                                    <input type="text" name="sn" value="{$user.sn.0}" />
                                </td>
                            </tr>
                            {if @in_array("user_edit.active",$acl)}
			                <tr>
				                <td>
                                    {t}Is active?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="mailstatus" {if $user.mailstatus.0 eq "FALSE"}{else}checked="checked"{/if} />
                                </td>
				            </tr>
                            {/if}
				            <tr>
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
			                <tr>
				                <td>
                                    {t}Redirect{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="nlo_redirectstatus" {if $redirectsettings.STATUS eq "#"}{else}checked="checked"{/if} />
                                </td>
				            </tr>
			                <tr>
				                <td>
                                    {t}Recipient:{/t}
                                </td>
                                <td>
                                    <input type="text" name="nlo_redirectrecipient" cols="60" value="{$redirectsettings.RECIPIENT}"/>
                                </td>
				            </tr>

				            <tr>
				                <td colspan="2">
                                    <hr/>
                                </td>
				            </tr>
			                <tr>
				                <td>
                                    {t}on vacation?{/t}
                                </td>
                                <td>
                                    <input type="checkbox" name="nlo_vacationstatus" {if $vacationsettings.STATUS eq "#"}{else}checked="checked"{/if} />
                                </td>
				            </tr>
			                <tr>
				                <td>
                                    {t}Message:{/t}
                                </td>
                                <td>
                                    <textarea name="nlo_vacationmessage" cols="55" rows="5">{$vacationsettings.MESSAGE}</textarea>
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
                                   password: <input type="password" name="clearpassword" size="10" value="{$user.clearpassword.0}"><br />
                                   password: <input type="password" name="nlo_clearpassword1" size="10" value="{$user.clearpassword.0}"><br />
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
