            <div id="Content">
                <h2>{t}Users for domain{/t} {$domain}</h2>
                <table>
                    <tr>
                        <th>Users</th>
                        <th>Status</th>
                        <th>Command</th>
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
                        <td>
                            <a href="{$users[users_sec].editlink}">{t}edit{/t}</a> 
                            <a href="{$users[users_sec].deletelink}">{t}delete{/t}</a>
                        </td>
                    </tr>
		            {/section}
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>
                            <a href="{$link_newuser}">{t}new user{/t}</a>
                        </td>
                    </tr>
		        </table>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
