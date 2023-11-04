@echo off

if ["%~1"]==["json"] (
    copy /-y resources\Workspace.json Workspace.json
)
if ["%~1"]==[""] (
    copy /-y resources\Workspace.yaml Workspace.yaml
)

copy /-y resources\after.sh after.sh
copy /-y resources\aliases aliases

echo Workspace initialized!
