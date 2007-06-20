                <div id="Content">
                    <h2>{t}Settings{/t}</h2>
		    {if $userclass == "systemadmin"}
		        <p><a href="{$smarty.server.PHP_SELF}?module=systemusers_list">{t}Users{/t}</a> - {t}Manage systemwide users. This gives you the ability to create users for your staff or customers, who should have access to certain domains only.{/t}</p>
		        <p><a href="{$smarty.server.PHP_SELF}?module=globaladmins_edit">{t}Administrators{/t}</a> - {t}Define which systemwide users should have global adminstrative rights. Theese can create new systemwide users as well as new domains.{/t}</p>
		    {/if}
                </div>

