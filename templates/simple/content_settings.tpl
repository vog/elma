                <div id="Content">
                    <h2>{t}Settings{/t}</h2>
		    <ul>
		    	{if $userclass == "systemadmin"}
		        <li><a href="{$smarty.server.PHP_SELF}?module=systemusers_list">{t}Users{/t}</a></li>
			{/if}
			{if $userclass == "systemadmin"}
		        <li><a href="{$smarty.server.PHP_SELF}?module=globaladmins_edit">{t}Organize global admins{/t}</a></li>
			{/if}
		    </ul>
                </div>

