            <div id="Content">
                <h2>Domains</h2>
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
                            <img src="{$template_path}/images/button_ok.png" alt="active"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="inactive"/>
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
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
