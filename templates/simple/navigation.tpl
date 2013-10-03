            <div id="Navigation">    
	            <ul>
                    <li><a href="?module=main">{t}Home{/t}</a></li>
                    {if @in_array("domains_list",$acl) }
	                <li><a href="?module=domains_list">{t}Domains{/t}</a></li>
                    {/if}
                    {if @in_array("user_edit_himself",$acl) }
                    <li><a href="?module=user_edit">{t}Settings{/t}</a></li>
                    {/if}
                    {if @in_array("settings",$acl) }
                    <li><a href="?module=settings">{t}Settings{/t}</a></li>
                    {/if}
                    <li><a href="?action=logout">{t}Logout{/t}</a></li>
                </ul>
            </div>

{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
