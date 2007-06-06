            <div id="Content">
                <h2>{t}Systemusers{/t}</h2>
                <table>
                    <tr>
                        <th>{t}Username{/t}</th>
                        <th>{t}Realname{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=systemusers_sec loop=$systemusers}
                    <tr>
                        <td>
                            {$systemusers[systemusers_sec].uid}
                        </td>
                        <td>
                            {$systemusers[systemusers_sec].lname} {$systemusers[systemusers_sec].fname}
                        </td>
                        <td class="command">
                            <a href="{$systemusers[systemusers_sec].editlink}">{t}edit{/t}</a> 
                            <a href="{$systemusers[systemusers_sec].deletelink}">{t}delete{/t}</a>
                        </td>
                    </tr>
		            {/section}
                    <tr>
                        <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last command">
                            <a href="{$link_newsystemuser}">{t}new user{/t}</a>
                        </td>
                    </tr>
		        </table>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
