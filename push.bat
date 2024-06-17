@echo off
set /p message="push : 1 / pull : 2 /remote : 3 /init : 4 :"
if %message% == 1 (
    set /p message="Enter commit message: "
    git add .
    git commit -m "%message%"
    git push
) else if %message% == 2 (
    git pull
) else if %message% == 3 (
    set /p url="Enter remote url: "
    git remote add origin %url%
) else if %message% == 4 (
    git init
    set /p url="Enter remote url: "
    git remote add origin %url%
)
```

