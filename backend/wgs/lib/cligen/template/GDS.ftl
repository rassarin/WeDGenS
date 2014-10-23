<#-- ステーション名を出力する場合はこちらを有効にする
${(stationId!'')?right_pad(5)}${(regionName!'')?right_pad(23)}${(stationName!'')?right_pad(23)}${(lat!'')?left_pad(5)}${''?right_pad(2)}${(lon!'')?left_pad(5)}${(alt!'')?left_pad(6)}
-->
${(stationId!'')?right_pad(5)}${' JAPAN'?right_pad(46)}${(lat!'')?left_pad(5)}${''?right_pad(2)}${(lon!'')?left_pad(5)}${(alt!'')?left_pad(6)}
<#list elementDataList as data>
${(data.year!'')?right_pad(2)}${(data.month!'')?right_pad(2)}${(data.day!'')?right_pad(2)}${(data.maxTemp!'')?left_pad(5)}${''?right_pad(2)}${(data.minTemp!'')?left_pad(5)}${''?right_pad(2)}${(data.prec!'')?left_pad(5)}
</#list>
