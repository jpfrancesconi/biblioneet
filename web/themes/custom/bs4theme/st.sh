#!/bin/bash
# Script to quickly create sub-theme.

export CUSTOM_BARRIO=bs4theme

for file in *bs4theme.*; do mv $file ${file//bootstrap_barrio_subtheme/$CUSTOM_BARRIO}; done
for file in config/*/*bs4theme.*; do mv $file ${file//bootstrap_barrio_subtheme/$CUSTOM_BARRIO}; done
mv {_,}$CUSTOM_BARRIO.theme
grep -Rl bs4theme .|xargs sed -i '' -e "s/bootstrap_barrio_subtheme/$CUSTOM_BARRIO/"
sed -i -e "s/Bootstrap Barrio Subtheme/$CUSTOM_BARRIO_NAME/" $CUSTOM_BARRIO.info.yml
echo "# Check the themes/custom folder for your new sub-theme."
