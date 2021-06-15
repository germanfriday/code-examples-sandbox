@echo off
echo %~dp0
cd %~dp0
set local=%~dp0
del masterslider.js
for /F %%a in (dev\files_batch.list) do (
	echo "%local%%%a"
	echo //%%a >> masterslider.js
	echo. >> masterslider.js
	type "%local%%%a" >> masterslider.js
)
