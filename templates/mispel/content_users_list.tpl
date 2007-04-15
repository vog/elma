            <h1 class="pageheading">{t}Users for domain{/t} {$domain}</h1>
            <table>
                <tr>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Command</th>
                </tr>
                {section name=users_sec loop=$users}
                <tr>
                    <td>{$users[users_sec].uid}</td>
                    <td>
                        {if $users[users_sec].mailstatus == "TRUE"}
                            <img alt="OK-Button" src="{$template_path}/images/button_ok.png"/>
                        {else}
                            <img alt="Cancel-Button" src="{$template_path}/images/button_cancel.png"/>
                        {/if}
                    </td>
                    <td>
                        <a href="{$users[users_sec].editlink}">{t}edit{/t}</a> 
                        <a href="{$users[users_sec].deletelink}">{t}delete{/t}</a>
                    </td>
                </tr>
                {/section}
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td><a href="{$link_newuser}">{t}new user{/t}</a></td>
                </tr>
            </table>