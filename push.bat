@REM do a git add .
@REM git commit -m "message", ask for message
@REM git push


@echo off
set /p message="Enter commit message: "
git add .
git commit -m "%message%"
git push
```

