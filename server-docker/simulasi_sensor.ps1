# ==========================================
# 📡 SIMULASI SENSOR IOT (SmartGuard Parking)
# ==========================================

$url = "http://localhost:8080/api_sensor.php"
$slots = @("A1", "A2", "A3", "B1", "B2", "B3")
$status_list = @("EMPTY", "OCCUPIED")

Clear-Host
Write-Host "╔══════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║    SMARTGUARD IOT SENSOR SIMULATION      ║" -ForegroundColor Cyan
Write-Host "║    Sending data to Docker Container...   ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# Loop selamanya (Ctrl + C untuk stop)
while ($true) {

    # 1. Pilih Slot dan Status Acak
    $slot = $slots | Get-Random
    $status = $status_list | Get-Random
    
    # 2. Bungkus data jadi JSON
    $body = @{
        slot_name = $slot
        status    = $status
    } | ConvertTo-Json

    # 3. Kirim ke Server (Try-Catch untuk handle error)
    try {
        $response = Invoke-RestMethod -Uri $url -Method Post -Body $body -ContentType "application/json"
        
        if ($status -eq "EMPTY") {
            Write-Host "[$(Get-Date -Format 'HH:mm:ss')] 🟢 Sensor $slot mendeteksi: KOSONG" -ForegroundColor Green
        } 
        else {
            Write-Host "[$(Get-Date -Format 'HH:mm:ss')] 🔴 Sensor $slot mendeteksi: ADA MOBIL" -ForegroundColor Red
        }
    }
    catch {
        Write-Host "❌ Gagal koneksi ke server! ($url)" -ForegroundColor Yellow
        Write-Host "   -> Pastikan Docker sudah 'UP' dan Web Parkir bisa dibuka." -ForegroundColor DarkGray
    }

    # 4. Delay 2 detik
    Start-Sleep -Seconds 2
}
