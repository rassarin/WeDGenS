  Yr Mn Dy DOY      Data
<#list elementDataList as data>
${(data.year!'')?right_pad(4)} ${(data.month!'')?right_pad(2)} ${(data.day!'')?right_pad(2)} ${(data.dayOfYear!'')?right_pad(3)} ${(data.value!'')?left_pad(9)}
</#list>
