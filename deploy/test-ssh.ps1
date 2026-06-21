# Test SSH connection to ADM hosting
$hostName = "bg622152.ftp.tools"
$user = "bg622152"

Write-Host "=== SSH test for ADM ===" -ForegroundColor Cyan
Write-Host "Host: $hostName"
Write-Host "User: $user"
Write-Host ""

Write-Host "1. DNS lookup..."
try {
    $dns = Resolve-DnsName $hostName -ErrorAction Stop | Where-Object { $_.Type -eq 'A' } | Select-Object -First 1
    Write-Host "   IP: $($dns.IPAddress)" -ForegroundColor Green
} catch {
    Write-Host "   DNS FAIL: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "2. Port 22 (SSH)..."
$tcp = Test-NetConnection -ComputerName $hostName -Port 22 -WarningAction SilentlyContinue
if ($tcp.TcpTestSucceeded) {
    Write-Host "   Port 22 OPEN - SSH should work" -ForegroundColor Green
} else {
    Write-Host "   Port 22 BLOCKED or TIMEOUT" -ForegroundColor Red
    Write-Host "   Try: mobile hotspot, other network, or contact ISP" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "3. Connect command:"
Write-Host "   ssh ${user}@${hostName}" -ForegroundColor White
Write-Host ""
Write-Host "First time: type 'yes' when asked about fingerprint"
Write-Host "Password: from ADM -> SSH access (characters hidden while typing)"
