param([string]$Username = 'tribeinn-local')
$secret = Read-Host 'New admin password (12–72 bytes)' -AsSecureString
$secretPointer = [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secret)
try {
    $plainPassword = [Runtime.InteropServices.Marshal]::PtrToStringBSTR($secretPointer)
    $plainPassword | php "$PSScriptRoot/configure-availability-admin.php" $Username
} finally {
    $plainPassword = $null
    [Runtime.InteropServices.Marshal]::ZeroFreeBSTR($secretPointer)
    $secret.Dispose()
}
