                    {if $submit_status == "0"}
                        <div>
                            {t}Data has been saved.{/t}
                        </div>
                    {else}
                        <fieldset class="error">
                            <legend  class="error">An error occured</legend>
                            <p>{t}Sorry, your data could not be saved. The following error(s) occured:{/t} {$submit_status}</p>
                            {foreach from=$validation_errors item="error"}
                                - <font color="red">{$error}</font><br/>
                            {/foreach}
                        </fieldset>
                        <p/>
                    {/if}
