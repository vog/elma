            <div id="Content">
                <h2>{t}Users and aliases for domain{/t} {$domain}</h2>
                <table>
                    <tr>
                        <th>{t}Username{/t}</th>
                        <th class="status">{t}Active{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=users_sec loop=$users}
                    <tr>
                        <td>
                            {$users[users_sec].uid}
                        </td>
                        <td class="status">
                        {if $users[users_sec].mailstatus == "TRUE"}
                            <img src="{$template_path}/images/button_ok.png" alt="active"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="inactive"/>
                        {/if}
                        </td>
                        <td class="command">
                            {if @in_array("user_edit",$acl)} <a href="{$users[users_sec].editlink}">{t}edit{/t}</a> {/if}
                            {if @in_array("user_delete",$acl)} <a href="{$users[users_sec].deletelink}">{t}delete{/t}</a>{/if}
                        </td>
                    </tr>
		            {/section}
                    {if @in_array("user_new",$acl)}
                    <tr>
                        <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last command">
                            <a href="{$link_newuser}">{t}new user{/t}</a>
                        </td>
                    </tr>
                    {/if}
		        </table>
                <div class="space25"></div>
                <table>
                    <tr>
                        <th>{t}Alias{/t}</th>
                        <th>{t}Alias for{/t}</th>
                        <th class="status">{t}Active{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=aliases_sec loop=$aliases}
                    <tr>
                        <td>
                            {$aliases[aliases_sec].uid}
                        </td>
                        <td>
                            <ul>
                        {section name=mailaliasedname_sec loop=$aliases[aliases_sec].mailaliasedname}
                                <li>{$aliases[aliases_sec].mailaliasedname[mailaliasedname_sec]}</li>
                        {/section}
                            </ul>
                        </td>
                        <td class="status">
                        {if $aliases[aliases_sec].mailstatus == "TRUE"}
                            <img src="{$template_path}/images/button_ok.png" alt="active"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="inactive"/>
                        {/if}
                        </td>
                        <td class="command">
                            {if @in_array("alias_edit",$acl)} <a href="{$aliases[aliases_sec].editlink}">{t}edit{/t}</a> {/if}
                            {if @in_array("alias_delete",$acl)} <a href="{$aliases[aliases_sec].deletelink}">{t}delete{/t}</a> {/if}
                        </td>
                    </tr>
		            {/section}
                    {if @in_array("alias_new",$acl) }
                    <tr>
                        <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last status">&nbsp;</td>
                        <td class="last command">
                            <a href="{$link_newalias}">{t}new alias{/t}</a>
                        </td>
                    </tr>
                    {/if}
		        </table>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
