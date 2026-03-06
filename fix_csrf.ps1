$viewsDir = 'c:\laragon\www\optima\app\Views'
$files = Get-ChildItem -Path $viewsDir -Recurse -Filter '*.php'
$totalChanges = 0
foreach ($file in $files) {
    $content = [System.IO.File]::ReadAllText($file.FullName, [System.Text.Encoding]::UTF8)
    $original = $content

    # Pattern 1: object literal key  csrf_test_name:  ->  [window.csrfTokenName]:
    $content = $content -replace '(?<!\[)(\bcsrf_test_name\b)(:)', '[window.csrfTokenName]$2'

    # Pattern 2: property assignment  .csrf_test_name =  ->  [window.csrfTokenName] =
    $content = $content -replace '\.csrf_test_name(\s*=)', '[window.csrfTokenName]$1'

    # Pattern 3: formData.append('csrf_test_name',  ->  formData.append(window.csrfTokenName,
    $content = $content -replace "append\('csrf_test_name'", 'append(window.csrfTokenName'

    # Pattern 4: '&csrf_test_name=' -> '&' + window.csrfTokenName + '='
    $content = $content -replace "'&csrf_test_name='", "'&' + window.csrfTokenName + '='"

    if ($content -ne $original) {
        [System.IO.File]::WriteAllText($file.FullName, $content, [System.Text.Encoding]::UTF8)
        Write-Host "Fixed: $($file.Name)"
        $totalChanges++
    }
}
Write-Host "Total files changed: $totalChanges"
