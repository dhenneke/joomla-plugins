@echo off

if exist out rmdir /s /q out
mkdir out

tar.exe -a -c -f out\plg_sppagebuilder_ytgdpr.zip plg_sppagebuilder_ytgdpr
tar.exe -a -c -f out\plg_sppagebuilder_contactform.zip plg_sppagebuilder_contactform
