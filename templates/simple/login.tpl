            <div id="Content">
                <form action="?action=login" method="post">
		            <table>
                        <tr>
			                <th colspan="3" class="login">
                                Exim Ldap Mail Administrator - Login
                            </th>
                        </tr>
                        <tr>
                            <td>
                                &nbsp;
                            </td>
                            <td colspan="2">
                                {if $loginerror == TRUE}
                                    <font color="red">{t}Invalid username and/or password.{/t}</font>
                                {else}
                                    &nbsp;
                                {/if}
                            </td>
                        </tr>
		                <tr>
                            <td rowspan="4">
                                <img src="{$template_path}/images/email.jpg" id="loginlogo" alt="ELMA Logo"/>
                            </td>
                            <td>
                                {t}Username{/t}
                            </td>
                            <td>
                                <input type="text" name="username" size="30" maxlength="80" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                {t}Password{/t}
                            </td>
                            <td>
                                <input type="password" name="password" size="20" maxlength="20" />
                            </td>
                        </tr>
			            <tr>
                            <td>
                                {t}Language{/t}
                            </td>
                            <td>
                                <select name="language">
                                    {html_options values=$language_ids output=$language_names selected=$default_language}
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input type="submit" name="submit" value="{t}Login{/t}" />
                            </td>
                        </tr>
			        </table>
                </form>
		    </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
