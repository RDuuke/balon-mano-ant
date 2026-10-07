$ErrorActionPreference = 'Stop'
$runDir = Join-Path $PWD 'openspec/changes/corregir-gate-global/verify-20261006-resumed'
$utf8 = New-Object System.Text.UTF8Encoding($false)
function Save-Text($name, $value) {
    [IO.File]::WriteAllText((Join-Path $runDir $name), (($value | Out-String) -replace "`r`n", "`n"), $utf8)
}
function Save-Json($name, $value) { Save-Text $name ($value | ConvertTo-Json -Depth 15) }
$results = @()
$stages = @(
    @{ id='desktop'; project='desktop-1024'; count=14; files=@('tests/e2e/public-experience.spec.ts','tests/e2e/home.spec.ts'); filter='Nosotros presenta integrantes|Nosotros filtros|Nosotros anuncia|Documentos limpia|3\.2 actualidad ofrece|3\.4 documentos ofrece|1\.2 actualidad reproduce|3\.1 contraste|6\.2 Documentos|detalle de actualidad|3\.3 no hay desborde|ultimas noticias conserva medios cargados|inicio responde'; timeout=360000 },
    @{ id='mobile'; project='mobile-320'; count=3; files=@('tests/e2e/contact.spec.ts','tests/e2e/public-experience.spec.ts'); filter='Contacto anuncia|3\.3 no hay desborde|detalle de actualidad'; timeout=180000 },
    @{ id='pdf'; project='tablet-768'; count=1; files=@('tests/e2e/document-admin.spec.ts'); filter='E1 reemplaza'; timeout=180000 }
)
Save-Json 'focal-selection.json' $stages
$readyCode = "fetch('http://host.docker.internal:8080/contacto/',{signal:AbortSignal.timeout(5000)}).then(async r=>{const t=await r.text();const ok=r.status===200&&!t.includes('scheduled maintenance')&&t.includes('labm-contact__form');console.log('HTTP',r.status,'maintenance',t.includes('scheduled maintenance'),'form',t.includes('labm-contact__form'));if(!ok)process.exitCode=1}).catch(e=>{console.log(e.name);process.exitCode=1})"
try {
    foreach ($probe in @(1,2)) {
        $ready = docker run --rm --add-host host.docker.internal:host-gateway --entrypoint node labm-browser-gate:node-22.13.1 -e $readyCode 2>&1
        $readyExit = $LASTEXITCODE
        Save-Text "readiness-$probe.log" $ready
        Write-Output "readiness-$probe exit=$readyExit $ready"
        if ($readyExit -ne 0) { throw 'WordPress no listo; no se ejecutan casos focales.' }
        if ($probe -eq 1) { Start-Sleep -Seconds 5 }
    }
    foreach ($stage in $stages) {
        $name = "labm-verify-20261006-focal-$($stage.id)"
        $jsonPath = "/app/openspec/changes/corregir-gate-global/verify-20261006-resumed/focal-$($stage.id)-results.json"
        $argsRun = @('run','-d','--name',$name,'--add-host','host.docker.internal:host-gateway','-e','CI=true','-e','WP_URL=http://host.docker.internal:8080','-e',"PLAYWRIGHT_JSON_OUTPUT_NAME=$jsonPath",'-v',"${PWD}:/app",'-v','labm_browser_node_modules:/app/node_modules','-v','labm_pnpm_store:/pnpm/store','-v','labm_playwright_browsers:/root/.cache/ms-playwright','-w','/app','labm-browser-gate:node-22.13.1','pnpm','exec','playwright','test')
        $argsRun += $stage.files
        $argsRun += @('--grep',$stage.filter,'--project',$stage.project,'--workers=1','--timeout=120000',"--global-timeout=$($stage.timeout)",'--reporter=list,json',"--output=openspec/changes/corregir-gate-global/verify-20261006-resumed/focal-$($stage.id)-output")
        $id = docker @argsRun
        if ($LASTEXITCODE -ne 0) { throw "No se pudo iniciar $name" }
        $started = Get-Date
        Write-Output "START $name id=$id expected=$($stage.count)"
        do {
            $state = docker inspect $name --format '{{json .State}}' | ConvertFrom-Json
            Save-Json "focal-$($stage.id)-state.json" $state
            $logs = docker logs $name 2>&1
            Save-Text "focal-$($stage.id).log" $logs
            Write-Output "PROGRESS $name running=$($state.Running) $($logs | Select-Object -Last 1)"
            if ($state.Running) {
                if (((Get-Date)-$started).TotalSeconds -gt (($stage.timeout / 1000)+60)) {
                    docker stop --time 10 $name | Out-Null
                    Write-Output "SUPERVISOR_TIMEOUT $name"
                } else { Start-Sleep -Seconds 15 }
            }
        } while ($state.Running)
        $results += [pscustomobject]@{id="browser-focal-$($stage.id)";container=$name;start=$started.ToString('o');end=(Get-Date -Format o);exit=$state.ExitCode;oom=$state.OOMKilled;project=$stage.project;expected=$stage.count;filter=$stage.filter;command=('docker '+($argsRun -join ' '));json="focal-$($stage.id)-results.json";log="focal-$($stage.id).log"}
        Save-Json 'focal-stage-results.json' $results
        Write-Output "DONE $name exit=$($state.ExitCode) OOM=$($state.OOMKilled)"
    }
} catch {
    Save-Text 'focal-supervisor-error.log' $_
    Write-Output "SUPERVISOR_ERROR $_"
} finally {
    $restoreStart = Get-Date -Format o
    try {
        Write-Output 'RESTORE official original backup START'
        $restoreOutput = & scripts/content-sync.ps1 -Action Restore -Backup .content-sync/backups/backup-manual-20261007T004758902Z-rduuqe-RDUUQE.zip -ConfirmReplace 2>&1
        Save-Text 'official-restore.log' $restoreOutput
        Save-Json 'official-restore-result.json' ([pscustomobject]@{start=$restoreStart;end=(Get-Date -Format o);status='PASS';command='scripts/content-sync.ps1 -Action Restore -Backup .content-sync/backups/backup-manual-20261007T004758902Z-rduuqe-RDUUQE.zip -ConfirmReplace';backup='backup-manual-20261007T004758902Z-rduuqe-RDUUQE.zip'})
        Write-Output 'RESTORE official PASS'
    } catch {
        Save-Text 'official-restore-error.log' $_
        Save-Json 'official-restore-result.json' ([pscustomobject]@{start=$restoreStart;end=(Get-Date -Format o);status='FAIL';error=$_.ToString()})
        Write-Output "RESTORE FAIL $_"
    }
}
