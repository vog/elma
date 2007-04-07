                <div id="Content">
                    <h2>Domains</h2>
		    {section name=domains_sec loop=$domains}
		        <a href="{$domains[domains_sec].link}">{$domains[domains_sec].dc}</a>
		    {/section}
                </div>

