$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$distPath = Join-Path $projectRoot 'dist'
$workPath = Join-Path $projectRoot 'work'
$stagePath = Join-Path $workPath 'layers-google-maps-embed'
$zipPath = Join-Path $distPath 'layers-google-maps-embed.zip'

if (-not $stagePath.StartsWith($projectRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
    throw 'Refusing to use a staging directory outside the project.'
}

if (Test-Path -LiteralPath $stagePath) {
    Remove-Item -LiteralPath $stagePath -Recurse -Force
}

New-Item -ItemType Directory -Path $stagePath -Force | Out-Null
New-Item -ItemType Directory -Path $distPath -Force | Out-Null

Copy-Item -LiteralPath (Join-Path $projectRoot 'layers-google-maps-embed.php') -Destination $stagePath
Copy-Item -LiteralPath (Join-Path $projectRoot 'includes') -Destination $stagePath -Recurse
Copy-Item -LiteralPath (Join-Path $projectRoot 'LICENSE') -Destination $stagePath
Copy-Item -LiteralPath (Join-Path $projectRoot 'readme.txt') -Destination $stagePath

if (Test-Path -LiteralPath $zipPath) {
    Remove-Item -LiteralPath $zipPath -Force
}

Compress-Archive -LiteralPath $stagePath -DestinationPath $zipPath -CompressionLevel Optimal
Remove-Item -LiteralPath $workPath -Recurse -Force

Write-Output $zipPath

