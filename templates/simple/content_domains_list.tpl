            <div id="Content">
                <h2>Domains</h2>
		        <table>
		            <tr>
                        <th>{t}Domain{/t}</th>
                        <th class="status">{t}Active{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=domains_sec loop=$domains}
		            <tr>
                        <td>
                            <a href="{$domains[domains_sec].userslink}">{$domains[domains_sec].dc}</a>
                        </td>
                        <td class="status">
                        {if $domains[domains_sec].mailstatus == "TRUE"}
                            <img src="{$template_path}/images/button_ok.png" alt="active"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="inactive"/>
                        {/if}
                        </td>
                        <td class="command">
                            <a href="{$domains[domains_sec].editlink}">{t}edit{/t}</a> 
                            <a href="{$domains[domains_sec].deletelink}">{t}delete{/t}</a>
                        </td>
                    </tr>
		            {/section}
                    <tr>
		                <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last command">
                            <a href="{$link_newdomain}">{t}new domain{/t}</a>
                        </td>
                    </tr>
		        </table>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
