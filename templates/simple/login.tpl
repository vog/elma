            <div id="Content">
                <form action="login.php" method="post">
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
                                <input type="text" name="username" size="30" maxlength="30" />
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
                                    <option value="de_DE">deutsch</option>
                                    <option value="en_US">english</option>
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
