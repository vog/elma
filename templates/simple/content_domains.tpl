                <div id="Content">
                    <h2>Domains</h2>
		    {section name=domains_sec loop=$domains}
		        {$domains[domains_sec].dc.0}
		    {/section}
                </div>

