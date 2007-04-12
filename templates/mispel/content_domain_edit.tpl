            <h1 class="pageheading">{t}Edit Domain{/t}</h1>
                {if $smarty.post.submit}
                    {if $submit_status == "0"}
                        <div>
                            {t}Data saved successfully.{/t}
                        </div>
                    {else}
                        <div>
                            {t}Sorry, your data could not be saved. The following LDAP error occured:{/t}
                            {$submit_status}
                        </div>
                    {/if}
                {/if}
                <form action="{$smarty.server.php_self}" method="post">
                    <div><input type="hidden" name="mode" value="{$mode}" /></div>
                    {if $mode == "modify"}
                        <div>
                            <input type="hidden" name="dc" value="{$domain.dc.0}" />
                        </div>
                        <fieldset>
                            <legend>{$domain.dc.0}</legend>
                            <table id="Content">
                                <tr>
                                    <td>
                                        {t}Status{/t}
                                    </td>
                                    <td>
                                        <input type="checkbox" name="mailstatus" {if $domain.mailstatus.0 eq "TRUE"}checked="checked"{/if} />
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td><input type="submit" name="submit" /></td>
                                </tr>
                            </table>
                        </fieldset>
                    {else}
                        <fieldset>
                            <legend>{t}new Domain{/t}</legend>
                            <table id="Content">
                                <tr>
                                    <td>        
                                        {t}Domain{/t}
                                    </td>
                                    <td>
                                        <input type="text" name="dc" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {t}Active{/t}
                                    </td>
                                    <td>
                                        <input type="checkbox" name="mailstatus" checked="checked" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>
                                        <input type="submit" name="submit" />
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    {/if}
                </form>
