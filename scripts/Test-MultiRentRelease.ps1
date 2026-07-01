param(
	[switch] $IncludePackages
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$scriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$repoRoot = Resolve-Path (Join-Path $scriptRoot '..')
$themeDir = Join-Path $repoRoot 'MultiRent'
$pluginDir = Join-Path $repoRoot 'multirent-companion'
$releaseDir = Join-Path $repoRoot 'release-assets'

$privatePathPattern = '(?i)(^|/)(AuthData|\.env)(/|\.|$)|(^|/)wp-config\.php$|\.(sql|sqlite|db|wpress|bak|tmp|log|pem|key|pfx|p12|crt|cer|token)$|(^|/)[^/]*(token|secret|password)[^/]*$'
$contentScanExtensions = @('.php', '.js', '.css', '.sh', '.ps1', '.yml', '.yaml', '.json', '.ini', '.sql', '.env')
$contentPatterns = @(
	'-----BEGIN ([A-Z ]+)?PRIVATE KEY-----',
	'(?i)\b(api[_-]?key|secret[_-]?key|auth[_-]?code|access[_-]?token|refresh[_-]?token|client[_-]?secret)\b\s*[:=]\s*[''\"][^''\"]{8,}',
	'(?i)\b(ai1wm_secret_key|grw_auth_code|session_tokens)\b'
)

function Get-RelativePath {
	param(
		[Parameter(Mandatory = $true)] [string] $BasePath,
		[Parameter(Mandatory = $true)] [string] $Path
	)

	$baseUri = [Uri]((Resolve-Path -LiteralPath $BasePath).Path.TrimEnd([IO.Path]::DirectorySeparatorChar) + [IO.Path]::DirectorySeparatorChar)
	$pathUri = [Uri]((Resolve-Path -LiteralPath $Path).Path)
	return [Uri]::UnescapeDataString($baseUri.MakeRelativeUri($pathUri).ToString())
}

function Get-SourceFiles {
	Get-ChildItem -LiteralPath $repoRoot -Recurse -File -Force |
		Where-Object {
			$relativePath = (Get-RelativePath -BasePath $repoRoot -Path $_.FullName).Replace('\\', '/')
			$relativePath -notmatch '(^|/)\.git(/|$)' -and
			$relativePath -notmatch '^release-assets/' -and
			$relativePath -notmatch '(^|/)vendor(/|$)' -and
			$relativePath -notmatch '(^|/)node_modules(/|$)'
		}
}

function Assert-NoPrivateArtifacts {
	param(
		[Parameter(Mandatory = $true)] [string] $Root,
		[Parameter(Mandatory = $true)] [System.IO.FileInfo[]] $Files
	)

	$issues = New-Object System.Collections.Generic.List[string]

	foreach ($file in $Files) {
		$relativePath = (Get-RelativePath -BasePath $Root -Path $file.FullName).Replace('\\', '/')
		if ($relativePath -eq 'scripts/Test-MultiRentRelease.ps1') {
			continue
		}

		if ($relativePath -match $privatePathPattern) {
			$issues.Add("Private-looking path: $relativePath")
			continue
		}

		if ($contentScanExtensions -notcontains $file.Extension.ToLowerInvariant()) {
			continue
		}

		$content = [System.IO.File]::ReadAllText($file.FullName)
		foreach ($pattern in $contentPatterns) {
			if ($content -match $pattern) {
				$issues.Add("Sensitive-looking content in: $relativePath")
				break
			}
		}
	}

	if ($issues.Count -gt 0) {
		throw "Privacy validation failed:`n$($issues -join "`n")"
	}
}

function Test-PhpSyntax {
	$php = Get-Command php -ErrorAction SilentlyContinue
	if (-not $php) {
		Write-Warning 'PHP is not available on PATH; skipping PHP syntax checks.'
		return
	}

	$phpFiles = Get-ChildItem -LiteralPath $themeDir, $pluginDir -Recurse -File -Filter '*.php'
	foreach ($file in $phpFiles) {
		& $php.Source -l $file.FullName | Out-Null
		if ($LASTEXITCODE -ne 0) {
			throw "PHP syntax check failed: $($file.FullName)"
		}
	}
}

function Test-ReleasePackages {
	if (-not (Test-Path -LiteralPath $releaseDir -PathType Container)) {
		return
	}

	$zipFiles = Get-ChildItem -LiteralPath $releaseDir -File -Filter '*.zip'
	if (-not $zipFiles) {
		return
	}

	$tempRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("multirent-zip-validation-{0}" -f [guid]::NewGuid().ToString('N'))
	try {
		New-Item -ItemType Directory -Path $tempRoot | Out-Null
		foreach ($zip in $zipFiles) {
			$entries = & tar.exe -tf $zip.FullName
			if ($LASTEXITCODE -ne 0) {
				throw "Could not inspect ZIP: $($zip.FullName)"
			}

			foreach ($entry in $entries) {
				$normalizedEntry = $entry.Replace('\\', '/')
				if ($normalizedEntry -match $privatePathPattern) {
					throw "Private-looking ZIP entry in $($zip.Name): $normalizedEntry"
				}
			}

			$extractDir = Join-Path $tempRoot ([IO.Path]::GetFileNameWithoutExtension($zip.Name))
			New-Item -ItemType Directory -Path $extractDir | Out-Null
			Push-Location $extractDir
			try {
				& tar.exe -xf $zip.FullName
				if ($LASTEXITCODE -ne 0) {
					throw "Could not extract ZIP for validation: $($zip.FullName)"
				}
			} finally {
				Pop-Location
			}

			$extractedFiles = @(Get-ChildItem -LiteralPath $extractDir -Recurse -File -Force)
			Assert-NoPrivateArtifacts -Root $extractDir -Files $extractedFiles
		}
	} finally {
		if (Test-Path -LiteralPath $tempRoot) {
			Remove-Item -LiteralPath $tempRoot -Recurse -Force
		}
	}
}

$sourceFiles = @(Get-SourceFiles)
Assert-NoPrivateArtifacts -Root $repoRoot -Files $sourceFiles
Test-PhpSyntax

if ($IncludePackages) {
	Test-ReleasePackages
}

Write-Host 'MultiRent validation passed.'
