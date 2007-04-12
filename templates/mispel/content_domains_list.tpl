            <h1 class="pageheading">Domains</h1>
            <table>
                <tr>
                    <th>Domain</th>
                    <th>Status</th>
                    <th>Command</th>
                </tr>
                {section name=domains_sec loop=$domains}
                <tr>
                    <td>
                        <a href="{$domains[domains_sec].userslink}">{$domains[domains_sec].dc}</a>
                    </td>
                    <td>
                        {if $domains[domains_sec].mailstatus == "TRUE"}
                            <img src="{$template_path}/images/button_ok.png" alt="OK-Button"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="Cancel-Button"/>
                        {/if}
                    </td>
                    <td>
                        <a href="{$domains[domains_sec].editlink}">{t}edit{/t}</a> 
                        <a href="{$domains[domains_sec].deletelink}">{t}delete{/t}</a>
                    </td>
                </tr>
                {/section}
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>
                        <a href="{$link_newdomain}">{t}new domain{/t}</a>
                    </td>
                </tr>
            </table>
