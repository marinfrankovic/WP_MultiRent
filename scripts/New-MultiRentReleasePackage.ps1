param(
	[Parameter(Mandatory = $true)]
	[ValidatePattern('^\d+\.\d+\.\d+$')]
	[string] $Version,

	[switch] $CleanOldLocalPackages
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$repoRoot = Resolve-Path (Join-Path $scriptRoot '..')
$themeDir = Join-Path $repoRoot 'MultiRent'
$pluginDir = Join-Path $repoRoot 'multirent-companion'
$releaseDir = Join-Path $repoRoot 'release-assets'

function Assert-ReleaseSource {
	$requiredPaths = @(
		(Join-Path $themeDir 'style.css'),
		(Join-Path $themeDir 'functions.php'),
		(Join-Path $pluginDir 'multirent-companion.php'),
		(Join-Path $pluginDir 'README.md')
	)

	foreach ($path in $requiredPaths) {
		if (-not (Test-Path -LiteralPath $path -PathType Leaf)) {
			throw "Required release source file is missing: $path"
		}
	}
}

function Assert-ZipEntry {
	param(
		[Parameter(Mandatory = $true)]
		[string] $ZipPath,

		[Parameter(Mandatory = $true)]
		[string] $Entry
	)

	$entries = & tar.exe -tf $ZipPath
	if ($LASTEXITCODE -ne 0) {
		throw "Could not inspect ZIP: $ZipPath"
	}

	if ($entries -notcontains $Entry) {
		throw "ZIP $ZipPath is missing required entry: $Entry"
	}
}

Assert-ReleaseSource

if (-not (Test-Path -LiteralPath $releaseDir -PathType Container)) {
	New-Item -ItemType Directory -Path $releaseDir | Out-Null
}

$stagingRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("multirent-release-{0}-{1}" -f $Version, [guid]::NewGuid().ToString('N'))
$packagesDir = Join-Path $stagingRoot 'packages'
$templateDir = Join-Path $stagingRoot 'template'

try {
	New-Item -ItemType Directory -Path $packagesDir | Out-Null
	Copy-Item -LiteralPath $themeDir -Destination (Join-Path $packagesDir 'MultiRent') -Recurse
	Copy-Item -LiteralPath $pluginDir -Destination (Join-Path $packagesDir 'multirent-companion') -Recurse

	$themeZip = Join-Path $releaseDir ("multirent-theme-upload-{0}.zip" -f $Version)
	$pluginZip = Join-Path $releaseDir ("multirent-companion-plugin-upload-{0}.zip" -f $Version)
	$templateZip = Join-Path $releaseDir ("multirent-complete-package-extract-first-{0}.zip" -f $Version)

	Push-Location $packagesDir
	try {
		& tar.exe -a -cf $themeZip 'MultiRent'
		if ($LASTEXITCODE -ne 0) { throw "Failed to create theme ZIP: $themeZip" }

		& tar.exe -a -cf $pluginZip 'multirent-companion'
		if ($LASTEXITCODE -ne 0) { throw "Failed to create companion plugin ZIP: $pluginZip" }
	} finally {
		Pop-Location
	}

	$bundleDir = Join-Path $templateDir 'multirent-complete-package-extract-first'
	New-Item -ItemType Directory -Path $bundleDir | Out-Null
	Copy-Item -LiteralPath $themeZip -Destination $bundleDir
	Copy-Item -LiteralPath $pluginZip -Destination $bundleDir

	Push-Location $templateDir
	try {
		& tar.exe -a -cf $templateZip 'multirent-complete-package-extract-first'
		if ($LASTEXITCODE -ne 0) { throw "Failed to create template ZIP: $templateZip" }
	} finally {
		Pop-Location
	}

	Assert-ZipEntry -ZipPath $themeZip -Entry 'MultiRent/style.css'
	Assert-ZipEntry -ZipPath $pluginZip -Entry 'multirent-companion/multirent-companion.php'
	Assert-ZipEntry -ZipPath $templateZip -Entry ("multirent-complete-package-extract-first/multirent-theme-upload-{0}.zip" -f $Version)
	Assert-ZipEntry -ZipPath $templateZip -Entry ("multirent-complete-package-extract-first/multirent-companion-plugin-upload-{0}.zip" -f $Version)

	if ($CleanOldLocalPackages) {
		Get-ChildItem -LiteralPath $releaseDir -File -Filter '*.zip' |
			Where-Object { $_.Name -notin @((Split-Path $themeZip -Leaf), (Split-Path $pluginZip -Leaf), (Split-Path $templateZip -Leaf)) } |
			Remove-Item -Force
	}

	Assert-ReleaseSource

	Write-Host "Created release packages:"
	Get-Item -LiteralPath $themeZip, $pluginZip, $templateZip | Select-Object Name, Length
} finally {
	if (Test-Path -LiteralPath $stagingRoot) {
		Remove-Item -LiteralPath $stagingRoot -Recurse -Force
	}

	Assert-ReleaseSource
}