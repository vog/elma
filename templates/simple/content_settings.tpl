                <div id="Content">
                    <h2>{t}Settings{/t}</h2>
		    {if @in_array("systemusers_list",$acl) }
		        <p><a href="?module=systemusers_list">{t}Users{/t}</a> - {t}Manage systemwide users. This gives you the ability to create users for your staff or customers, who should have access to certain domains only.{/t}</p>
		    {/if}
		    {if @in_array("globaladmins_edit",$acl) }
		        <p><a href="?module=globaladmins_edit">{t}Administrators{/t}</a> - {t}Define which systemwide users should have global adminstrative rights. These can create new systemwide users as well as new domains.{/t}</p>
                   {/if}
                </div>

