            <div id="Content">
                <h2>Domain overview</h2>
		        <table>
		            <tr>
                        <th>{t}Domain{/t}</th>
                        <th class="status">{t}Users{/t}</th>
                        <th class="status">{t}Aliases{/t}</th>
                        <th class="status">{t}Active{/t}</th>
                        <th class="command">{t}Command{/t}</th>
                    </tr>
		            {section name=domains_sec loop=$domains}
		            <tr>
                        <td>
                            {if $domains[domains_sec].maildomainaliasstatus == 0}
				<a href="{$domains[domains_sec].userslink}">{$domains[domains_sec].dc}</a>
			    {else}
				{$domains[domains_sec].dc}<br />
				<span class="isAliasFor">({t}Alias for{/t} {$domains[domains_sec].maildomainaliastarget})</span>
			    {/if}
                        </td>
                        <td class="status">
                            {$domains[domains_sec].users}/{$domains[domains_sec].usersactive}
                        </td>
                        <td class="status">
                            {$domains[domains_sec].aliases}/{$domains[domains_sec].aliasesactive}
                        </td>
                        <td class="status">
                        {if $domains[domains_sec].mailstatus == "TRUE"}
                            <img src="{$template_path}/images/button_ok.png" alt="active"/>
                        {else}
                            <img src="{$template_path}/images/button_cancel.png" alt="inactive"/>
                        {/if}
                        </td>
                        <td class="command">
                            {if @in_array("domain_edit",$acl) }<a href="{$domains[domains_sec].editlink}">{t}edit{/t}</a>{/if}
                            {if @in_array("domain_delete",$acl) }<a href="{$domains[domains_sec].deletelink}">{t}delete{/t}</a>{/if}
                        </td>
                    </tr>
		            {/section}
                    {if @in_array("domain_new",$acl) }
                    <tr>
		                <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last">&nbsp;</td>
                        <td class="last command">
                            <a href="{$link_newdomain}">{t}new domain{/t}</a>
                        </td>
                    </tr>
                    {/if}
		        </table>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
