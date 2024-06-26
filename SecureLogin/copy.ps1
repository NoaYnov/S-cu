param (
    [string]$sourceFolder = (Get-Location),
    [string]$destinationFolder = "C:\xampp\htdocs",
    [string]$excludeListFile = "exclude_list.txt"
)

# Function to check if an item should be excluded
function ShouldExcludeItem($itemName) {
    $excludeList = Get-Content -Path $excludeListFile
    return $excludeList -contains $itemName
}

# Function to copy items while excluding those in the exclude list
function CopyItems($sourcePath, $destinationPath) {
    try {
        $null = Get-ChildItem -Path $sourcePath -Force | ForEach-Object {
            if (-not (ShouldExcludeItem $_.Name)) {
                $targetPath = Join-Path -Path $destinationPath -ChildPath $_.Name
                if ($_.PsIsContainer) {
                    # If it's a folder, remove the existing destination folder and copy its contents
                    if (Test-Path -Path $targetPath -PathType Container) {
                        $null = Remove-Item -Path $targetPath -Recurse -Force -ErrorAction SilentlyContinue
                    }
                    $null = New-Item -Path $targetPath -ItemType Directory -Force
                    CopyItems -sourcePath $_.FullName -destinationPath $targetPath
                } else {
                    # If it's a file, copy it to the destination
                    $null = Copy-Item -Path $_.FullName -Destination $targetPath -Force -ErrorAction SilentlyContinue
                }
            }
        }
    } catch {
        Write-Host "Error: $_"
    }
}

# Remove the existing destination folder if it exists
try {
    if (Test-Path -Path $destinationFolder -PathType Container) {
        $null = Remove-Item -Path $destinationFolder -Recurse -Force -ErrorAction SilentlyContinue
    }
    CopyItems -sourcePath $sourceFolder -destinationPath $destinationFolder
    Write-Host "Task completed successfully."
} catch {
    Write-Host "Error: $_"
}
