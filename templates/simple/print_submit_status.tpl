                    {if $submit_status == "0"}
                        <div>
                            {t}Data has been saved.{/t}
                        </div>
                    {else}
                        <div>
                            {t}Sorry, your data could not be saved. The following error occured:{/t} {$submit_status} {$submit_status1}
                        </div>
                    {/if}
