@echo off

if ["%~1"]==["json"] (
    copy /-y resources\Devweb.json Devweb.json
)
if ["%~1"]==[""] (
    copy /-y resources\Devweb.yaml Devweb.yaml
)

copy /-y resources\after.sh after.sh
copy /-y resources\aliases aliases

echo Devweb initialized!
