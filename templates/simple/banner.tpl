            <div id="Banner">
                <h1>ELMA - Exim LDAP Mail Administrator - v0.3</h1>
            </div>
            <div id="Pathfinder">
                {$get.module} {if $get.domain}&gt; <a href="{$smarty.server.PHP_SELF}?module=users_list&amp;domain={$get.domain}">{$get.domain}</a>{/if} {if $get.user}&gt; {$get.user}{elseif $get.alias}&gt; {$get.alias}{/if}
            </div>


{*
// vim:tabstop=4:expandtab:shiftwidth=4:filetype=smarty:syntax:ruler:
*}
