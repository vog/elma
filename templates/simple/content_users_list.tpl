            <div id="Content">
                <h2>{t}Users for domain{/t} {$domain}</h2>
                <table>
                    <tr>
                        <th>{t}User{/t}</th>
                        <th>{t}Status{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=users_sec loop=$users}
                    <tr>
                        <td>
                            {$users[users_sec].uid}
                        </td>
                        <td>
                        {if $users[users_sec].mailstatus == "TRUE"}
                            <img src="{$template_path}/images/button_ok.png" alt="active"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="inactive"/>
                        {/if}
                        </td>
                        <td class="command">
                            <a href="{$users[users_sec].editlink}">{t}edit{/t}</a> 
                            <a href="{$users[users_sec].deletelink}">{t}delete{/t}</a>
                        </td>
                    </tr>
		            {/section}
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="command">
                            <a href="{$link_newuser}">{t}new user{/t}</a>
                        </td>
                    </tr>
		        </table>
                <div class="space25"></div>
                <table>
                    <tr>
                        <th>{t}Alias{/t}</th>
                        <th>{t}Alias for{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=aliases_sec loop=$aliases}
                    <tr>
                        <td>
                            {$aliases[aliases_sec].uid}
                        </td>
                        <td>

                        {section name=mailaliasedname_sec loop=$aliases[aliases_sec].mailaliasedname}
                            {$aliases[aliases_sec].mailaliasedname[mailaliasedname_sec]}<br/>
                        {/section}
                        </td>
                        <td class="command">
                            <a href="{$aliases[aliases_sec].editlink}">{t}edit{/t}</a> 
                            <a href="{$aliases[aliases_sec].deletelink}">{t}delete{/t}</a>
                        </td>
                    </tr>
		            {/section}
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td class="command">
                            <a href="{$link_newalias}">{t}new alias{/t}</a>
                        </td>
                    </tr>
		        </table>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
