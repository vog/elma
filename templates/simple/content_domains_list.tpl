                <div id="Content">
                    <h2>Domains</h2>
		    <table>
		        <tr><th>Domain</th><th>Status</th></tr>
		    {section name=domains_sec loop=$domains}
		        <tr><td><a href="{$domains[domains_sec].link}">{$domains[domains_sec].dc}</a></td><td>{$domains[domains_sec].mailstatus}</td></tr>
		    {/section}
		    </table>
                </div>

