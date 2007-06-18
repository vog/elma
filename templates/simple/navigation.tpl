            <div id="Navigation">    
	            <ul>
                    <li><a href="{$smarty.server.PHP_SELF}?module=main">{t}Home{/t}</a></li>
                    {if $userclass != "user"}
	                <li><a href="{$smarty.server.PHP_SELF}?module=domains_list">{t}Domains{/t}</a></li>
                    {/if}
                    {if $userclass == "systemadmin"}
                    <li><a href="{$smarty.server.PHP_SELF}?module=settings">{t}Settings{/t}</a></li>
                    {/if}
                    <li><a href="logout.php">{t}Logout{/t}</a></li>
                </ul>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
